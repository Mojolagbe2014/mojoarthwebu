<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$sliderObj = new Slider($dbObj); // Create an object of Slider class
$errorArr = array(); //Array of errors
$sliderImgFil ="";
if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "addNewSlider") != NULL){
        $postVars = array('title', 'content', 'image', 'orders'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image':   $sliderObj->$postVar = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_".  StringManipulator::trimStringToFullWord(30, StringManipulator::slugify(filter_input(INPUT_POST, 'title'))).".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $sliderImgFil = $sliderObj->$postVar;
                                if($sliderObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $sliderObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($sliderObj->$postVar == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            $targetFile = MEDIA_FILES_PATH."slider/". $sliderImgFil;
            $uploadOk = 1; $msg = '';
            $imageFileType = pathinfo($targetFile, PATHINFO_EXTENSION);
            if (file_exists($targetFile)) { $msg .= " Slider image already exists."; $uploadOk = 0; }
            if ($_FILES["image"]["size"] > 80000000) { $msg .= " Slider image is too large."; $uploadOk = 0; }
            if ($imageFileType != "jpg" && $imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "gif"  && $imageFileType != "bmp") { $msg .= "Sorry, only image files are allowed."; $uploadOk = 0; }
            if ($uploadOk == 0) {
                $msg = "Sorry, your slider image was not uploaded. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                header('Content-type: application/json');
                echo json_encode($json);
            } 
            else {
                if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                    $msg .= "The image has been uploaded.";
                    $status = 'ok';
                    echo $sliderObj->add();
                } else {
                    $msg = " Sorry, there was an error uploading your slider image. ERROR: ".$msg;
                    $json = array("status" => 0, "msg" => $msg); 
                    $dbObj->close();//Close Database Connection
                    header('Content-type: application/json');
                    echo json_encode($json);
                }
            }

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