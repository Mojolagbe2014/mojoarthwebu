<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of User class
$errorArr = array(); //Array of errors

if(filter_input(INPUT_POST, "email")!=NULL){
    $postVars = array('email','password'); // Form fields names
    foreach ($postVars as $postVar){
        switch($postVar){
            case 'email':  $userObj->$postVar = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) :  ''; 
                            if($userObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
            
            default     :   $userObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                            if($userObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                            break;
        }
    }
    //If validated and not empty submit it to database
    if(count($errorArr) < 1)   { 
        if($userObj->emailExists()){
            $loginInUser = json_decode($userObj->fetch("*", "  email = '$userObj->email' ", " id LIMIT 1", false));
            $_SESSION['ITCLoggedInUser'] = true; 
            $_SESSION['ITCUserName'] = $loginInUser->info[0]->username;
            $_SESSION['ITCuserId'] = $loginInUser->info[0]->id; 
            $_SESSION['ITCuserEmail'] = $loginInUser->info[0]->email;
            echo $userObj->signIn();
        }
        else{ 
            $json = array("status" => 0, "msg" => '<strong>ACCESS DENIED !!!</strong> <br/><u>Reason</u>: The User ID you entered does not exist in our database.'); 
            $dbObj->close();//Close Database Connection
            if(array_key_exists('callback', $_GET)){
                header('Content-Type: text/javascript');  header('Access-Control-Allow-Origin: *');  header('Access-Control-Max-Age: 3628800');  header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
                echo $_GET['callback'].'('.json_encode($json).');';
            }else{ 
                header('Content-Type: application/json'); 
                echo json_encode($json); 

            }
        }
        
    }
    else{ 
        $json = array("status" => 0, "msg" => $errorArr); 
        $dbObj->close();//Close Database Connection
        if(array_key_exists('callback', $_GET)){
            header('Content-Type: text/javascript'); 
            header('Access-Control-Allow-Origin: *'); 
            header('Access-Control-Max-Age: 3628800'); 
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
            echo $_GET['callback'].'('.json_encode($json).');';
        }else{ 
            header('Content-Type: application/json'); 
            echo json_encode($json); 
            
        }
    }
}