<?php
global $indexConfig;
global $globalConfig;
$path = dirname(__DIR__, 3) . "/config.php";
if (!is_file($path)) {
    die("Global config not found.");
}
require($path);
date_default_timezone_set($globalConfig['staticParameters']['default_timezone']);