<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$sponsorObj = new Sponsor($dbObj); // Create an object of Sponsor class
$errorArr = array(); //Array of errors
$sponsorLogo =""; $sponsorImage ="";  

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "addNewSponsor") != NULL && filter_input(INPUT_POST, "addNewSponsor")=="addNewSponsor"){
        $postVars = array('name', 'logo', 'website', 'product', 'description', 'image'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'logo':   $sponsorObj->$postVar = basename($_FILES["logo"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["logo"]["name"]),PATHINFO_EXTENSION): ""; 
                                $sponsorLogo = $sponsorObj->$postVar;
                                if($sponsorObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                case 'image':   $sponsorObj->$postVar = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $sponsorImage = $sponsorObj->$postVar;
                                if($sponsorObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $sponsorObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($sponsorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $targetLogo = MEDIA_FILES_PATH."sponsor/". $sponsorLogo;
            $targetImage = MEDIA_FILES_PATH."sponsor-image/". $sponsorImage;
            $uploadOk = 1; $msg = '';
            $imageFileType = pathinfo($targetLogo, PATHINFO_EXTENSION);
            if (file_exists($targetLogo)) { $msg .= " Sponsor logo already exists."; $uploadOk = 0; }
            if ($_FILES["logo"]["size"] > 80000000) { $msg .= " Sponsor logo is too large."; $uploadOk = 0; }
            if ($uploadOk == 0) {
                $msg = "Sorry, your sponsor logo was not uploaded. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                header('Content-type: application/json');
                echo json_encode($json);
            } 
            else {
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetLogo) && move_uploaded_file($_FILES["image"]["tmp_name"], $targetImage)) {
                    $msg .= "The logo has been uploaded.";
                    $status = 'ok';
                    echo $sponsorObj->add();
                } else {
                    $msg = " Sorry, there was an error uploading your sponsor logo. ERROR: ".$msg;
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
    
    if(filter_input(INPUT_POST, "fetchSponsors") != NULL){
        $requestData= $_REQUEST;
        $columns = array( 0 =>'id', 1 =>'id', 2 => 'status', 3 => 'name', 4 => 'logo', 5 => 'website', 6 => 'date_added', 7 => 'product', 8 =>'description', 9 =>'image');

        // getting total number records without any search
        $query = $dbObj->query("SELECT * FROM sponsor ");
        $totalData = mysqli_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT * FROM sponsor WHERE 1=1 ";
        if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
                $sql.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";    
                $sql.=" OR logo LIKE '%".$requestData['search']['value']."%' ";
                $sql.=" OR product LIKE '%".$requestData['search']['value']."%' ";
                $sql.=" OR description LIKE '%".$requestData['search']['value']."%' ";
                $sql.=" OR website LIKE '%".$requestData['search']['value']."%' ";
                $sql.=" OR date_added LIKE '%".$requestData['search']['value']."%' ) ";
        }
        $query = $dbObj->query($sql);
        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

        echo $sponsorObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
    }
    
    if(filter_input(INPUT_POST, "deleteThisSponsor")!=NULL){
        $postVars = array('id', 'logo', 'image'); // Form fields names
        $sponsorLogo =""; $sponsorImage ="";  
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'logo': $sponsorObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                $sponsorLogo = $sponsorObj->$postVar;
                                if($sponsorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                case 'image': $sponsorObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                $sponsorImage = $sponsorObj->$postVar;
                                if($sponsorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $sponsorObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($sponsorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $logoDelParam = true;
            if(file_exists(MEDIA_FILES_PATH."sponsor/".$sponsorLogo) && $sponsorLogo!=''){
                if(unlink(MEDIA_FILES_PATH."sponsor/".$sponsorLogo)){ $logoDelParam = true;}
                else { $logoDelParam = false; }
            }
            if(file_exists(MEDIA_FILES_PATH."sponsor-image/".$sponsorImage) && $sponsorImage!=''){
                if(unlink(MEDIA_FILES_PATH."sponsor-image/".$sponsorImage)){ $logoDelParam = true;}
                else { $logoDelParam = false; }
            }
            if($logoDelParam == true){ echo $sponsorObj->delete(); }
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
    
    if(filter_input(INPUT_POST, "addNewSponsor") != NULL && filter_input(INPUT_POST, "addNewSponsor")=="editSponsor"){
        $postVars = array('id', 'name', 'logo', 'website', 'product', 'description', 'image'); // Form fields names 
        $newLogo =""; $oldLogo = $_REQUEST['oldLogo']; $newImage = ""; $oldImage = $_REQUEST['oldImage'];
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'logo':   $newLogo = basename($_FILES["logo"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["logo"]["name"]),PATHINFO_EXTENSION): ""; 
                                $sponsorObj->$postVar = $newLogo;
                                if($sponsorObj->$postVar == "") { $sponsorObj->$postVar = $oldLogo;}
                                break;
                case 'image':   $newImage = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $sponsorObj->$postVar = $newImage;
                                if($sponsorObj->$postVar == "") { $sponsorObj->$postVar = $oldImage;}
                                break;
                default     :   $sponsorObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($sponsorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $targetLogo = MEDIA_FILES_PATH."sponsor/". $newLogo;
            $targetImage = MEDIA_FILES_PATH."sponsor-image/". $newImage;
            $uploadOk = 1; $msg = '';
            if($newLogo !=""){
                if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetLogo)) {
                    $msg .= "The file ". basename( $_FILES["logo"]["name"]). " has been uploaded.";
                    $status = 'ok'; if(file_exists(MEDIA_FILES_PATH."sponsor/".$oldLogo)) unlink(MEDIA_FILES_PATH."sponsor/".$oldLogo); $uploadOk = 1;
                } else { $uploadOk = 0; }
            }
            if($newImage !=""){
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetImage)) {
                    $msg .= "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
                    $status = 'ok'; if(file_exists(MEDIA_FILES_PATH."sponsor-image/".$oldImage)) unlink(MEDIA_FILES_PATH."sponsor-image/".$oldImage); $uploadOk = 1;
                } else { $uploadOk = 0; }
            }
            if($uploadOk == 1){
                echo $sponsorObj->update();
            }
            else {
                    $msg = " Sorry, there was an error uploading your sponsor logo. ERROR: ".$msg;
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

    if(filter_input(INPUT_GET, "activateSponsor")!=NULL){
        $postVars = array('id', 'status'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'status':  $sponsorObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar, FILTER_VALIDATE_INT)) :  0; 
                                if($sponsorObj->$postVar == 1) {$sponsorObj->$postVar = 0;} 
                                elseif($sponsorObj->$postVar == 0) {$sponsorObj->$postVar = 1;}
                                break;
                default     :   $sponsorObj->$postVar = filter_input(INPUT_GET, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, $postVar)) :  ''; 
                                if($sponsorObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            echo Sponsor::updateSingle($dbObj, ' status ',  $sponsorObj->status, $sponsorObj->id); 
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