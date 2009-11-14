<?php
/*+*******************************************************************************
 *  The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 *********************************************************************************/
session_start();
if($_SESSION['authentication_key'] != $_REQUEST['auth_key']) {
	die("Not Authorized to perform this operation");
}
if(!empty($_REQUEST['rootUserName'])) $_SESSION['migration_info']['root_username'] = $_REQUEST['rootUserName'];

if(!empty($_REQUEST['rootPassword'])) {
	$_SESSION['migration_info']['root_password'] = $_REQUEST['rootPassword'];
} else {
	$_SESSION['migration_info']['root_password'] = '';
}


require_once 'include/db_backup/DatabaseBackup.php';
$mode = $_REQUEST['mode'];

$source_directory = $_SESSION['migration_info']['source_directory'];
require_once $source_directory.'config.inc.php';
$createDB = $_REQUEST['createDB'];
if(empty($createDB)){
	$createDB = false;
}else{
	$createDB = true;
}

$backup = new DatabaseBackup($dbconfig['db_type'],$createDB);
$hostName = $dbconfig['db_server'].$dbconfig['db_port'];
$username = $dbconfig['db_username'];
$password = $dbconfig['db_password'];
$dbName = $dbconfig['db_name'];
$backup->setSourceConfig(new DatabaseConfig($hostName,$username,$password,$dbName));
if(strtolower($mode) == 'dump'){
	header('Content-type: text/plain; charset=UTF-8');
	$backup->enableDownloadMode();
	$backup->backup();
}else{
	$targetName = $_REQUEST['newDatabaseName'];
	try{
		if(!empty($targetName) && $dbName != $targetName){
			$rootUserName = $_SESSION['migration_info']['root_username'];
			$rootPassword = $_SESSION['migration_info']['root_password'];
			$backup->setDestinationConfig(new DatabaseConfig($hostName,$username,$password,$targetName,$rootUserName,$rootPassword));
			$backup->backup();
			$_SESSION['migration_info']['new_dbname'] = $targetName;
			if ($createDB && $backup->isUTF8SupportEnabled()) {
				$_SESSION['migration_info']['db_utf8_support'] = true;
			}
			echo 'true';
			return;
		}
		echo 'false';
	}catch (DatabaseBackupException $e){
		echo 'false';
	}catch(Exception $e){
		echo 'false';
	}
}

?>