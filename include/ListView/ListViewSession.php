<?php
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is:  vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/

require_once('include/logging.php');
require_once('modules/CustomView/CustomView.php');

class ListViewSession {

	var $module = null;
	var $viewname = null;
	var $start = null;
	var $sorder = null;
	var $sortby = null;
	var $page_view = null;

/**initializes ListViewSession 
 * Portions created by vtigerCRM are Copyright (C) vtigerCRM.
 * All Rights Reserved.
*/

	function ListViewSession()
	{
		global $log,$currentModule;
		$log->debug("Entering ListViewSession() method ...");
		
		$this->module = $currentModule;
		$this->sortby = 'ASC';
		$this->start =1;
	}
	
	function getCurrentPage($currentModule,$viewId){
		if(!empty($_SESSION['lvs'][$currentModule][$viewId]['start'])){
			return $_SESSION['lvs'][$currentModule][$viewId]['start'];
		}
		return 1;
	}
	
	function getRequestStartPage(){
		$start = $_REQUEST['start'];
		if(!is_numeric($start)){
			$start = 1;
		}
		if($start < 1){
			$start = 1;
		}
		$start = ceil($start);
		return $start;
	}
	
	function getListViewNavigation($currentRecordId){
		global $currentModule,$current_user,$adb,$log,$list_max_entries_per_page;
		Zend_Json::$useBuiltinEncoderDecoder = true;
		$reUseData = false;
		$displayBufferRecordCount = 10;
		$bufferRecordCount = 15;
		if($currentModule == 'Documents'){
			$sql = "select folderid from vtiger_notes where notesid=?";
			$params = array($currentRecordId);
			$result = $adb->pquery($sql,$params);
			$folderId = $adb->query_result($result,0,'folderid');
		}
		$cv = new CustomView();
		$viewId = $cv->getViewId($currentModule);
		if(!empty($_SESSION[$currentModule.'_DetailView_Navigation'.$viewId])){
			$recordNavigationInfo = Zend_Json::decode($_SESSION[$currentModule.'_DetailView_Navigation'.$viewId]);
			$pageNumber =0;
			if(count($recordNavigationInfo) == 1){
				foreach ($recordNavigationInfo as $recordIdList) {
					if(in_array($currentRecordId,$recordIdList)){
						$reUseData = true;
					}
				}
			}else{
				$recordList = array();
				$recordPageMapping = array();
				foreach ($recordNavigationInfo as $start=>$recordIdList){
					foreach ($recordIdList as $index=>$recordId) {
						$recordList[] = $recordId;
						$recordPageMapping[$recordId] = $start;
						if($recordId == $currentRecordId){
							$searchKey = count($recordList)-1;
						}
					}
				}
				if($searchKey > $displayBufferRecordCount -1 && $searchKey < count($recordList)-$displayBufferRecordCount){
					$reUseData= true;
				}
			}
		}
		
		if($reUseData === false){
			$recordNavigationInfo = array();
			if(!empty($_REQUEST['start'])){
				$start = ListViewSession::getRequestStartPage();
			}else{
				$start = ListViewSession::getCurrentPage($currentModule,$viewId);
			}
			$startRecord = (($start - 1) * $list_max_entries_per_page) - $bufferRecordCount;
			if($startRecord < 0){
				$startRecord = 0;
			}
			
			$list_query = $_SESSION[$currentModule.'_listquery'];
			if($currentModule=='Documents' && !empty($folderId)){
				$list_query = preg_replace("/[\n\r\s]+/"," ",$list_query);
				$findOrderByPosition = stripos($list_query,' ORDER BY ');
				if ($findOrderByPosition > 0) {
					$orderByClause = substr($list_query, $findOrderByPosition, strlen($list_query));
					$list_query = substr($list_query, 0, $findOrderByPosition). " AND vtiger_notes.folderid=$folderId " . $orderByClause;
				} else {
					$list_query .= " AND vtiger_notes.folderid=$folderId";
				}
			}
			if($start !=1){
				$recordCount = ($list_max_entries_per_page+2 * $bufferRecordCount);
			}else{
				$recordCount = ($list_max_entries_per_page+ $bufferRecordCount);
			}
			if( $adb->dbType == "pgsql"){
				$list_query .= " OFFSET $startRecord LIMIT $recordCount";
			}else{
				$list_query .= " LIMIT $startRecord, $recordCount";
			}
			
			$resultAllCRMIDlist_query=$adb->pquery($list_query,array());
			$navigationRecordList = array();
			while($forAllCRMID = $adb->fetch_array($resultAllCRMIDlist_query)) {
				$navigationRecordList[] = $forAllCRMID['crmid'];
			}
			
			$pageCount = 0;
			$current = $start;
			if($start ==1){
				$firstPageRecordCount = $list_max_entries_per_page;
			}else{
				$firstPageRecordCount = $bufferRecordCount;
				$current -=1;
			}
			
			$searchKey = array_search($currentRecordId,$navigationRecordList);
			$recordNavigationInfo = array();
			if($searchKey !== false){
				foreach ($navigationRecordList as $index => $recordId) {
					if(!is_array($recordNavigationInfo[$current])){
						$recordNavigationInfo[$current] = array();
					}
					if($index == $firstPageRecordCount  || $index == ($firstPageRecordCount+$pageCount * $list_max_entries_per_page)){
						$current++;
						$pageCount++;
					}
					$recordNavigationInfo[$current][] = $recordId;
				}
			}
			$_SESSION[$currentModule.'_DetailView_Navigation'.$viewId] = 
				Zend_Json::encode($recordNavigationInfo);
		}
		return $recordNavigationInfo;
	}
	
	function getRequestCurrentPage($currentModule, $query, $viewid, $queryMode = false) {
		global $list_max_entries_per_page, $adb;
		$start = 1;
		if(isset($_REQUEST['query']) && $_REQUEST['query'] == 'true'){
			return ListViewSession::getRequestStartPage();
		}
		if(!empty($_REQUEST['start'])){
			$start = $_REQUEST['start'];
			if($start == 'last'){
				$count_result = $adb->query( mkCountQuery( $query));
				$noofrows = $adb->query_result($count_result,0,"count");				
				if($noofrows > 0){
					$start = ceil($noofrows/$list_max_entries_per_page);
				}
			}
			if(!is_numeric($start)){
				$start = 1;
			}elseif($start < 1){
				$start = 1;
			}
			$start = ceil($start);
		}else if(!empty($_SESSION['lvs'][$currentModule][$viewid]['start'])){
			$start = $_SESSION['lvs'][$currentModule][$viewid]['start'];
		}
		if(!$queryMode) {
			$_SESSION['lvs'][$currentModule][$viewid]['start'] = intval($start);
		}
		return $start;
	}
	
}
?>