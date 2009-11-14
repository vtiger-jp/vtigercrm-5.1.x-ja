<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/

require_once('include/database/Postgres8.php');
require_once('Smarty_setup.php');
require_once('modules/Vendors/Vendors.php');
require_once('include/ListView/ListView.php');
require_once('include/utils/utils.php');
require_once('modules/CustomView/CustomView.php');

global $app_strings,$mod_strings,$list_max_entries_per_page,$currentModule,$theme,$adb;

$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";

$smarty = new vtigerCRM_Smarty;
$smarty->assign("MOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("THEME", $theme);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("MODULE",$currentModule);
$smarty->assign("SINGLE_MOD",'Vendor');
$category = getParentTab();
$smarty->assign("CATEGORY",$category);

$focus = new Vendors();
// Initialize sort by fields
$focus->initSortbyField('Vendors');
// END
$other_text = Array();

if(!$_SESSION['lvs'][$currentModule])
{
	unset($_SESSION['lvs']);
	$modObj = new ListViewSession();
	$modObj->sorder = $sorder;
	$modObj->sortby = $order_by;
	$_SESSION['lvs'][$currentModule] = get_object_vars($modObj);
}

if($_REQUEST['errormsg'] != '')
{
        $errormsg = vtlib_purify($_REQUEST['errormsg']);
        $smarty->assign("ERROR","The User does not have permission to delete ".$errormsg." ".$currentModule);
}else
{
        $smarty->assign("ERROR","");
}
if (!isset($where)) $where = "";

//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>
if($_REQUEST['order_by'] != '')
	$order_by = $adb->sql_escape_string($_REQUEST['order_by']);
else
	$order_by = (($_SESSION['VENDORS_ORDER_BY'] != '')?($_SESSION['VENDORS_ORDER_BY']):($focus->default_order_by));

if($_REQUEST['sorder'] != '')
	$sorder = $adb->sql_escape_string($_REQUEST['sorder']);
else
	$sorder = (($_SESSION['VENDORS_SORT_ORDER'] != '')?($_SESSION['VENDORS_SORT_ORDER']):($focus->default_sort_order));

$_SESSION['VENDORS_ORDER_BY'] = $order_by;
$_SESSION['VENDORS_SORT_ORDER'] = $sorder;
//<<<<<<<<<<<<<<<<<<< sorting - stored in session >>>>>>>>>>>>>>>>>>>>

if(isset($_REQUEST['query']) && $_REQUEST['query'] != '' && $_REQUEST['query'] == 'true')
{
	list($where, $ustring) = split("#@@#",getWhereCondition($currentModule));
	// we have a query
	$url_string .="&query=true".$ustring;
	$log->info("Here is the where clause for the list view: $where");
	$smarty->assign("SEARCH_URL",$url_string);

}

//<<<<cutomview>>>>>>>
$oCustomView = new CustomView("Vendors");
$viewid = $oCustomView->getViewId($currentModule);
$customviewcombo_html = $oCustomView->getCustomViewCombo($viewid);
$viewnamedesc = $oCustomView->getCustomViewByCvid($viewid);

//Added to handle approving or denying status-public by the admin in CustomView
$statusdetails = $oCustomView->isPermittedChangeStatus($viewnamedesc['status']);
$smarty->assign("CUSTOMVIEW_PERMISSION",$statusdetails);

//To check if a user is able to edit/delete a customview
$edit_permit = $oCustomView->isPermittedCustomView($viewid,'EditView',$currentModule);
$delete_permit = $oCustomView->isPermittedCustomView($viewid,'Delete',$currentModule);
$smarty->assign("CV_EDIT_PERMIT",$edit_permit);
$smarty->assign("CV_DELETE_PERMIT",$delete_permit);

//<<<<<customview>>>>>

// Buttons and View options
if(isPermitted('Vendors','DeleteVendor','') == 'yes')
{
	$other_text['del'] = $app_strings[LBL_MASS_DELETE];
}
if(isPermitted('Vendors','EditView','') == 'yes')
	$other_text['mass_edit'] = $app_strings[LBL_MASS_EDIT];

if($viewnamedesc['viewname'] == 'All')
{
	$smarty->assign("ALL", 'All');
}
if($viewid ==0)
{
	echo "<table border='0' cellpadding='5' cellspacing='0' width='100%' height='450px'><tr><td align='center'>";
	echo "<div style='border: 3px solid rgb(153, 153, 153); background-color: rgb(255, 255, 255); width: 55%; position: relative; z-index: 10000000;'>

		<table border='0' cellpadding='5' cellspacing='0' width='98%'>
		<tbody><tr>
		<td rowspan='2' width='11%'><img src='". vtiger_imageurl('denied.gif', $theme) ."' ></td>
		<td style='border-bottom: 1px solid rgb(204, 204, 204);' nowrap='nowrap' width='70%'><span clas
		s='genHeaderSmall'>$app_strings[LBL_PERMISSION]</span></td>
		</tr>
		<tr>
		<td class='small' align='right' nowrap='nowrap'>
		<a href='javascript:window.history.back();'>$app_strings[LBL_GO_BACK]</a><br>
		</td>
		</tr>
		</tbody></table>
		</div>";
	echo "</td></tr></table>";
	exit;
}


//Retreive the list from Database
//<<<<<<<<<customview>>>>>>>>>
if($viewid != "0")
{
	$listquery = getListQuery("Vendors");
	$list_query = $oCustomView->getModifiedCvListQuery($viewid,$listquery,"Vendors");
}else
{
	$list_query = getListQuery("Vendors");
}
//<<<<<<<<customview>>>>>>>>>

if(isset($where) && $where != '')
{
        $list_query .= ' and '.$where;
        $_SESSION['export_where'] = $where;
}
else
   unset($_SESSION['export_where']);


if(isset($order_by) && $order_by != '')
{
	$tablename = getTableNameForField('Vendors',$order_by);
	$tablename = (($tablename != '')?($tablename."."):'');

	if( $adb->dbType == "pgsql")
 	    $list_query .= ' GROUP BY '.$tablename.$order_by;
	
        $list_query .= ' ORDER BY '.$tablename.$order_by.' '.$sorder;
}

//Postgres 8 fixes
if( $adb->dbType == "pgsql")
	$list_query = fixPostgresQuery( $list_query, $log, 0);

if(PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false) === true){
	$count_result = $adb->query( mkCountQuery( $list_query));
	$noofrows = $adb->query_result($count_result,0,"count");
}else{
	$noofrows = null;
}

$queryMode = (isset($_REQUEST['query']) && $_REQUEST['query'] == 'true');
$start = ListViewSession::getRequestCurrentPage($currentModule, $list_query, $viewid, $queryMode);

$navigation_array = VT_getSimpleNavigationValues($start,$list_max_entries_per_page,$noofrows);

$limit_start_rec = ($start-1) * $list_max_entries_per_page;

if( $adb->dbType == "pgsql")
	$list_result = $adb->pquery($list_query. " OFFSET $limit_start_rec LIMIT $list_max_entries_per_page", array());
else
	$list_result = $adb->pquery($list_query. " LIMIT $limit_start_rec, $list_max_entries_per_page", array());

$recordListRangeMsg = getRecordRangeMessage($list_result, $limit_start_rec);
$smarty->assign('recordListRange',$recordListRangeMsg);

//Retreive the List View Table Header
if($viewid !='')
$url_string .="&viewname=".$viewid;

$listview_header = getListViewHeader($focus,"Vendors",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("LISTHEADER", $listview_header);

$listview_header_search = getSearchListHeaderValues($focus,"Vendors",$url_string,$sorder,$order_by,"",$oCustomView);
$smarty->assign("SEARCHLISTHEADER",$listview_header_search);

$listview_entries = getListViewEntries($focus,"Vendors",$list_result,$navigation_array,'','&return_module=Vendors&return_action=index','EditView','Delete',$oCustomView);
$smarty->assign("LISTENTITY", $listview_entries);

$smarty->assign("AVALABLE_FIELDS", getMergeFields($currentModule,"available_fields"));
$smarty->assign("FIELDS_TO_MERGE", getMergeFields($currentModule,"fileds_to_merge"));

//Added to select Multiple records in multiple pages
$smarty->assign("SELECTEDIDS", vtlib_purify($_REQUEST['selobjs']));
$smarty->assign("ALLSELECTEDIDS", vtlib_purify($_REQUEST['allselobjs']));
$smarty->assign("CURRENT_PAGE_BOXES", implode(array_keys($listview_entries),";"));

$smarty->assign("CUSTOMVIEW_OPTION",$customviewcombo_html);
$smarty->assign("VIEWID", $viewid);
$navigationOutput = getTableHeaderSimpleNavigation($navigation_array, $url_string,"Vendors","index",$viewid);
$alphabetical = AlphabeticalSearch($currentModule,'index','vendorname','true','basic',"","","","",$viewid);
$fieldnames = getAdvSearchfields($currentModule);
$criteria = getcriteria_options();
$smarty->assign("CRITERIA", $criteria);
$smarty->assign("FIELDNAMES", $fieldnames);
$smarty->assign("ALPHABETICAL", $alphabetical);
$smarty->assign("NAVIGATION", $navigationOutput);
$smarty->assign("RECORD_COUNTS", $record_string);
$smarty->assign("BUTTONS", $other_text);

$check_button = Button_Check($currentModule);
$smarty->assign("CHECK", $check_button);

$_SESSION[$currentModule.'_listquery'] = $list_query;

if(isset($_REQUEST['ajax']) && $_REQUEST['ajax'] != '')
	$smarty->display("ListViewEntries.tpl");
else	
	$smarty->display("ListView.tpl");
?>