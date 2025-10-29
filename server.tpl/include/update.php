<?php
// Bu daha sonra kaldırılacak
require __DIR__ . "/env.php";
if(IS_DEV){
    require SRC_PATH_DEV . "/update.php";
} else {
    require SRC_PATH_PROD . "/update.php";
}
