<?php
define('PROJ_DIR', __DIR__);
require('vendor/autoload.php');

// run!
(new Fruit\Route\Pux(Fruit\Config\IniConfig::fromFile('config.ini'), $_SERVER['PATH_INFO']))->route();
