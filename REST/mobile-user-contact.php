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
    
    $emailAddress = 'info@arth.ai';
    if(empty($subject)) $subject = "Message From: $name";	
    $message = "<strong>From:</strong> $name <br/><br/> <div><strong>Message:</strong> <p>$message</p></div>";
    $headers = 'From: '. $name . '<' . $email . '>' . "\r\n";
    $headers .= 'Reply-To: ' . $email . "\r\n";
    $headers .= 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
    mail($emailAddress, $subject, $message, $headers);
    $json = array("status" => 1, "msg" => "Message Successfully Sent"); 
    $dbObj->close();//Close Database Connection
    if(array_key_exists('callback', $_GET)){
        header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        echo $_GET['callback'].'('.json_encode($json).');';
    }else{ header('Content-Type: application/json'); echo json_encode($json); }
    
}else{ 
    $json = array("status" => 0, "msg" => $errorArr); 
    $dbObj->close();//Close Database Connection
    if(array_key_exists('callback', $_GET)){
        header('Content-Type: text/javascript'); header('Access-Control-Allow-Origin: *'); header('Access-Control-Max-Age: 3628800'); header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        echo $_GET['callback'].'('.json_encode($json).');';
    }else{ header('Content-Type: application/json'); echo json_encode($json); }
}