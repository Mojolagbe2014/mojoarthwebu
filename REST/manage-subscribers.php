<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
require '../swiftmailer/lib/swift_required.php';
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$userObj = new User($dbObj); // Create an object of User class
$newsObj = new News($dbObj);
$errorArr = array(); //Array of errors
$email =''; $name =''; $subject=''; $message; $body = '';

include('../includes/other-settings.php');

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "fetchUsers") != NULL){
        $requestData= $_REQUEST;
        $columns = array( 0 =>'id', 1 =>'id', 2 => 'name', 3 => 'email', 4 => 'company');

        // getting total number records without any search
        $query = $dbObj->query("SELECT * FROM user ");
        $totalData = mysqli_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT * FROM user WHERE 1=1 ";
        if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
                $sql.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";  
                $sql.=" OR email LIKE '".$requestData['search']['value']."%' ";
                $sql.=" OR company LIKE '".$requestData['search']['value']."%' ) ";
        }
        $query = $dbObj->query($sql);
        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

        echo $userObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
    }
    
    if(filter_input(INPUT_POST, "deleteThisUser")!=NULL){
        $postVars = array('id'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                default     :   $userObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($userObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   { echo $userObj->delete(); }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    } 
    
    if(filter_input(INPUT_POST, "sendEmails")!=NULL && filter_input(INPUT_POST, "newsType")=="custom"){
        $postVars = array('email', 'name', 'subject', 'message'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'message' :   $body = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($body === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   { 
            $emailAddress = COMPANY_EMAIL;
            $addBody = "<p>If you want to stop receiving this mail, <a href='".SITE_URL."REST/unsubscribe.php?email=".$email."&id=".User::getSingle($dbObj, 'id', $email)."'>unsubcribe here!!</a></p>";
            if(empty($subject)) $subject = "Message From: ".WEBSITE_AUTHOR;	
            $transport = Swift_MailTransport::newInstance();
            $message = Swift_Message::newInstance();
            $message->setTo(array($email => $name));
            $message->setSubject($subject);
            $message->setBody($body.$addBody);
            $message->setFrom($emailAddress, WEBSITE_AUTHOR);
            $message->setContentType("text/html");
            $mailer = Swift_Mailer::newInstance($transport);
            $mailer->send($message);
            
            $json = array("status" => 1, "msg" => "Your message to $name has been sent."); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    } 

    if(filter_input(INPUT_POST, "sendEmails")!=NULL && filter_input(INPUT_POST, "newsType")!="custom"){
        $postVars = array('email', 'name', 'newsType'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                default     :   $$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   { 
            
            $emailAddress = COMPANY_EMAIL;
            $subject = "Newsletter - ".$newsObj->title;
            $transport = Swift_MailTransport::newInstance();
            $message = Swift_Message::newInstance();
            $message->setTo(array($email => $name));
            $message->setSubject($subject);
            include('../includes/email-template.php');
            $message->setBody($body);
            $message->setFrom($emailAddress, WEBSITE_AUTHOR);
            $message->setContentType("text/html");
            $mailer = Swift_Mailer::newInstance($transport);
            
//            $headers = 'MIME-Version: 1.0' . "\r\n";
//            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//            $headers .= "From:$emailAddress \r\n";
//            $headers .= "Reply-To: $emailAddress \r\n";
            
            //fix gmail image problem
            //if(strpos($email, "@gmail.com")!=false) { 
                $doc = new DOMDocument();
                $doc->loadHTML($newsObj->description);
                $imageTags = $doc->getElementsByTagName('img');

                foreach($imageTags as $tag) {
                    $thisTagSrc = $tag->getAttribute('src');
                    $message->attach( 
                        Swift_Attachment::fromPath($thisTagSrc)->setFilename(basename($thisTagSrc))
                    );
                }
            //}
            
            $mailer->send($message);
            //mail($email, $subject, $body, $headers);
            
            $json = array("status" => 1, "msg" => "Your newsletter to $name has been sent."); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
    }
}