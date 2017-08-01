<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
require_once '../swiftmailer/lib/swift_required.php';

$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of User class

$errorArr = array(); //Array of errors
$thisEmail = ""; 

if(filter_input(INPUT_POST, "email")!=NULL){
    $postVars = array('email'); // Form fields names
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'email':   $thisEmail = filter_input(INPUT_POST, $postVar, FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar, FILTER_VALIDATE_EMAIL)) :  ''; 
                            if($thisEmail == "") {array_push ($errorArr, "Please enter valid email ");}
                            break;
        }
    }
    if(count($errorArr) < 1)   {
        $userObj->email = $thisEmail;
        $userObj->password = 'rst'.sha1(time());
        
        // Create the mail transport configuration
        $transport = Swift_MailTransport::newInstance();

        // Create the message
        $message = Swift_Message::newInstance();
        $message->setTo(array($thisEmail => "ARTH App User" ));
        $message->setSubject("Password Reset Message");
        $message->setBody("Your password has been reset. <br/> Email: $thisEmail <br/> New Password: $userObj->password");
        $message->setFrom("noreply@arth.ai", "ARTH App Automated Message");

        // Send the email
        $mailer = Swift_Mailer::newInstance($transport);
        //if($userObj->emailExists()) $mailer->send($message);
        
        echo  $userObj->resetPassword();
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