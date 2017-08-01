<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of User class
$errorArr = array(); //Array of errors

session_destroy();
$json = array("status" => 1, "msg" => "Logout successful."); 
$dbObj->close();//Close Database Connection
if(array_key_exists('callback', $_GET)){
    header('Content-Type: text/javascript');  header('Access-Control-Allow-Origin: *');  header('Access-Control-Max-Age: 3628800');  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    echo $_GET['callback'].'('.json_encode($json).');';
}else{ 
    header('Content-Type: application/json'); 
    echo json_encode($json); 
} 
