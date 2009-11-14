<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
 
echo TraceIncomingCall();

function TraceIncomingCall(){
	require_once 'include/utils/utils.php';
	global $adb, $current_user;
	global $theme,$app_strings;

	$theme_path="themes/".$theme."/";
	$image_path=$theme_path."images/";
	
	$sql = "select * from vtiger_asteriskextensions where userid = ".$current_user->id;
	$result = $adb->query($sql);
	$asterisk_extension = $adb->query_result($result, 0, "asterisk_extension");
	
	$query = "select * from vtiger_asteriskincomingcalls where to_number = ".$asterisk_extension;
	$result = $adb->query($query);
	
	if($adb->num_rows($result)>0){
		$callerNumber = $adb->query_result($result,0,"from_number");
		$callerName = $adb->query_result($result,0,"from_name");
		$callerType = $adb->query_result($result,0,"callertype");
		
		if(!empty($callerType)){
			$caller = getCallerName("$callerType:".$callerNumber);
		}else{
			$caller = getCallerName($callerNumber);
		}
	
		$adb->query("delete from vtiger_asteriskincomingcalls where to_number = ".$asterisk_extension);
		
		//prepare the div for incoming calls
		$status = "	<table  border='0' cellpadding='5' cellspacing='0'>
						<tr>
							<td style='padding:10px;' colspan='2'><b>Incoming Call</b></td>
						</tr>
					</table>
					<table  border='0' cellpadding='0' cellspacing='0' class='hdrNameBg'>
						<tr><td style='padding:10px;' colspan='2'><b>Caller Information</b>
							<br><b>Number:</b> $callerNumber
							<br><b>Name:</b> $callerName
						</td></tr>
						<tr><td style='padding:10px;' colspan='2'><b>Information from vtigerCRM</b>
							<br>$caller
						</td></tr>
					</table>";
		
		require_once 'modules/Calendar/Activity.php';
		$focus = new Activity();
		$focus->column_fields['subject'] = "Incoming call from $callerName ($callerNumber)";
		$focus->column_fields['activitytype'] = "Call";
		$focus->column_fields['date_start'] = date('Y-m-d');
		$focus->column_fields['due_date'] = date('Y-m-d');
		$focus->column_fields['time_start'] = date('H:i');
		$focus->column_fields['time_end'] = date('H:i');
		$focus->column_fields['eventstatus'] = "Held";
		$focus->column_fields['assigned_user_id'] = $current_user->id;
		$focus->save('Calendar');
		
		$callerInfo = getCallerInfo("$callerType:".$callerNumber);
		if($callerInfo == false){
			$callerInfo = getCallerInfo(getStrippedNumber($callerNumber));
		}
		if($callerInfo != false){
			$tablename = array('Contacts'=>'vtiger_cntactivityrel', 'Accounts'=>'vtiger_seactivityrel', 'Leads'=>'vtiger_seactivityrel');
			$sql = "insert into ".$tablename[$callerInfo['module']]." values (?,?)";
			$params = array($callerInfo[id], $focus->id);
			$adb->pquery($sql, $params);
		}
	}else{
		$status = "failure";
	}
	
	return $status;
}

?>
