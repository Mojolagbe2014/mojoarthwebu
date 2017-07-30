<section id="main-slider">
        <div class="owl-carousel">
            <?php 
            $addStyle = '';
            foreach ($sliderObj->fetchRaw("*", " status = 1 ", " orders ASC ") as $slider) {
                $sliderData = array('id' => 'id', 'title' => 'title', 'content' => 'content', 'orders' => 'orders', 'status' => 'status', 'image' => 'image');
                foreach ($sliderData as $key => $value){
                    switch ($key) { 
                        case 'image': $sliderObj->$key = MEDIA_FILES_PATH1.'slider/'.$slider[$value];break;
                        default     :   $sliderObj->$key = $slider[$value]; break; 
                    }
                }
                $addStyle = $sliderObj->orders == 1 ? 'image-fam' : ''; 
                //$sliderObj->image = new ThumbNail($sliderObj->image, 80, 80);
            ?>
            <div class="item">
                <div class="slider-inner">
                    <div class="container">
                        <div class="support item <?php echo $addStyle; ?>"  style="background-image: url(<?php echo $sliderObj->image; ?>);"> </div>
                            <div class="row">
                                <div class="col-sm-12 col-lg-12 col-md-12 slider-content ">
                                    <div class="carousel-content">
                                        <h2><?php echo $sliderObj->title; ?></h2>
                                        <p class="slider-text-content"><?php echo $sliderObj->content; ?></p>
                                    </div>
                                </div>
                          </div>
                    </div>
                </div>
          </div><!--/.item-->
          <?php } ?>
        </div><!--/.owl-carousel-->
    </section><!--/#main-slider-->