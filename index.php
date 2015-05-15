<?php
/*
	Plugin Name: Wordpress Debugbar
	Author: Dennis van der Velde
 */

global $wpdb;

if((WP_DEBUG == true) || (defined('WP_ENV') && WP_ENV == 'local')) {

	$autoload = require_once __DIR__ . '/vendor/autoload.php';

	require_once __DIR__ . '/helpers.php';

	return require_once 'src/init.php';
}
