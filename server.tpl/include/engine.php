#!/usr/bin/php -q
<?php
require __DIR__ . "/env.php";
if(IS_DEV){
    require(SRC_PATH_DEV . "/AutomationEngine.php");
} else {
    require(SRC_PATH_PROD . "/AutomationEngine.php");
}