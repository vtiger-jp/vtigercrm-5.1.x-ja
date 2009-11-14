<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include('adodb/adodb.inc.php');

if(version_compare(phpversion(), '5.0') < 0) {
        require_once('phpversionfail.php');
        die();
}

/** Function to  return a string with backslashes stripped off
 * @param $value -- value:: Type string
 * @returns $value -- value:: Type string array
 */
 function stripslashes_checkstrings($value){
 	if(is_string($value)){
 		return stripslashes($value);
 	}
 	return $value;

 }
 if(get_magic_quotes_gpc() == 1){
 	$_REQUEST = array_map("stripslashes_checkstrings", $_REQUEST);
	$_POST = array_map("stripslashes_checkstrings", $_POST);
	$_GET = array_map("stripslashes_checkstrings", $_GET);

}

//Run command line if no web var detected
if (!isset($_SERVER['REQUEST_METHOD'])) {
	require('install/CreateTables.inc.php');
	exit;
}
			
if (!empty($_REQUEST['file'])) $the_file = $_REQUEST['file'];
else $the_file = "welcome.php";

installCheckFileAccess("install/".$the_file);
include("install/".$the_file);

/** Function to check the file access is made within web root directory. */
function installCheckFileAccess($filepath) {
	global $root_directory;
	// Set the base directory to compare with
	$use_root_directory = $root_directory;
	if(empty($use_root_directory)) {
		$use_root_directory = realpath(dirname(__FILE__).'/.');
	}

	$realfilepath = realpath($filepath);

	/** Replace all \\ with \ first */
	$realfilepath = str_replace('\\\\', '\\', $realfilepath);
	$rootdirpath  = str_replace('\\\\', '\\', $use_root_directory);

	/** Replace all \ with / now */
	$realfilepath = str_replace('\\', '/', $realfilepath);
	$rootdirpath  = str_replace('\\', '/', $rootdirpath);
	
	if(stripos($realfilepath, $rootdirpath) !== 0) {
		die("Sorry! Attempt to access restricted file.");
	}
}
?>
