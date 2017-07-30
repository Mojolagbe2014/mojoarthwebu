            <div>
                    <?php if(isset($_SESSION['msg'])) {  ?>
                    <script src="<?php echo SITE_URL; ?>sweet-alert/sweetalert.min.js" type="text/javascript"></script>
                    <script>
                        swal({
                            title: "Message Box!",
                            text: '<?php echo $_SESSION['msg']; ?>',
                            confirmButtonText: "Okay",
                            customClass: 'twitter',
                            html: true,
                            type: '<?php echo $_SESSION['msgStatus']; ?>'
                        });
                    </script>
                    <?php  unset($_SESSION['msg']); unset($_SESSION['msgStatus']);  } ?>
                </div>
    <header id="header">
        <nav id="main-menu" class="navbar navbar-default navbar-fixed-top" role="banner">
            <div class="container navbar-header">
                <div class="navbar-header" style="float:left !important;">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand " href="<?php echo SITE_URL; ?>"><img src="images/logo.jpg" alt="logo"></a>
                </div>

                <div class="collapse navbar-collapse navbar-right">
                    <ul class="nav navbar-nav">
                        <li class="scroll active"><a href="#home">Home</a></li>
                        <li class="scroll"><a href="#about">About</a></li>
                        <li class="scroll"><a href="#portfolio">The Program</a></li>
                        <li class="scroll"><a href="#testimonial">Testimonals</a></li> 
                        <li class="scroll"><a href="#features">Breaking News</a></li>
                        <li class="scroll"><a href="#contactus">Contact</a></li>
                        <li><a href="<?php echo FACEBOOK_LINK; ?>" title="Socialize with us on Facebook" target="_blank"><i class="fa fa-facebook-square fa-2x" style="color:blue"></i></a></li>
                        <li><a href="#subscribeForm" title="Subscribe to our newsletter"><i class="fa fa-user fa-2x"></i></a></li>
                    </ul>
                </div>
            </div><!--/.container-->
        </nav><!--/nav-->
    </header><!--/header-->