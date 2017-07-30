<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$newsObj = new News($dbObj); // Create an object of News class
$errorArr = array(); //Array of errors
$newsImage ="";  $newImage = ""; 

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "addNewNews") != NULL && filter_input(INPUT_POST, "addNewNews")=="addNewNews"){
        $postVars = array('title', 'description', 'image'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image':   $newsObj->$postVar = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_". utf8_encode(strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'title')))).".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $newsImage = $newsObj->$postVar;
                                if($newsObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $newsObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($newsObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $targetImage = MEDIA_FILES_PATH."news/". $newsImage;
            $uploadOk = 1; $msg = '';
            //$imageFileType = pathinfo($targetLogo, PATHINFO_EXTENSION);
            if ($newsImage!='' && file_exists($targetImage)) { $msg .= " News image already exists."; $uploadOk = 0; }
            if ($_FILES["image"]["size"] > 80000000) { $msg .= " News image is too large."; $uploadOk = 0; }
            if ($uploadOk == 0) {
                $msg = "Sorry, your news logo was not uploaded. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                header('Content-type: application/json');
                echo json_encode($json);
            } 
            else {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetImage)) {
                    $msg .= "The image has been uploaded.";
                    $status = 'ok';
                    echo $newsObj->add();
                } else {
                    $msg = " Sorry, there was an error uploading your news image. ERROR: ".$msg;
                    $json = array("status" => 0, "msg" => $msg); 
                    $dbObj->close();//Close Database Connection
                    header('Content-type: application/json');
                    echo json_encode($json);
                }
            }
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
    } 
    
    if(filter_input(INPUT_POST, "fetchNews") != NULL){
        $requestData= $_REQUEST;
        $columns = array( 0 =>'id', 1 =>'id', 2 => 'status', 3 => 'title', 4 => 'image', 5 => 'description', 6 =>'date_added');

        // getting total number records without any search
        $query = $dbObj->query("SELECT * FROM news ");
        $totalData = mysqli_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT * FROM news WHERE 1=1 ";
        if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
                $sql.=" AND ( title LIKE '%".$requestData['search']['value']."%' ";
                $sql.=" OR description LIKE '%".$requestData['search']['value']."%' ";
                $sql.=" OR date_added LIKE '%".$requestData['search']['value']."%' ) ";
        }
        $query = $dbObj->query($sql);
        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

        echo $newsObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
    }
    
    if(filter_input(INPUT_POST, "deleteThisNews")!=NULL){
        $postVars = array('id', 'image'); // Form fields names
        $newsImage ="";  
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image': $newsObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                $newsImage = $newsObj->$postVar;
                                if($newsObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $newsObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($newsObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $logoDelParam = true;
            if(file_exists(MEDIA_FILES_PATH."news/".$newsImage) && !empty($newsImage)){
                if(unlink(MEDIA_FILES_PATH."news/".$newsImage)){ $logoDelParam = true;}
                else { $logoDelParam = false; }
            }
            if($logoDelParam == true){ echo $newsObj->delete(); }
            else{ 
                $json = array("status" => 0, "msg" => $errorArr); 
                $dbObj->close();//Close Database Connection
                header('Content-type: application/json');
                echo json_encode($json);
            }
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    } 
    
    if(filter_input(INPUT_POST, "addNewNews") != NULL && filter_input(INPUT_POST, "addNewNews")=="editNews"){
        $postVars = array('id', 'title', 'description', 'image'); // Form fields names 
        $oldImage = $_REQUEST['oldImage'];
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image':   $newsObj->$postVar = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_". utf8_encode(strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'title')))).".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $newImage = $newsObj->$postVar;
                                if($newsObj->$postVar == "") { $newsObj->$postVar = $oldImage;}
                                break;
                default     :   $newsObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($newsObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $targetImage = MEDIA_FILES_PATH."news/". $newImage;
            $uploadOk = 1; $msg = '';
            if($newImage !=""){
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetImage)) {
                    $msg .= "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
                    $status = 'ok'; if(file_exists(MEDIA_FILES_PATH."news/".$oldImage)) unlink(MEDIA_FILES_PATH."news/".$oldImage); $uploadOk = 1;
                } else { $uploadOk = 0; }
            }
            if($uploadOk == 1){ echo $newsObj->update(); }
            else {
                    $msg = " Sorry, there was an error uploading your news logo. ERROR: ".$msg;
                    $json = array("status" => 0, "msg" => $msg); 
                    $dbObj->close();//Close Database Connection
                    header('Content-type: application/json');
                    echo json_encode($json);
            }
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
    } 

    if(filter_input(INPUT_GET, "activateNews")!=NULL){
        $postVars = array('id', 'status'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'status':  $newsObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_INT)) :  0; 
                                if($newsObj->$postVar == 1) {$newsObj->$postVar = 0;} 
                                elseif($newsObj->$postVar == 0) {$newsObj->$postVar = 1;}
                                break;
                default     :   $newsObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                                if($newsObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            echo News::updateSingle($dbObj, ' status ',  $newsObj->status, $newsObj->id); 
        }
        //Else show error messages
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    }
}