<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of User class
$errorArr = array(); //Array of errors
$oldPicture = ""; $newPicture = ""; $pictureFil = "";

if(!isset($_SESSION['ITCLoggedInUser']) || $_REQUEST['LoggedInUserId'] != $_SESSION['ITCuserId']){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "update") != NULL){
        $postVars = array('id', 'name','address','username','picture'); // Form fields names
        $oldPicture = $_REQUEST['oldPicture'];
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'id'   :   $userObj->$postVar = filter_input(INPUT_POST, 'LoggedInUserId', FILTER_VALIDATE_INT) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'LoggedInUserId', FILTER_VALIDATE_INT)) :  ''; 
                                if($userObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                case 'picture':   $newPicture = basename($_FILES["picture"]["name"]) ? time().".".pathinfo(basename($_FILES["picture"]["name"]),PATHINFO_EXTENSION): ""; 
                                $userObj->$postVar = $newPicture;
                                if($userObj->$postVar == "") { $userObj->$postVar = $oldPicture;}
                                $pictureFil = $newPicture;
                                break;
                default     :   $userObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($userObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        
        if(count($errorArr) < 1)   {
            
            $targetPicture = MEDIA_FILES_PATH."user/". $pictureFil;
            $docFileType = pathinfo($targetPicture,PATHINFO_EXTENSION);
            
            $msg = ''; $allowedExts = array('jpg', 'png', 'gif', 'bmp', 'jpeg', 'JPG', 'GIF', 'PNG', 'JPEG');
            $uploadOk = in_array($docFileType, $allowedExts) ? 1 : ($newPicture =="") ? 1 : 0;
            
            
            if($newPicture !="" && $uploadOk ==1){
                move_uploaded_file($_FILES["picture"]["tmp_name"], $targetPicture);
                $msg .= "The picture ". basename( $_FILES["picture"]["name"]). " has been uploaded.";
                if($oldPicture!='' && file_exists(MEDIA_FILES_PATH."user/".$oldPicture))unlink(MEDIA_FILES_PATH."user/".$oldPicture);
            }
            
            if($uploadOk == 1){ echo $userObj->update(); }
            else {
                $msg = " Sorry, there was an error uploading your picture. <br/> ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                if(array_key_exists('callback', $_GET)){
                    header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                    echo $_GET['callback'].'('.json_encode($json).');';
                }else{ header('Content-Type: application/json'); echo json_encode($json); }
            }
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            if(array_key_exists('callback', $_GET)){
                header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                echo $_GET['callback'].'('.json_encode($json).');';
            }else{ header('Content-Type: application/json'); echo json_encode($json); }
        }
    } 
}