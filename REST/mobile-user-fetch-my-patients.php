<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of User class
$patientObj = new Patient($dbObj);
$errorArr = array(); //Array of errors
$newPassword ="";

if(!isset($_SESSION['ITCLoggedInUser']) || $_REQUEST['LoggedInUserId'] != $_SESSION['ITCuserId']){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    //Get all needed parameters
    $userObj->id = $_SESSION['ITCuserId'] ? $_SESSION['ITCuserId'] :  0;
    $userObj->clientId = User::getSingle($dbObj, 'client_id', $userObj->id);
    $userObj->canAccess = User::getSingle($dbObj, 'can_access', $userObj->id);
    echo $patientObj->fetchSpecial("*", " client_id = $userObj->clientId AND bed_id = $userObj->canAccess AND status = 1 "); 
}