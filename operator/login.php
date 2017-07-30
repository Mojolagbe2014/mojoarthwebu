<?php session_start(); ?>
<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$adminObj = new Admin($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Section - Train2Invest</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
   <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />

</head>
<body>
    <div class="container">
        <div class="row text-center ">
            <div class="col-md-12">
                <div id="messageBox"></div>
                <br /><br />
                <h2> Train2Invest</h2>
               
                <h5>( Login to get access )</h5>
                 <br />
            </div>
        </div>
         <div class="row ">
            <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <strong>   Enter Details To Login </strong>  
                    </div>
                    <div class="panel-body">
                        <form role="form" id="login-form" name="login-form" action="../REST/admin-login.php" method="POST">
                            <br />
                          <div class="form-group input-group">
                                 <span class="input-group-addon"><i class="fa fa-tag"  ></i></span>
                                 <input type="email" id="email" name="email" class="form-control" placeholder="Admin ID " required="required"/>
                             </div>
                                                                   <div class="form-group input-group">
                                 <span class="input-group-addon"><i class="fa fa-lock"  ></i></span>
                                 <input type="password" id="passWord" name="passWord" class="form-control"  placeholder="Authentication PIN"  required="required"/>
                             </div>
                         <div class="form-group">
                                 <label class="checkbox-inline">
                                     <input type="checkbox" /> Remember me
                                 </label>
                                 <span class="pull-right"></span>
                             </div>
                            <input type="hidden" name="loginstuff" id="loginstuff" value="loginstuff"/>
                            <button type="submit" class="btn btn-primary ">Request Access</button>
                         </form>
                    </div>
                    <div class="messageBox"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-1.10.2.js"></script>
    <script src="assets/js/login.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</body>
</html>
