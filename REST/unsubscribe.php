<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors
$msg = ''; $msgStatus = '';

if(isset($_GET['id']) && isset($_GET['email'])){
    $userObj->email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL)) :  ''; 
    if($userObj->email == "") {array_push ($errorArr, "valid email ");}
    $userObj->id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)) :  ''; 
    if($userObj->id == "") {array_push ($errorArr, " id ");}
   
    if(count($errorArr) < 1)   {
        if(!$userObj->emailExists()){
            $msgStatus = 'error';
            $msg ='<h3>Subscription Removal Failed!</h3><p>REASON: Your email does not exist in our database.</p>';
        }
        else{
            $msgStatus = $userObj->deleteRaw();
            $msg = ($msgStatus == 'success') ? '<h3>SUCCESS</h3><p>You have successfully unsubscribed from our site.</p>' : '<h3>ERROR</h3><p>Subscription removal failed.</p>';
        }
    }else{ 
        $msgStatus = 'error'; $msg = $thisPage->showError($errorArr); 
    }
    $_SESSION['msgStatus'] = $msgStatus;
    $_SESSION['msg'] = $msg;
    $thisPage->redirectTo(SITE_URL);
}