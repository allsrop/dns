<?php

require_once('vendor/autoload.php');

$mux = new \Pux\Mux;

$o = new \RDE\DNS\Controller\DomainController;
$mux->mount('/d', $o->expand());

$o = new \RDE\DNS\Controller\RecordController;
$mux->mount('/r', $o->expand());

return $mux;
