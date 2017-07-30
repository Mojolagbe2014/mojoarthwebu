<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$coursBrochObj = new CourseBrochure($dbObj); // Create an object of CourseBrochure class
$errorArr = array(); //Array of errors
$oldDocument = ""; $newDocument =""; $coursBrochDoc ="";

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "addNewBrochure") != NULL && filter_input(INPUT_POST, "addNewBrochure")=="addNewBrochure"){
        $postVars = array('name', 'document'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'document':   $coursBrochObj->$postVar = basename($_FILES["document"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["document"]["name"]),PATHINFO_EXTENSION): ""; 
                                $coursBrochDoc = $coursBrochObj->$postVar;
                                if($coursBrochObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $coursBrochObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($coursBrochObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $target_file = MEDIA_FILES_PATH."brochure/". $coursBrochDoc;
            $uploadOk = 1; $msg = '';
            $docFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            if ($coursBrochDoc!='' && file_exists($target_file)) { $msg .= " Course brochure document already exists."; $uploadOk = 0; }
            if ($_FILES["document"]["size"] > 800000000) { $msg .= " Course brochure document is too large."; $uploadOk = 0; }
            if ($docFileType!='doc' && $docFileType!='docx' && $docFileType!='pdf' && $docFileType!='xls' && $docFileType!='csv') { $msg .= "Brochure must be in either of these formats: PDF, DOC, DOCX, XLS, CSV."; $uploadOk = 0; }
            if ($uploadOk == 0) {
                $msg = "Sorry, your course brochure document was not uploaded. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                header('Content-type: application/json');
                echo json_encode($json);
            } 
            else {
                if (move_uploaded_file($_FILES["document"]["tmp_name"], MEDIA_FILES_PATH."/brochure/".$coursBrochDoc)) {
                    $msg .= "The file ". basename( $_FILES["document"]["name"]). " has been uploaded.";
                    $status = 'ok';
                    echo $coursBrochObj->add();
                } else {
                    $msg = " Sorry, there was an error uploading your course brochure document. ERROR: ".$msg;
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
    
    if(filter_input(INPUT_POST, "fetchBrochures") != NULL){
        $requestData= $_REQUEST;
        $columns = array( 0 =>'id', 1 =>'id', 2 => 'name', 3 => 'document');

        // getting total number records without any search
        $query = $dbObj->query("SELECT * FROM course_brochure ");
        $totalData = mysqli_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT * FROM course_brochure WHERE 1=1 ";
        if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
                $sql.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";    
                $sql.=" OR document LIKE '".$requestData['search']['value']."%' ) ";
        }
        $query = $dbObj->query($sql);
        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

        echo $coursBrochObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
    }
    
    if(filter_input(INPUT_POST, "deleteThisBrochure")!=NULL){
        $postVars = array('id','document'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'document':   $coursBrochObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                $coursBrochDoc = $coursBrochObj->$postVar;
                                if($coursBrochObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $coursBrochObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($coursBrochObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   {
            if($coursBrochDoc!='' && file_exists(MEDIA_FILES_PATH."brochure/".$coursBrochDoc)){ unlink(MEDIA_FILES_PATH."brochure/".$coursBrochDoc); }
            echo $coursBrochObj->delete();
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    } 
    
    if(filter_input(INPUT_POST, "addNewBrochure") != NULL && filter_input(INPUT_POST, "addNewBrochure")=="editCourseBrochure"){
        $postVars = array('id', 'name', 'document'); // Form fields names
        $oldDocument = $_REQUEST['oldFile'];
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'document':   $newDocument = basename($_FILES["document"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["document"]["name"]),PATHINFO_EXTENSION): ""; 
                                $coursBrochObj->$postVar = $newDocument;
                                if($coursBrochObj->$postVar == "") { $coursBrochObj->$postVar = $oldDocument;}
                                $coursBrochDoc = $newDocument;
                                break;
                default     :   $coursBrochObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($coursBrochObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   {
            $target_file = MEDIA_FILES_PATH."brochure/". $coursBrochDoc;
            $uploadOk = 1; $msg = '';
            $docFileType = pathinfo($target_file,PATHINFO_EXTENSION);
            if ($newDocument !="" && $docFileType!='doc' && $docFileType!='docx' && $docFileType!='pdf' && $docFileType!='xls' && $docFileType!='csv') { $msg .= "Brochure must be in either of these formats: PDF, DOC, DOCX, XLS, CSV."; $uploadOk = 0; }
            if($uploadOk == 1){
                if($newDocument !=""){
                    move_uploaded_file($_FILES["document"]["tmp_name"], $target_file);
                    if ($oldDocument!='' && file_exists(MEDIA_FILES_PATH."brochure/".$oldDocument)) {  unlink(MEDIA_FILES_PATH."brochure/".$oldDocument); } 
                }
                echo $coursBrochObj->update();
            }else{
                $msg = "Sorry, your course brochure document was not uploaded. ERROR: ".$msg;
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
}