<?php
require dirname(__DIR__) . "/include/env.php";
if(IS_DEV){
    require SRC_PATH_DEV . "/mainInclude.php";
} else {
    require SRC_PATH_PROD . "/mainInclude.php";
}