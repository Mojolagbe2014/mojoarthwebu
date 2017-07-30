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
    <title>Manage Sponsors/Partners  - Train2Invest</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href="images/icons/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <link href="assets/js/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
    <style>th, td { white-space: nowrap; } </style>
    <script src="../ckeditor/ckeditor.js" type="text/javascript"></script>
    <link href="assets/js/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
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
                    <div class="col-md-12">
                        <div class="messageBox"></div>
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3><i class="fa fa-apple"></i> All Partners/Sponsors</h3>
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table id="sponsorlist" class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="select-checkbox" id="multi-action-box" /></th>
                                                <th>ID</th>
                                                <th>Actions &nbsp; 
                                                    <div style="white-space:nowrap">
                                                    <button  class="btn btn-success btn-sm multi-activate-sponsor multi-select" title="Change selected sponsor status"><i class="btn-icon-only icon-check"> </i></button> 
                                                    <button class="btn btn-danger btn-sm multi-delete-sponsor multi-select" title="Delete Selected"><i class="btn-icon-only icon-trash"> </i></button>
                                                    </div>
                                                </th>
                                                <th>Sponsor</th>
                                                <th>Official Logo</th>
                                                <th>Website</th>
                                                <th>Date Added</th>
                                                <th>Product/Services</th>
                                                <th>Description</th>
                                                <th>Product Image</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="messageBox"></div>
                        <div class="panel panel-info" id="hiddenUpdateForm">
                            <div class="panel-heading">
                                <h3 id="multiHeader">Add Sponsor/Partner</h3>
                            </div>
                            <div class="panel-body">
                                <form role="form" id="CreateSponsor" method="POST" name="CreateSponsor" action="../REST/manage-sponsors.php"  enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="control-label" for="name">Sponsor/Partner Name:</label>
                                        <div class="controls">
                                            <input type="hidden" id="id" name="id"> 
                                            <textarea id="name" name="name" placeholder="Sponsor/Partner Name" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="logo">Official Logo:</label>
                                        <div class="controls">
                                            <input type="hidden" name="oldLogo" id="oldLogo" />
                                            <input data-title="" type="file" placeholder="" id="logo" name="logo" class="form-control">
                                            <br/><span class="hidden" id="oldLogoLabel">Old Logo:</span> <span id="oldLogoComment"></span>
                                            <div id="oldLogoSource"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="website">Website:</label>
                                        <div class="controls">
                                            <input type="url" id="website" name="website" placeholder="E.g http://www.sponsorwebsite.com" class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="product">Product/Services:</label>
                                        <div class="controls">
                                            <input type="text" id="product" name="product" placeholder="Product/Services" class="form-control"/>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="description">Description:</label>
                                        <div class="controls">
                                            <textarea id="description" name="description" class="form-control"></textarea>
                                            <script> CKEDITOR.replace('description');</script>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label" for="image">Product Image:</label>
                                        <div class="controls">
                                            <input type="hidden" name="oldImage" id="oldImage" />
                                            <input data-title="" type="file" placeholder="" id="image" name="image" class="form-control">
                                            <br/><span class="hidden" id="oldImageLabel">Old Image:</span> <span id="oldImageComment"></span>
                                            <div id="oldImageSource"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="controls">
                                            <input type="hidden" name="addNewSponsor" id="addNewSponsor" value="addNewSponsor"/>
                                            <button type="submit" class="btn btn-success" id="multi-action-sponsorAddEdit">Add Sponsor</button> &nbsp; &nbsp;
                                        </div>
                                    </div>
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
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js" type="text/javascript"></script>
    <script src="assets/js/gritter/js/jquery.gritter.min.js" type="text/javascript"></script>
    <script src="assets/js/dataTables/jquery.dataTables.js"></script>
    <script src="assets/js/dataTables/dataTables.bootstrap.js"></script>
    <script src="assets/js/manage-sponsors.js"></script>
    <script src="assets/js/custom.js"></script>
</body>
</html>
