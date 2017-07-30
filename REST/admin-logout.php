<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$adminObj = new Admin($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors

session_destroy();
$json = array("status" => 1, "msg" => "Logout successful."); 
$dbObj->close();//Close Database Connection
header('Content-type: application/json');
echo json_encode($json);
