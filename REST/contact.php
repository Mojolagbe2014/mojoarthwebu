<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$errorArr = array(); //Array of errors

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) :  ''; 
if($email == "") {array_push ($errorArr, "valid email ");}
$name = filter_input(INPUT_POST, 'name') ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'name')) :  ''; 
if($name == "") {array_push ($errorArr, " name ");}
$message = filter_input(INPUT_POST, 'message') ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'message')) :  ''; 
if($message == "") {array_push ($errorArr, " message ");}
$subject = filter_input(INPUT_POST, 'subject') ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'subject')) :  ''; 

if(count($errorArr) < 1)   {
    // email address starts
    $emailAddress = 'info@iadet.net';//iadet910@iadet.net
    if(empty($subject)) $subject = "Message From: $name";	
    $message = "<strong>From:</strong> $name <br/><br/> <strong>Message:</strong> $message";
    $headers = 'From: '. $name . '<' . $email . '>' . "\r\n";
    $headers .= 'Reply-To: ' . $email . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    mail($emailAddress, $subject, $message, $headers);
    $json = array("status" => 1, "msg" => "Message Successfully Sent"); 
    $dbObj->close();//Close Database Connection
    header('Content-type: application/json');
    echo json_encode($json);
    
}else{ 
    $json = array("status" => 0, "msg" => $errorArr); 
    $dbObj->close();//Close Database Connection
    header('Content-type: application/json');
    echo json_encode($json);
}