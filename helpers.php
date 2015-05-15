<?php

if(!function_exists('globals')) {		
	function globals($key) {
		return $GLOBALS[$key];
	}
}

/**
 * Dumps a variable to the debugbar
 */
if(!function_exists('debug')) {

	function debug($value) {
	       $debugbar = globals('debugbar');
	       foreach (func_get_args() as $value) {
	           $debugbar->addMessage($value, 'debug');
	       }
	   }

}

/**
 * Same as debug(), but red
 * @see debug
 */
if(!function_exists('error')) {

	function error($value) {
	       $debugbar = globals('debugbar');
	       foreach (func_get_args() as $value) {
	           $debugbar->addMessage($value, 'error');
	       }
	   }

}

/**
 * Same as debug(), but yellow
 * @see  debug 
 */
if(!function_exists('warning')) {

	function warning($value) {
	       $debugbar = globals('debugbar');
	       foreach (func_get_args() as $value) {
	           $debugbar->addMessage($value, 'warning');
	       }
	   }

}


if(!function_exists('addQuery')) {

	function addQuery($value) {
	       $debugbar = globals('debugbar');
	       foreach (func_get_args() as $value) {
	           $debugbar->addQuery($value, 'queries');
	       }
	   }

}


if(!function_exists('d')) {
	function d() {

		array_map(function($x) {
			echo '<pre>';
			echo var_dump($x, true);
			echo '</pre>';
		}, func_get_args());
	}
}

if(!function_exists('dd')) {
	function dd() {

		array_map(function($x) {
			echo '<pre>';
			echo print_r($x, true);
			echo '</pre>';
		}, func_get_args()); die;
	}
}
