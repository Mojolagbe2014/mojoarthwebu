<nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
                    <li class="text-center"> <a href="profile" title="My Profile"><img src="assets/img/find_user.png" class="user-image img-responsive"/></a> </li>
                    <li> <a href="index"><i class="fa fa-dashboard fa-2x"></i> Dashboard</a> </li>
                    <?php if(isset($_SESSION['ITCadminRole']) && $_SESSION['ITCadminRole'] =='Admin' ) { ?>
                    <li> <a href="#"><i class="fa fa-user fa-2x"></i>Admin Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-third-level">
                            <li> <a href="add-admin">Add Site Admin</a> </li>
                            <li> <a href="manage-admins">Manage Site Admins</a> </li>
                        </ul>
                    </li>
                    <?php } ?>
                    <li> <a href="#"><i class="fa fa-file fa-2x"></i> Brochure Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-brochures">Manage Brochures</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-user fa-2x"></i> Subscribers<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-subscribers">Manage Subscribers</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-apple fa-2x"></i> Sliders Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="add-slider">Add Slider</a> </li>
                            <li> <a href="manage-sliders">Manage Slider</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-question-circle fa-2x"></i> FAQs Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-faq">Manage FAQs</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-bell-o fa-2x"></i> Breaking News<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-news">Manage News</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-quote-left fa-2x"></i> Testimonials Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-testimonials">Manage Testimonials</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-picture-o fa-2x"></i> Gallery Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="add-gallery-image">Add Images</a> </li>
                            <li> <a href="manage-gallery">Manage Gallery</a> </li>
                        </ul>
                    </li>
                    <li> <a href="#"><i class="fa fa-video-camera fa-2x"></i> Video Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-videos">Add Video</a> </li>
                            <li> <a href="manage-videos">Manage Videos</a> </li>
                        </ul>
                    </li>
                    <?php if(isset($_SESSION['ITCadminEmail']) && $_SESSION['ITCadminEmail'] == trim(stripcslashes(strip_tags(Setting::getValue($dbObj, 'COMPANY_EMAIL'))))) { ?>
                    <li> <a href="#"><i class="fa fa-cog fa-2x"></i>Settings Manager<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li> <a href="manage-settings"><i class="fa fa-cogs fa-1x"></i> General Settings</a> </li>
                            <li> <a href="manage-webpages"><i class="fa fa-globe fa-1x"></i> Manage Web Pages</a> </li>
                        </ul>
                    </li>
                    <?php } ?>
                    <li>
                        <a  href="profile"><i class="fa fa-book fa-2x"></i> My Profile</a>
                    </li>
                    <li>
                        <a  href="#" class="logout"><i class="fa fa-sign-out fa-2x"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </nav>  