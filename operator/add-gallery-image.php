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
    <title>Add Gallery Image  - Train2Invest</title>
    <link href="media-uploader/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="media-uploader/css/uploader.css" rel="stylesheet" type="text/css"/>
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/custom.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
    <script src="../ckeditor/ckeditor.js" type="text/javascript"></script>
    <link href="../css/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <link href="media-uploader/css/demo.css" rel="stylesheet" type="text/css"/>
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
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3><i class="fa fa-picture-o"></i> Add Images to Gallery</h3>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-6">
                                  <!-- D&D Zone-->
                                  <div id="drag-and-drop-zone" class="uploader">
                                    <div>Drag &amp; Drop Images Here</div>
                                    <div class="or">-or-</div>
                                    <div class="browser">
                                      <label>
                                        <span>Click to open the file Browser</span>
                                        <input type="file" name="files[]"  accept="image/*" multiple="multiple" title='Click to add Images'>
                                      </label>
                                    </div>
                                  </div>
                                  <!-- /D&D Zone -->

                                  <!-- Debug box -->
                                  <div class="panel panel-default">
                                    <div class="panel-heading">
                                      <h3 class="panel-title">Status/Result</h3>
                                    </div>
                                    <div class="panel-body demo-panel-debug">
                                      <ul id="demo-debug">
                                      </ul>
                                    </div>
                                  </div>
                                  <!-- /Debug box -->
                                </div>
                                <!-- / Left column -->

                                <div class="col-md-6">
                                  <div class="panel panel-default">
                                    <div class="panel-heading">
                                      <h3 class="panel-title">Uploads</h3>
                                    </div>
                                    <div class="panel-body demo-panel-files" id='demo-files'>
                                      <span class="demo-note">No Files have been selected/dropped yet...</span>
                                    </div>
                                  </div>
                                </div>
                                <!-- / Right column -->
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
    <script src="../js/jquery-ui.1.11.4.js" type="text/javascript"></script>
    <script src="assets/js/common-handler.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.metisMenu.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="media-uploader/js/jquery-1.10.1.min.js" type="text/javascript"></script>
    <script src="media-uploader/js/jquery-migrate-1.2.1.min.js" type="text/javascript"></script>
    <script src="media-uploader/js/demo-preview.min.js" type="text/javascript"></script>
    <script src="media-uploader/js/dmuploader.min.js" type="text/javascript"></script>
    <script src="assets/js/add-gallery-image.js" type="text/javascript"></script>
</body>
</html>