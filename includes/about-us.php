<section id="about">
        <div class="container">

            <div class="section-header">
                <h2 class="section-title text-center wow fadeInDown">About Us</h2>
                <p class="text-center wow fadeInDown" style="color:#111 !important;"><?php echo Setting::getValue($dbObj, 'ABOUT_US_INTRO') ? Setting::getValue($dbObj, 'ABOUT_US_INTRO') : ''; ?></p>
            </div>

            <div class="row">
              <div class="col-md-12">
                  <div class="panel panel-default">
                      <div class="panel-body min_height">
                          <?php echo Setting::getValue($dbObj, 'ABOUT_US_CONTENT') ? Setting::getValue($dbObj, 'ABOUT_US_CONTENT') : ''; ?>
                      </div>
                  </div>
              </div>
            </div>
            <div class="row toggle-read-more"><?php echo Setting::getValue($dbObj, 'ABOUT_US_READ_MORE') ? Setting::getValue($dbObj, 'ABOUT_US_READ_MORE') : ''; ?></div>
        </div>
    </section><!--/#about-->