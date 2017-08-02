<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj);
$errorArr = array(); //Array of errors

$message = filter_input(INPUT_POST, 'message') ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'message')) :  ''; 
if($message == "") {array_push ($errorArr, " Please enter message ");}

if(!isset($_SESSION['ITCLoggedInUser']) || $_REQUEST['LoggedInUserId'] != $_SESSION['ITCuserId']){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    if(count($errorArr) < 1)   {
        $userObj->id = $_SESSION['ITCuserId'] ? $_SESSION['ITCuserId'] :  0;
        $userObj->clientId = User::getSingle($dbObj, 'client_id', $userObj->id);
        $userObj->email = User::getSingle($dbObj, 'email', $userObj->id);
        $userObj->name = User::getSingle($dbObj, 'name', $userObj->id);
        
        $emailAddress = Client::getSingle($dbObj, "email", $userObj->clientId);
        	
        $message = "<strong>From:</strong> $userObj->name <br/><br/> <div><strong>Message:</strong> <p>$message</p></div>";
        $headers = 'From: '. $userObj->name . '<' . $userObj->email . '>' . "\r\n";
        $headers .= 'Reply-To: ' . $userObj->email . "\r\n";
        $headers .= 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        mail($emailAddress, "NOTIFICATION MESSAGE", $message, $headers);
        $json = array("status" => 1, "msg" => "Your Notification Has Been Sent"); 
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
}