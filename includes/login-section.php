<li class="menu_user_login"><a href="#popup_login" class="popup_link popup_login_link" style="background:#000">Subscribe</a>
                                    <div id="popup_login" class="popup_wrap popup_login bg_tint_light" style="border:1px solid #000">
                                        <a href="#" class="popup_close"></a>
                                        <div class="form_wrap">
                                            <div class="">
                                                <form action="<?php echo SITE_URL.'REST/subscribe.php'; ?>" method="post" name="login_form" class="popup_form login_form">
                                                    <input type="hidden" name="redirect_to" value="">
                                                    <div class="popup_form_field password_field iconed_field icon-agenda-2"><input type="text" id="subscriberCompany" name="subscriberCompany" value="" placeholder="Company Name" required="required"></div>
                                                    <div class="popup_form_field password_field iconed_field icon-user-2"><input type="text" id="subscriberName" name="subscriberName" value="" placeholder="Subscriber Name" required="required"></div>
                                                    <div class="popup_form_field login_field iconed_field icon-mail"><input type="email" id="subscriberEmail" name="subscriberEmail" value="" placeholder="Email" required="required"></div>
                                                    <div class="popup_form_field submit_field"><input type="submit" class="submit_button" name="subscriberSubmit" value="Subscribe Now!"></div>
                                                </form>
                                            </div>
                                            
                                        </div>	<!-- /.login_wrap -->
                                    </div>		<!-- /.popup_login -->
                                </li>