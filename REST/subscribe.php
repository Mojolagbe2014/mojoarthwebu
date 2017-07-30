<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors
$msg = ''; $msgStatus = '';

if(isset($_POST['subscriberSubmit'])){
    $userObj->email = filter_input(INPUT_POST, 'subscriberEmail', FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'subscriberEmail', FILTER_VALIDATE_EMAIL)) :  ''; 
    if($userObj->email == "") {array_push ($errorArr, "valid email ");}
    $userObj->name = filter_input(INPUT_POST, 'subscriberName') ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'subscriberName')) :  ''; 
    if($userObj->name == "") {array_push ($errorArr, " name ");}
    $userObj->company = filter_input(INPUT_POST, 'subscriberCompany') ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'subscriberCompany')) :  ''; 
    //if($userObj->company == "") {array_push ($errorArr, " company ");}
   
    if(count($errorArr) < 1)   {
        if($userObj->emailExists()){
            $msgStatus = 'error';
            $msg ='<h3>Subscription failed!</h3><p>REASON: You have already subscribed to our site.</p>';
        }
        else{
            $msgStatus = $userObj->addRaw();
            $msg = ($msgStatus == 'success') ? '<h3>SUCCESS</h3><p>You have successfully subscribed to our site.</p>' : '<h3>ERROR</h3><p>Website subscription failed.</p>';
        }
    }else{ 
        $msgStatus = 'error'; $msg = $thisPage->showError($errorArr); 
    }
    $_SESSION['msgStatus'] = $msgStatus;
    $_SESSION['msg'] = $msg;
    $thisPage->redirectTo($_SERVER['HTTP_REFERER']);
}