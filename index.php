<?php 
session_start();
define("CONST_FILE_PATH", "includes/constants.php");
define("CURRENT_PAGE", "home");
require('classes/WebPage.php'); //Set up page as a web page
require 'swiftmailer/lib/swift_required.php';
$thisPage = new WebPage(); //Create new instance of webPage class

$dbObj = new Database();//Instantiate database
$thisPage->dbObj = $dbObj;
?>
<html>
    <head>
        <title>ARTH App</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <div>
            <h2>New Data Logs</h2> 
            <ul id="content"></ul>
        </div>
        
        <script src="js/jquery.js" type="text/javascript"></script>
        <script>
            function getRandomInt(min, max) { return Math.floor(Math.random() * (max - min + 1)) + min; }
            $(document).ready(function(){
                
                siteRoot = 'http://localhost/mojoarthwebu/REST/log-data.php';
                //console.log(formData);
                setInterval(function(){ 
                    timstmp = Math.round((new Date()).getTime() / 1000);
                    formData = {addNewData: true, clientId: 1, bedId: 1, patientId: 1, createdAt: timstmp,status: getRandomInt(1,4)};
                    
                    $.ajax({
                        type: 'GET',
                        url: siteRoot,
                        data: formData,//{addNewData: 'true', clientId: '1', bedId: '1', patientId: '1', createdAt: Math.round((new Date()).getTime() / 1000),status: getRandomInt(1,4)},
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            var formatted = (new Date(timstmp*1000)).toUTCString();
                            $('#content').append('<li>@'+formatted+' => '+data.msg+'</li>');
                        },
                        error : function(xhr, status) {
                            erroMsg = '';
                            if(xhr.status===0){ erroMsg = 'There is a problem connecting to internet. Please review your internet connection.'; }
                            else if(xhr.status===404){ erroMsg = 'Requested page not found.'; }
                            else if(xhr.status===500){ erroMsg = 'Internal Server Error.';}
                            else if(status==='parsererror'){ erroMsg = 'Error. Parsing JSON Request failed.'; }
                            else if(status==='timeout'){  erroMsg = 'Request Time out.';}
                            else { erroMsg = 'Unknown Error.\n'+xhr.responseText;}          
                            console.log(erroMsg);
                        }
                    });
                    return false;
                }, 60000);

                });
        </script>
    </body>
</html>