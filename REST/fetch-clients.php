<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$clientObj = new Client($dbObj); // Create an object of Course class
$errorArr = array(); //Array of errors

$userObj = new User($dbObj);
$dataObj = new Data($dbObj);
$camObj = new Camera($dbObj);
$bedObj = new Bed($dbObj);
$patientObj = new Patient($dbObj);

//fetch all courses
//header('Content-type: application/json');
echo $userObj->fetch("*", "  email = 'mojolagbe@gmail.com' ", " id LIMIT 1");