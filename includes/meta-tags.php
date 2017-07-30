<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
    <meta charset="UTF-8" />    
    <!--[if lt IE 9]> 
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <title><?php echo $thisPage->title; ?></title>
    <link rel='shortcut icon' href='<?php echo SITE_URL; ?>images/favicon.png' type='image/x-icon' />
    <meta name='viewport' content='width=device-width, initial-scale=1, maximum-scale=1' />
    <meta name="description" content="<?php echo $thisPage->description; ?>"/>
    <meta name="robots" content="All" />
    <meta name="robots" content="index, follow" />
    <meta name="keywords" content="<?php echo $thisPage->keywords; ?>" />
    <meta name="rating" content="General" />
    <meta name="dcterms.title" content="<?php echo $thisPage->title; ?>" />
    <meta name="dcterms.contributor" content="<?php echo $thisPage->author; ?>" />
    <meta name="dcterms.creator" content="<?php echo $thisPage->author; ?>" />
    <meta name="dcterms.publisher" content="<?php echo $thisPage->author; ?>" />
    <meta name="dcterms.description" content="<?php echo $thisPage->description; ?>" />
    <meta name="dcterms.rights" content="2010 - 2020" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="en_US" />
    <meta property="og:title" content="<?php echo $thisPage->title; ?>" />
    <meta property="og:url" content="<?php echo SITE_URL1.$_SERVER['REQUEST_URI']; ?>" />
    <meta property="og:site_name" content="<?php echo $thisPage->author; ?>" />
    <meta property="og:title" content="<?php echo $thisPage->title; ?>" />
    <meta property="og:description" content="<?php echo $thisPage->description; ?>" />
    <meta property="twitter:title" content="<?php echo $thisPage->title; ?>" />
    <meta property="twitter:description" content="<?php echo $thisPage->description; ?>" />
    <meta property="og:image" content="<?php echo SITE_URL; ?>images/favicon.png"/>
    <meta property="og:image:type" content="image/jpeg"/>
    <meta property="og:image:width" content="200"/>
    <meta property="og:image:height" content="200"/>
    <meta property="fb:admins" content="<?php echo FACEBOOK_ADMINS; ?>" />
    <meta property="fb:app_id" content="<?php echo FACEBOOK_APP_ID; ?>"/>
    <meta property="twitter_id" content="<?php echo TWITTER_ID; ?>"/>
    <?php echo utf8_decode(Setting::getValue($dbObj, 'ANALYTICS')); ?>