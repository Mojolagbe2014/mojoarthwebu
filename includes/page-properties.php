<?php
$thisPage->title = StringManipulator::trimStringToFullWord(62, stripslashes(strip_tags(WebPage::getSingleByName($dbObj, 'title', CURRENT_PAGE))).' - '.WEBSITE_AUTHOR);
$thisPage->description = StringManipulator::trimStringToFullWord(150, trim(stripslashes(strip_tags(WebPage::getSingleByName($dbObj, 'description', CURRENT_PAGE)))));
$thisPage->keywords = WebPage::getSingleByName($dbObj, 'keywords', CURRENT_PAGE);
$thisPage->author = WEBSITE_AUTHOR;