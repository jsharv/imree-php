<?php


if(!isset($_GET['file_id']) OR !$_GET['file_id']) {
	header("location: http://google.com/");
}

//This is an injection to test if mod_rewrite is working for imree-php/file/abc in the setup.php process
if($_GET['file_id'] === 'abc') {
	die('1');
}
require_once('../config.php');

$original_request = explode("?",$_SERVER['REQUEST_URI']);
if(count($original_request) > 1) {
	$original_vars = explode("&", urldecode($original_request[1]));
	foreach($original_vars as $var) {
		$parts = explode("=", $var);
		if(isset($parts[1])) {
			$_GET[$parts[0]] = $parts[1];
		} else {
			$_GET[$parts[0]] = "";
		}
	}
}

imree_file($_GET['file_id'], (isset($_GET['size']) ? $_GET['size'] : false));

