<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
	      
require_once('include/logging.php');
require_once('modules/Users/Users.php');
require_once('include/database/PearDatabase.php');
global $adb;

$local_log =& LoggerManager::getLogger('UsersAjax');
$ajaxaction = $_REQUEST["ajxaction"];
if($ajaxaction == "DETAILVIEW")
{
	$crmid = $_REQUEST["recordid"];
	$tablename = $_REQUEST["tableName"];
	$fieldname = $_REQUEST["fldName"];
	$fieldvalue = utf8RawUrlDecode($_REQUEST["fieldValue"]); 
	if($crmid != "")
	{
		$userObj = new Users();
		$userObj->retrieve_entity_info($crmid,"Users");
		$userObj->column_fields[$fieldname] = $fieldvalue;
		if($fieldname == 'internal_mailer'){
			
			if(isset($_SESSION['internal_mailer']) && $_SESSION['internal_mailer'] != $userObj->column_fields['internal_mailer'])
				$_SESSION['internal_mailer'] = $userObj->column_fields['internal_mailer'];
		}
		$userObj->id = $crmid;
		$userObj->mode = "edit";
		$userObj->save("Users");
		if($userObj->id != "")
		{
			echo ":#:SUCCESS";
		}else
		{
			echo ":#:FAILURE";
		}   
	}else
	{
		echo ":#:FAILURE";
	}
}
?>