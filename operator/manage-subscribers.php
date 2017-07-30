<?php session_start(); ?>
<?php
define("CONST_FILE_PATH", "../includes/constants.php");
include ('../classes/WebPage.php'); //Set up page as a web page
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$adminObj = new Admin($dbObj); // Create an object of Admin class
$errorArr = array(); //Array of errors
?>﻿
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Manage Subscribers  - Train2Invest</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href="images/icons/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <link href="assets/js/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <script src="../ckeditor/ckeditor.js" type="text/javascript"></script>
</head>
<body>
    <div id="wrapper">
        <?php include('includes/top-bar.php'); ?> 
        <!-- /. NAV TOP  -->
        <?php include('includes/side-bar.php'); ?> 
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div id="messageBox"></div>
                    <div class="col-md-12">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3> <i class="fa fa-users"></i> All Subscribers</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="subscriberlist" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="select-checkbox" id="multi-action-box" /></th>
                                                <th>ID</th>
                                                <th>Subscriber Name</th>
                                                <th>Email Address</th>
                                                <th>Company Name</th>
                                                <th>Actions &nbsp; 
                                                    <button class="btn btn-danger btn-sm multi-delete-user multi-select" title="Delete Selected"><i class="btn-icon-only icon-trash"> </i></button>
<!--                                                    <button class="btn btn-primary btn-sm multi-message-user multi-select" title="Message Selected Users"><i class="btn-icon-only icon-envelope"> </i></button>-->
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="messageBox"></div>
                            <div class="panel panel-info" id="hiddenUpdateForm">
                            <div class="panel-heading">
                                <h3> <i class="fa fa-envelope"></i> Compose Email Message</h3>
                            </div>
                            <div class="panel-body">
                                <form role="form" id="emailSenderForm" name="emailSenderForm" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="control-label" for="newsType">Message Type:</label>
                                        <div class="controls">
                                            <select tabindex="1" name="newsType" id="newsType" data-placeholder="Custom Message" class="form-control">
                                                <option value="custom"> -- Custom Message -- </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label" for="subject">Subject:</label>
                                        <div class="controls">
                                            <input type="hidden" id="id" name="id" value=""/>
                                            
                                            <input type="text" id="subject" name="subject" placeholder="mail subject" class="form-control">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label" for="message">Body:</label>
                                        <div class="controls">
                                            <textarea class="span5" id="message" name="message" class="form-control"></textarea>
                                            <script>
                                                var ckeditor = CKEDITOR.replace('message');
                                            </script>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="controls">
                                            <button type="submit" name="sendEmail" id="sendEmail" class="btn btn-success">Send Mail</button> &nbsp; &nbsp;
                                            <button type="reset" class="btn btn-danger">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js" type="text/javascript"></script>
    <script src="assets/js/gritter/js/jquery.gritter.min.js" type="text/javascript"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="assets/js/manage-subscribers.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>