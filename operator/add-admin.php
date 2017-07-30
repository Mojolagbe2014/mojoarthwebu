<?php session_start(); ?>
<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$adminObj = new Admin($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors
?>
﻿<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Admin  - Train2Invest</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
    <div id="wrapper">
        <?php include('includes/top-bar.php'); ?> 
        <!-- /. NAV TOP  -->
        <?php include('includes/side-bar.php'); ?> 
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row text-center  ">
                    <div class="messageBox"></div>
                    <div class="col-md-12">
                        <br /><br />
                        <h2><i class="fa fa-user"></i> Add New Site Administrator</h2>
                         <br />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                         <div class="panel panel-default">
                             <div class="panel-heading">
                                 <strong>  Fill all fields </strong>  
                             </div>
                             <div class="panel-body">
                                 <form role="form" id="CreateAdmin" name="CreateAdmin" action="../REST/add-admin.php" method="POST">
                                     <br/>
                                     <div class="form-group input-group">
                                         <span class="input-group-addon"><i class="fa fa-user"  ></i></span>
                                         <input type="text" class="form-control" id='name' name='name' placeholder="Admin's Full Name" required="required"/>
                                     </div>
                                     <div class="form-group input-group">
                                         <span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
                                         <input type="text" class="form-control" id="userName" name="userName" placeholder="Desired Username" required="required"/>
                                     </div>
                                     <div class="form-group input-group">
                                        <span class="input-group-addon">@</span>
                                        <input type="email" class="form-control" id="email" name="email" placeholder="Valid Email" required="required"/>
                                    </div>
                                     <div class="form-group input-group">
                                           <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                           <input type="password" class="form-control" id="passWord" name="passWord" placeholder="Enter Password" required="required"/>
                                     </div>
                                     <div class="form-group input-group">
                                         <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                         <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Retype Password" required="required"/>
                                     </div>
                                     <input type="hidden" id="addNewAdmin" name="addNewAdmin" value="addNewAdmin"/>
                                    <div class="form-group input-group">
                                         <span class="input-group-addon"><i class="fa fa-archive"  ></i></span>
                                         <select name="role" id="role" class="form-control" required="required">
                                             <option value=""> -- Select a role -- </option>
                                             <option value="Admin">Admin</option>
                                             <option value="Sub-Admin">Sub-Admin</option>
                                         </select>
                                     </div>
                                     <button type="submit" class="btn btn-success ">Add New Admin</button>
                                </form>
                             </div>
                            
                         </div>
                     </div>
                </div>
                <div class="messageBox"></div>
            </div>
             <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
     <!-- /. WRAPPER  -->
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/common-handler.js"></script>
    <script src="assets/js/add-admin.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
