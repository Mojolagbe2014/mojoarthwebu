<section id="portfolio">
        <div class="container row" style="margin: 0px auto;">
            <div class="section-header">
                <?php echo Setting::getValue($dbObj, 'THE_PROGRAM') ? Setting::getValue($dbObj, 'THE_PROGRAM') : ''; ?>
            </div>
            <div class="text-center">
                <ul class="portfolio-filter">
                    <?php 
                    $num = 1; $addStyle = '';
                    foreach ($settingObj->fetchRaw("*", " name LIKE 'THE_PROGRAM_MENU%' ", " name ASC ") as $setting) {
                        $settingData = array('name' => 'name', 'value' => 'value');
                        foreach ($settingData as $key => $value){
                            switch ($key) {  default     :  $settingObj->$key = $setting[$value]; break; }
                        }
                        $addStyle = $num == 1 ? 'active ' : ''; 
                        $slugifiedMenu = StringManipulator::slugify(strip_tags($settingObj->value));
                    ?>
                    
                    <li><a href="#"  class="<?php echo $addStyle; ?>toggle" data-content="<?php echo $slugifiedMenu;?>"><?php echo trim(stripcslashes(strip_tags($settingObj->value))); ?></a></li>
                    <?php $num++; } ?>
                </ul><!--/#portfolio-filter-->
            </div>

            <div class="course-details">
                <?php 
                $nums = 1; $addStyles = '';
                foreach ($settingObj->fetchRaw("*", " name LIKE 'THE_PROGRAM_MENU%' ", " name ASC ") as $setting) {
                        $settingData = array('name' => 'name', 'value' => 'value');
                        foreach ($settingData as $key => $value){
                            switch ($key) {  default     :  $settingObj->$key = $setting[$value]; break; }
                        }
                        $slugifiedMenu = StringManipulator::slugify(strip_tags($settingObj->value));
                ?>
                <div class="container toggle-content" id="<?php echo $slugifiedMenu; ?>">
                        <div class="row">
                            <?php echo Setting::getValue($dbObj, str_replace("MENU", "CONTENT", $settingObj->name)) ? Setting::getValue($dbObj, str_replace("MENU", "CONTENT", $settingObj->name)) : ''; ?>
                        </div>
                  <!-- /.row -->
                </div><?php $nums++; } ?>
            </div>
        </div><!--/.container-->
    </section><!--/#portfolio-->
    