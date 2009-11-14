<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

global $app_strings;
global $currentModule,$image_path,$theme,$adb, $current_user;

require_once('Smarty_setup.php');
require_once("data/Tracker.php");
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/utils/utils.php');
require_once('modules/Calendar/Activity.php');

$log = LoggerManager::getLogger('Activity_Reminder');
$smarty = new vtigerCRM_Smarty;
if(isPermitted('Calendar','index') == 'yes'){
$active = $adb->pquery("select * from vtiger_users where id=?",array($current_user->id));
$active_res = $adb->query_result($active,0,'reminder_interval');
if($active_res!='None'){
	$reminder_next=$adb->query_result($active,0,"reminder_next_time");
	$lastlogin_time =$adb->query_result($active,0,"date_entered");
	if(empty($reminder_next)) {
		$reminder_next = date('Y-m-d H:i');
	}
	if(strtotime($lastlogin_time) > strtotime($reminder_next)) {
		$reminder_next = $lastlogin_time;
	}
	$reminder_next_time = strtotime($reminder_next);
	$cur_time = mktime(date('H'),date('i'),0,date('m'),date('d'),date('Y'));

	if($reminder_next_time <= $cur_time)
	{
		$cb_time=$adb->query_result($active,0,"reminder_interval");
		$cb_time = ConvertToMinutes($cb_time);
		$cb_mktime = strtotime($reminder_next);
		$cbdate1 = date('Y-m-d', strtotime("+$cb_time minutes", $cb_mktime));
		$cbtime1 = date('H:i',   strtotime("+$cb_time minutes", $cb_mktime));
		$callback_query = 
		"SELECT * FROM vtiger_activity_reminder_popup inner join vtiger_crmentity where " .
		" vtiger_activity_reminder_popup.status = 0 and " .
		" vtiger_activity_reminder_popup.recordid = vtiger_crmentity.crmid " .
		" and vtiger_crmentity.smownerid = ".$current_user->id." and vtiger_crmentity.deleted = 0 " .
		" and ((DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') < '" . $cbdate1 . "')" . 
		" OR ((DATE_FORMAT(vtiger_activity_reminder_popup.date_start,'%Y-%m-%d') = '" . $cbdate1 . "')" . 
		" AND (TIME_FORMAT(vtiger_activity_reminder_popup.time_start,'%H:%i') <= '" . $cbtime1 . "')))";

		$result = $adb->query($callback_query);

		$cbrows = $adb->num_rows($result);
		if($cbrows > 0) {
			for($index = 0; $index < $cbrows; ++$index) {
				$reminderid = $adb->query_result($result, $index, "reminderid");
				$cbrecord = $adb->query_result($result, $index, "recordid");
				$cbmodule = $adb->query_result($result, $index, "semodule");
 
				$focus = CRMEntity::getInstance($cbmodule);
								
				if($cbmodule == 'Calendar') {
					$focus->retrieve_entity_info($cbrecord,$cbmodule);
					
					$cbsubject = $focus->column_fields['subject'];
					$cbactivitytype   = $focus->column_fields['activitytype'];
					$cbdate   = $focus->column_fields["date_start"];
					$cbtime   = $focus->column_fields["time_start"];
				} else {
					// For non-calendar records.
					$cbsubject      = array_values(getEntityName($cbmodule, $cbrecord));
					$cbsubject      = $cbsubject[0];
					$cbactivitytype = getTranslatedString($cbmodule, $cbmodule);
					$cbdate         = $adb->query_result($result, $index, 'date_start');
					$cbtime         = $adb->query_result($result, $index, 'time_start');				
				}
				
				if($cbactivitytype=='Task')
					$cbstatus   = $focus->column_fields["taskstatus"];
				else
					$cbstatus   = $focus->column_fields["eventstatus"];
				// Appending recordid we can get unique callback dom id for that record.
				$popupid = "ActivityReminder_$cbrecord";
				if($cbdate <= date('Y-m-d')){
					if($cbdate == date('Y-m-d') && $cbtime > date('H:i')) $cbcolor = '';
					else $cbcolor= '#FF1515';
				}
				$smarty->assign("THEME", $theme);
				$smarty->assign("popupid", $popupid);
				$smarty->assign("APP", $app_strings);
				$smarty->assign("cbreminderid", $reminderid);
				$smarty->assign("cbdate", $cbdate);
				$smarty->assign("cbtime", $cbtime);
				$smarty->assign("cbsubject", $cbsubject);
				$smarty->assign("cbmodule", $cbmodule);
				$smarty->assign("cbrecord", $cbrecord);
				$smarty->assign("cbstatus", $cbstatus);
				$smarty->assign("cbcolor", $cbcolor);
				$smarty->assign("cblinkdtl", $cblinkdtl);
				$smarty->assign("activitytype", $cbactivitytype);
				$smarty->display("ActivityReminderCallback.tpl");

				$mark_reminder_as_read = "UPDATE vtiger_activity_reminder_popup set status = 1 where reminderid = ?";
				$adb->pquery($mark_reminder_as_read, array($reminderid));
			}
		}
		$reminder_next = date('Y-m-d H:i', strtotime("+$cb_time minutes", $cur_time));
		// NOTE date_entered has CURRENT_TIMESTAMP constraint, so we need to reset when updating the table 
		$adb->pquery("UPDATE vtiger_users set reminder_next_time=?, date_entered=? where id=?",array($reminder_next, $lastlogin_time, $current_user->id));
		$reminder_interval_reset = (strtotime($reminder_next) - $cur_time) * 1000;
		
		// NOTE (Overcome Browser's issue): If required comment out the following line if condition below. 
		// To make popup work fine when interval is set to 1 minute, we need decrement the timeout
		// if($reminder_interval_reset / 1000 == 60) $reminder_interval_reset = (40 * 1000);
		
		echo "<script type='text/javascript' id='_vtiger_activityreminder_callback_interval_'>$reminder_interval_reset</script>";
	}
}
}

?>