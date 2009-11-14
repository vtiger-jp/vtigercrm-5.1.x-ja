<?php
/*********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 ********************************************************************************/

require_once('modules/Settings/MailScanner/core/MailScannerInfo.php');
require_once('modules/Settings/MailScanner/core/MailBox.php');

$newscannerinfo = new Vtiger_MailScannerInfo(false, false);
$newscannerinfo->scannername = trim($_REQUEST['mailboxinfo_scannername']);
$newscannerinfo->server     = trim($_REQUEST['mailboxinfo_server']);
$newscannerinfo->protocol   = trim($_REQUEST['mailboxinfo_protocol']);
$newscannerinfo->username   = trim($_REQUEST['mailboxinfo_username']);
$newscannerinfo->password   = trim($_REQUEST['mailboxinfo_password']);
$newscannerinfo->ssltype    = trim($_REQUEST['mailboxinfo_ssltype']);
$newscannerinfo->sslmethod  = trim($_REQUEST['mailboxinfo_sslmethod']);
$newscannerinfo->server     = trim($_REQUEST['mailboxinfo_server']);
$newscannerinfo->searchfor  = trim($_REQUEST['mailboxinfo_searchfor']);
$newscannerinfo->markas     = trim($_REQUEST['mailboxinfo_markas']);
$newscannerinfo->isvalid    =($_REQUEST['mailboxinfo_enable'] == 'true')? true : false;

// Rescan all folders on next run?
$rescanfolder = ($_REQUEST['mailboxinfo_rescan_folders'] == 'true')? true : false;

$isconnected = false;

$scannerinfo = new Vtiger_MailScannerInfo('DEFAULT');

if(!$scannerinfo->compare($newscannerinfo)) {
	$mailbox = new Vtiger_MailBox($newscannerinfo);

	$isconnected = $mailbox->connect();
	if($isconnected) $newscannerinfo->connecturl = $mailbox->_imapurl;

} else {
	$isconnected = true;
	$scannerinfo->isvalid = $newscannerinfo->isvalid; // Copy new value
	$newscannerinfo = $scannerinfo;
}

if(!$isconnected) {
	require_once('Smarty_setup.php');
	global $app_strings, $mod_strings, $currentModule, $theme, $current_language;

	$smarty = new vtigerCRM_Smarty;
	$smarty->assign("MOD", return_module_language($current_language,'Settings'));
	$smarty->assign("CMOD", $mod_strings);
	$smarty->assign("APP", $app_strings);
	$smarty->assign("THEME", $theme);
	$smarty->assign("IMAGE_PATH","themes/$theme/images/");

	$smarty->assign("SCANNERINFO", $newscannerinfo->getAsMap());
	$smarty->assign("CONNECTFAIL", "Connecting to mailbox failed!");
	$smarty->display('MailScanner/MailScannerEdit.tpl');	
} else {

	$mailServerChanged = $scannerinfo->update($newscannerinfo);
	
	$scannerinfo->updateAllFolderRescan($rescanfolder);

	// Update lastscan on all the available folders.
	if($mailServerChanged && $mailbox) {
		$folders = $mailbox->getFolders();
		foreach($folders as $folder) $scannerinfo->updateLastscan($folder);
	}

	require('modules/Settings/MailScanner/MailScannerInfo.php');
}
?>
