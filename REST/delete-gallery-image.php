<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    $basedir = '../media/gallery/';
    $fileToDelete = $_REQUEST["image"];  

    if(!empty($fileToDelete))
        unlink($basedir.$fileToDelete);

    $json = array("status" => 1, "msg" => $fileToDelete." successfully deleted"); 
    header('Content-type: application/json');
    echo json_encode($json);
}