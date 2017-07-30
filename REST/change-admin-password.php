<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$adminObj = new Admin($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors
$newPassword ="";
if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "changeThisPassword")!=NULL){
        $postVars = array('oldPassword', 'newPassword', 'confirmPassword'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'confirmPassword':    if(filter_input(INPUT_POST,$postVar) !== filter_input(INPUT_POST, "newPassword")){
                                array_push ($errorArr, "Password Mismatch !!! ");
                                if(filter_input(INPUT_POST, $postVar) == "") {array_push ($errorArr, "Please confirm your password. ");}}
                                break;
                case 'oldPassword'     :   $adminObj->passWord = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($adminObj->passWord == "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default:        if(filter_input(INPUT_POST, $postVar) == "") {array_push ($errorArr, "Please enter $postVar. ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            //$adminObj->passWord = 'testing';//mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'oldPassword'));
            $adminObj->id = $_SESSION['ITCadminId'];
            $newPassword =  mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'newPassword'));
            echo  $adminObj->changePassword($newPassword);
        }
        //Else show error messages
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    } 
}