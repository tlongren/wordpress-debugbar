<?php namespace Dennie170\DebugBar;
use Dennie170\DebugBar\DebugbarInit;

$debugbarRenderer = null;


if(file_exists($a = __DIR__ . '/../config.php')) {
	
	$config = (object) require_once($a); 
} else { 
	$config = new \StdClass();
}


$debugbar = new DebugbarInit($config);


$debugbar = $GLOBALS['debugbar'] = $debugbar->getDebugBar();

$debugbarRenderer = $debugbar->getJavascriptRenderer();

