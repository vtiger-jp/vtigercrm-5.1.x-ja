<?php
/*+********************************************************************************
  * The contents of this file are subject to the vtiger CRM Public License Version 1.0
  * ("License"); You may not use this file except in compliance with the License
  * The Original Code is:  vtiger CRM Open Source
  * The Initial Developer of the Original Code is vtiger.
  * Portions created by vtiger are Copyright (C) vtiger.
  * All Rights Reserved.
  ********************************************************************************/

require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
global $app_strings;
global $list_max_entries_per_page;
global $currentModule, $current_user;
if($current_user->is_admin != 'on')
{
        die("<br><br><center>".$app_strings['LBL_PERMISSION']." <a href='javascript:window.history.back()'>".$app_strings['LBL_GO_BACK'].".</a></center>");
}

$log = LoggerManager::getLogger('user_list');

global $mod_strings,$adb;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $current_language;
$mod_strings = return_module_language($current_language,'Users');
$category = getParentTab();
$focus = new Users();
$no_of_users=UserCount();

//Display the mail send status
$smarty = new vtigerCRM_Smarty;
if($_REQUEST['mail_error'] != '')
{
    require_once("modules/Emails/mail.php");
    $error_msg = strip_tags(parseEmailErrorString($_REQUEST['mail_error']));
	$error_msg = $app_strings['LBL_MAIL_NOT_SENT_TO_USER']. ' ' . vtlib_purify($_REQUEST['user']). '. ' .$app_strings['LBL_PLS_CHECK_EMAIL_N_SERVER'];
	$smarty->assign("ERROR_MSG",$mod_strings['LBL_MAIL_SEND_STATUS'].' <b><font class="warning">'.$error_msg.'</font></b>');
}

//Retreiving the start value from request
if(isset($_REQUEST['start']) && $_REQUEST['start'] != '') {
	$start = vtlib_purify($_REQUEST['start']);
} elseif($_SESSION['user_pagestart'] != '') {
	$start = $_SESSION['user_pagestart'];
} else
	$start=1;

$list_query = getListQuery("Users");

	$userid = array(); 
	$userid_Query = "SELECT id,user_name FROM vtiger_users WHERE user_name IN ('admin')";
	$users = $adb->pquery($userid_Query,array());
	$norows = $adb->num_rows($users);
	if($norows  > 0){
		for($i=0;$i<$norows ;$i++){
			$id = $adb->query_result($users,$i,'id');
			$userid[$id] = $adb->query_result($users,$i,'user_name');
		}
	}
	$smarty->assign("USERNODELETE",$userid);
$_SESSION['user_pagestart'] = $start;
if($_REQUEST['sorder'] !='')
	$sorder = $adb->sql_escape_string($_REQUEST['sorder']);
elseif($_SESSION['user_sorder'] != '')
	$sorder = $adb->sql_escape_string($_SESSION['user_sorder']);
else
	$sorder = 'ASC';
$_SESSION['user_sorder'] = $sorder;
if($_REQUEST['order_by'] != '')
	$order_by = $adb->sql_escape_string($_REQUEST['order_by']);
elseif($_SESSION['user_orderby'] != '')
	$order_by = $adb->sql_escape_string($_SESSION['user_orderby']);
else
	$order_by = 'last_name';
$_SESSION['user_orderby'] = $orderby;
$list_query .= ' ORDER BY '.$order_by.' '.$sorder;
$list_result = $adb->query($list_query);
//Retreive the Navigation array
$navigation_array = getNavigationValues($start, $adb->num_rows($list_result), $no_of_users['user']);

$navigationOutput = getTableHeaderNavigation($navigation_array, $url_string,"Administration","index",'');
$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("CURRENT_USERID", $current_user->id);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("CATEGORY",$category);
$smarty->assign("LIST_HEADER",getListViewHeader($focus,"Users",$url_string,$sorder,$order_by,"",""));
$smarty->assign("LIST_ENTRIES",getListViewEntries($focus,"Users",$list_result,$navigation_array,"","","EditView","Delete",""));
$smarty->assign("USER_COUNT",$no_of_users);
$smarty->assign("RECORD_COUNTS", $record_string);
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("USER_IMAGES",getUserImageNames());
if($_REQUEST['ajax'] !='')
	$smarty->display("UserListViewContents.tpl");
else
	$smarty->display("UserListView.tpl");

?>