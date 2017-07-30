<section id="testimonial">
        <div class="container">
            <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="embed-responsive embed-responsive-16by9">
                  <video controls>
                      <?php foreach ($videoObj->fetchRaw("*", " name = 'home_video_one' ", " name ASC LIMIT 1 ") as $video) { ?>
                        <source src="media/video/<?php echo $video['video']; ?>" type="video/mp4">
                      <?php } ?>
                      Your browser does not support the video tag.
                  </video>
                </div>
            </div>
                <div class="col-sm-12 col-lg-6 col-md-6">

                    <div id="carousel-testimonial" class="carousel slide text-center" data-ride="carousel">
                        <!-- Wrapper for slides -->
                        <h2>Testimonials</h2>
                        <div class="carousel-inner" role="listbox">
                            <?php 
                                $num = 1;
                                $totDisplTest = Setting::getValue($dbObj, 'TOTAL_DISPLAYABLE_TESTIMONIAL') ? trim(stripcslashes(strip_tags(Setting::getValue($dbObj, 'TOTAL_DISPLAYABLE_TESTIMONIAL')))) : 50;
                                foreach($testimonialObj->fetchRaw("*", " 1=1 ", " RAND() LIMIT $totDisplTest") as $testimonial) { 
                                    $activeMenu = $num == 1 ? "active " : "";
                            ?>
                            <div class="item <?php echo $activeMenu; ?>">
                                <h4><?php echo $testimonial['author']; ?></h4>
                                <p style="margin-top:15px"><?php echo $testimonial['content']; ?></p>
                            </div>
                            <?php ++$num; } ?>
                        </div>

                        <!-- Controls -->
                        <div class="btns">
                            <a class="btn btn-primary btn-sm" href="#carousel-testimonial" role="button" data-slide="prev">
                                <span class="fa fa-angle-left" aria-hidden="true"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="btn btn-primary btn-sm" href="#carousel-testimonial" role="button" data-slide="next">
                                <span class="fa fa-angle-right" aria-hidden="true"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                        <br>
<!--                        <div><a class="btn btn-primary btn-lg" href="testimonials.pdf">Read More</a></div>-->
                    </div>
                </div>
            
            </div>
        </div>
    </section><!--/#testimonial-->