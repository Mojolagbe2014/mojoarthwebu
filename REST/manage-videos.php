<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$videoObj = new Video($dbObj); // Create an object of Video class
$errorArr = array(); //Array of errors
$oldMedia = ""; $newMedia =""; $videoMedFil ="";

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "addNewVideo") != NULL && filter_input(INPUT_POST, "addNewVideo")=="addNewVideo"){
        $postVars = array('name', 'description', 'video'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'video':   $videoObj->$postVar = basename($_FILES["video"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["video"]["name"]),PATHINFO_EXTENSION): ""; 
                                $videoMedFil = $videoObj->$postVar;
                                if($videoObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $videoObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($videoObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   {
            $targetVideo = MEDIA_FILES_PATH."video/". $videoMedFil;
            $uploadOk = 1; $msg = '';
            $videoFileType = pathinfo($targetVideo,PATHINFO_EXTENSION);
            // Check if file already exists
            if (file_exists($targetVideo)) { $msg .= " Video already exists."; $uploadOk = 0; }
            // Check file size and 
            if ($_FILES["video"]["size"] > 800000000) { $msg .= " Video is too large."; $uploadOk = 0; }
            if($videoFileType != 'mp4'){ $msg .= " Video is not an MP4 file. Please upload .mp4 files only."; $uploadOk = 0;}
            if ($uploadOk == 0) {
                $msg = "Sorry, your video was not uploaded. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                header('Content-type: application/json');
                echo json_encode($json);
            } 
            else {
                if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetVideo)) {
                    $msg .= "The file ". basename( $_FILES["video"]["name"]). " has been uploaded.";
                    $status = 'ok';
                    echo $videoObj->add();
                } else {
                    $msg = " Sorry, there was an error uploading your video. ERROR: ".$msg;
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
    
    if(filter_input(INPUT_POST, "fetchVideos") != NULL){
        $requestData= $_REQUEST;
        $columns = array( 0 =>'id', 1 =>'id', 2 => 'name', 3 => 'description', 4 => 'video');

        // getting total number records without any search
        $query = $dbObj->query("SELECT * FROM video ");
        $totalData = mysqli_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT * FROM video WHERE 1=1 ";
        if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
                $sql.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";    
                $sql.=" OR description LIKE '".$requestData['search']['value']."%' ";
                $sql.=" OR video LIKE '".$requestData['search']['value']."%' ) ";
        }
        $query = $dbObj->query($sql);
        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

        echo $videoObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
    }
    
    if(filter_input(INPUT_POST, "deleteThisVideo")!=NULL){
        $postVars = array('id','video'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'video':   $videoObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                $videoMedFil = $videoObj->$postVar;
                                if($videoObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $videoObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($videoObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   {
            if($videoMedFil!='' && file_exists(MEDIA_FILES_PATH."video/".$videoMedFil)){ unlink(MEDIA_FILES_PATH."video/".$videoMedFil); echo $videoObj->delete(); }
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
    
    if(filter_input(INPUT_POST, "addNewVideo") != NULL && filter_input(INPUT_POST, "addNewVideo")=="editVideo"){
        $postVars = array('id', 'name', 'description', 'video'); // Form fields names
        $oldMedia = $_REQUEST['oldFile'];
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'video':   $newMedia = basename($_FILES["video"]["name"]) ? rand(100000, 1000000)."_".  strtolower(str_replace(" ", "_", filter_input(INPUT_POST, 'name'))).".".pathinfo(basename($_FILES["video"]["name"]),PATHINFO_EXTENSION): ""; 
                                $videoObj->$postVar = $newMedia;
                                if($videoObj->$postVar == "") { $videoObj->$postVar = $oldMedia;}
                                $videoMedFil = $newMedia;
                                break;
                default     :   $videoObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($videoObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $targetVideo = MEDIA_FILES_PATH."video/". $videoMedFil;
            $uploadOk = 1; $msg = '';
            $videoFileType = pathinfo($targetVideo,PATHINFO_EXTENSION);
            if($videoFileType != 'mp4' && $videoFileType != ''){ $msg .= " Video is not an MP4 file. Please upload .mp4 files only."; $uploadOk = 0;}
            if($uploadOk == 1){
                if($newMedia !=""){
                    if (move_uploaded_file($_FILES["video"]["tmp_name"], $targetVideo)) {
                        $msg .= "The file ". basename( $_FILES["video"]["name"]). " has been uploaded.";
                        $status = 'ok';
                        if(file_exists(MEDIA_FILES_PATH."video/".$oldMedia) && $oldMedia!='') unlink(MEDIA_FILES_PATH."video/".$oldMedia);
                        echo $videoObj->update();
                    } else {
                        $msg = " Sorry, there was an error uploading your video media. ERROR: ".$msg;
                        $json = array("status" => 0, "msg" => $msg); 
                        $dbObj->close();//Close Database Connection
                        header('Content-type: application/json');
                        echo json_encode($json);
                    }
                } 
                else{ echo $videoObj->update(); }
            }else {
                $msg = " Sorry, there was an error uploading your video media. ERROR: ".$msg;
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