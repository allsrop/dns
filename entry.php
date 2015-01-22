<?php
define('PROJ_DIR', __DIR__);
require('vendor/autoload.php');

// init lazyrecord
$config = new LazyRecord\ConfigLoader;
$config->load('db/config/database.yml');
$config->init();

// run!
(new Fruit\Route\Pux(Fruit\Config\IniConfig::fromFile('config.ini'), $_SERVER['PATH_INFO']))->route();
