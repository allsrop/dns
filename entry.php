<?php
define('PROJ_DIR', __DIR__);
require('vendor/autoload.php');

// run!
(new Fruit\Route\Pux(new Fruit\Config\IniConfig('config.ini'), $_SERVER['PATH_INFO']))->route();
