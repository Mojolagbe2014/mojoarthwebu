<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$dataObj = new Data($dbObj); // Create an object of Data class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_POST, "addNewData") != NULL){
    $postVars = array('clientId','bedId','patientId','createdAt','status'); 
    foreach ($postVars as $postVar){
        switch($postVar){
            default     :   $dataObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($dataObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   { 
        //header('Content-type: application/json');
        echo $dataObj->add(); 
        
    }
    else{ 
        $json = array("status" => 0, "msg" => $errorArr); 
        $dbObj->close();//Close Database Connection
        header('Content-type: application/json');
        echo json_encode($json);
    }
} 