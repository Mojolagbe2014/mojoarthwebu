<?php
session_start();
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$adminObj = new Admin($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors

if(!isset($_SESSION['ITCLoggedInAdmin']) || !isset($_SESSION["ITCadminEmail"])){ 
    $json = array("status" => 0, "msg" => "You are not logged in."); 
    header('Content-type: application/json');
    echo json_encode($json);
}
else{
    $requestData= $_REQUEST;
    $columns = array( 0 =>'id', 1 =>'id', 2 => 'name', 3=> 'email', 4=> 'username', 5 => 'role', 6 => 'date_registered');

    // getting total number records without any search
    $query = $dbObj->query("SELECT * FROM admin ");
    $totalData = mysqli_num_rows($query);
    $totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.

    $sql = "SELECT id, name, email, username, role, date_registered FROM admin WHERE 1=1 ";
    if(!empty($requestData['search']['value'])) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
            $sql.=" AND ( name LIKE '%".$requestData['search']['value']."%' ";    
            $sql.=" OR email LIKE '%".$requestData['search']['value']."%' ";
            $sql.=" OR username LIKE '%".$requestData['search']['value']."%' )";
    }
    $query = $dbObj->query($sql);
    $totalFiltered = mysqli_num_rows($query); // when there is a search parameter then we have to modify total number filtered rows as per search result. 
    $sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
    /* $requestData['order'][0]['column'] contains colmun index, $requestData['order'][0]['dir'] contains order such as asc/desc  */	

    echo $adminObj->fetchForJQDT($requestData['draw'], $totalData, $totalFiltered, $sql);
}