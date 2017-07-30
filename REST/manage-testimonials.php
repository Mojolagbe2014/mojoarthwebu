<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$testimonialObj = new Testimonial($dbObj); // Create an object of Testimonial class
$errorArr = array(); //Array of errors
$oldMedia = ""; $newMedia =""; $testimonialImg ="";

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    echo json_encode($json);
}
else{
    if(filter_input(INPUT_POST, "addNewTestimonial") != NULL && filter_input(INPUT_POST, "addNewTestimonial")=="addNewTestimonial"){
        $postVars = array('author', 'content', 'image'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image':   $testimonialObj->$postVar = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_".  time().".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $testimonialImg = $testimonialObj->$postVar;
                                if($testimonialObj->$postVar == "") {$testimonialImg = "";}
                                break;
                default     :   $testimonialObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($testimonialObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            
            $target_file = MEDIA_FILES_PATH."testimonial/". $testimonialImg;
            $uploadOk = 1; $msg = '';
            if ($testimonialImg!='' && file_exists($target_file)) { $msg .= " testimonial image already exists."; $uploadOk = 0; }
            if ($_FILES["image"]["size"] > 800000000) { $msg .= " testimonial image is too large."; $uploadOk = 0; }
            if ($uploadOk == 0) {
                $msg = "Sorry, your testimonial image was not uploaded. ERROR: ".$msg;
                $json = array("status" => 0, "msg" => $msg); 
                $dbObj->close();//Close Database Connection
                header('Content-type: application/json');
                echo json_encode($json);
            } 
            else {
                if($testimonialImg !=''){ 
                    move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
                    $msg .= "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
                    $status = 'ok';
                }
                echo $testimonialObj->add();
            }

        }
        //Else show error messages
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
    } 
    
    if(filter_input(INPUT_POST, "fetchTestimonials") != NULL){
        $requestData= $_REQUEST;
        $columns = array( 0 =>'id', 1 =>'id', 2 => 'content', 3 => 'author', 4 => 'image');

        // getting total number records without any search
        $query = $dbObj->query("SELECT * FROM testimonial ");
        $totalData = mysqli_num_rows($query);
        $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

        $sql = "SELECT * FROM testimonial WHERE 1=1 ";
        if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
                $sql.=" AND ( author LIKE '%".$requestData['search']['value']."%' ";    
                $sql.=" OR content LIKE '".$requestData['search']['value']."%' ) ";
        }
        $query = $dbObj->query($sql);
        $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
        $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
        /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

        echo $testimonialObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
    }
    
    if(filter_input(INPUT_POST, "deleteThisTestimonial")!=NULL){
        $postVars = array('id','image'); // Form fields names
        //Validate the POST variables and add up to error message if empty
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image':   $testimonialObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                $testimonialImg = $testimonialObj->$postVar;
                                if($testimonialObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
                default     :   $testimonialObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($testimonialObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        //If validated and not empty submit it to database
        if(count($errorArr) < 1)   {
            if($testimonialImg!='' && file_exists(MEDIA_FILES_PATH."testimonial/".$testimonialImg)){ unlink(MEDIA_FILES_PATH."testimonial/".$testimonialImg); }
            echo $testimonialObj->delete();
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }

    } 
    
    if(filter_input(INPUT_POST, "addNewTestimonial") != NULL && filter_input(INPUT_POST, "addNewTestimonial")=="editTestimonial"){
        $postVars = array('id', 'author', 'content', 'image'); // Form fields names
        $oldMedia = $_REQUEST['oldFile'];
        foreach ($postVars as $postVar){
            switch($postVar){
                case 'image':   $newMedia = basename($_FILES["image"]["name"]) ? rand(100000, 1000000)."_". time().".".pathinfo(basename($_FILES["image"]["name"]),PATHINFO_EXTENSION): ""; 
                                $testimonialObj->$postVar = $newMedia;
                                if($testimonialObj->$postVar == "") { $testimonialObj->$postVar = $oldMedia;}
                                $testimonialImg = $newMedia;
                                break;
                default     :   $testimonialObj->$postVar = filter_input(INPUT_POST, $postVar) ? mysqli_real_escape_string($dbObj->connection, filter_input(INPUT_POST, $postVar)) :  ''; 
                                if($testimonialObj->$postVar === "") {array_push ($errorArr, "Please enter $postVar ");}
                                break;
            }
        }
        if(count($errorArr) < 1)   {
            $target_file = MEDIA_FILES_PATH."testimonial/". $testimonialImg;
            $uploadOk = 1; $msg = '';
            
            
            if($newMedia !=""){ 
                move_uploaded_file($_FILES["image"]["tmp_name"], $target_file); 
                if ($oldMedia!='' && file_exists(MEDIA_FILES_PATH."testimonial/".$oldMedia)) { unlink(MEDIA_FILES_PATH."testimonial/".$oldMedia); }
            } 
            echo $testimonialObj->update();
        }
        else{ 
            $json = array("status" => 0, "msg" => $errorArr); 
            $dbObj->close();//Close Database Connection
            header('Content-type: application/json');
            echo json_encode($json);
        }
    } 
}