<section id="features">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title text-center wow fadeInDown animated" style="visibility: visible; animation-name: fadeInDown;">Breaking News</h2>
            </div>

            <?php 
            foreach ($newsObj->fetchRaw("*", " status = 1 ", " date_added DESC LIMIT 1") as $news) {
                $newsData = array('id' => 'id', 'title' => 'title', 'image' => 'image', 'description' => 'description', 'dateAdded' => 'date_added', 'status' => 'status');
                foreach ($newsData as $key => $value){
                    switch ($key) { 
                        case 'image': $newsObj->$key = MEDIA_FILES_PATH1.'news/'.$news[$value];break;
                        default     :   $newsObj->$key = $news[$value]; break; 
                    }
                }
            ?>
            <div class="row">
                <div class="col-sm-6 wow fadeInLeft animated" style="visibility: visible; animation-name: fadeInLeft;">
                    <img style="border-radius: 10px;" class="img-responsive" src="<?php echo $newsObj->image; ?>" alt="">
                </div>
                <div class="col-sm-6">
                    <div class="media service-box wow fadeInRight animated" style="visibility: visible; animation-name: fadeInRight;">
                        <div class="media-body">
                            <h4 class="media-heading"><?php echo $newsObj->title; ?></h4>
                            <p><?php echo $newsObj->description; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </section>