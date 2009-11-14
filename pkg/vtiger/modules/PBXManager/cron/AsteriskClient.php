#!/usr/bin/php
<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/**
 * this file will be run as a shell script (in linux) or a batch file (under windows).
 * the purpose of the file is to create a master socket which will be connecting to the asterisk server
 * and to keep it (the socket) alive all the time. 
 */

ini_set("include_path", "../../../");
require_once('modules/PBXManager/utils/AsteriskClass.php');
require_once('config.php');
require_once('include/utils/utils.php');
require_once('include/language/en_us.lang.php');
require_once('modules/PBXManager/AsteriskUtils.php');

asteriskClient();

/**
 * this function defines the asterisk client
 */
function asteriskClient(){
	global $app_strings, $current_user;
	global $adb, $log;
	
	$data = getAsteriskInfo($adb);
	$server = $data['server'];
	$port = $data['port'];
	$username = $data['username'];
	$password = $data['password'];
	$version = $data['version'];

	$errno = $errstr = NULL;
	$sock = @fsockopen($server, $port, $errno, $errstr, 1);
	stream_set_blocking($sock, false);
	if( $sock === false ) {
		echo "Socket cannot be created due to error: $errno:  $errstr\n";
		$log->debug("Socket cannot be created due to error:   $errno:  $errstr\n");
		exit(0);
	}else{
		echo "Date: ".date("d-m-Y")."\n";
		echo "Connecting to asterisk server.....\n";
		$log->debug("Connecting to asterisk server.....\n");
	}
	echo "Connected successfully\n\n\n";
	$asterisk = new Asterisk($sock, $server, $port);

	authorizeUser($username, $password, $asterisk);
		
	//keep looping continuosly to check if there are any calls
	while (true) {
		//check for incoming calls and insert in the database
		sleep(2);
		$incoming = handleIncomingCalls($asterisk, $adb, $version);
	}
	fclose($sock);
	unset($sock);
}

/**
 * this function checks if there are any incoming calls for the current user
 * if any call is found, it just inserts the values into the vtiger_asteriskincomingcalls table
 * 
 * @param $asterisk - the asterisk object
 * @param $adb - the peardatabase type object
 * @return	incoming call information if successful
 * 			false if unsuccessful
 */
function handleIncomingCalls($asterisk, $adb, $version="1.4"){
	$response = $asterisk->getAsteriskResponse();
	if(empty($response)){
		return false;
	}
	$callerNumber = "Unknown";
	$callerName = "Unknown";
	
	//event can be both newstate and newchannel :: this is an asterisk bug and can be found at
	//http://lists.digium.com/pipermail/asterisk-dev/2006-July/021565.html
	if($version == "1.6"){
		$state = "ChannelStateDesc";
	}else{
		$state = "State";
	}
	
	if(($response['Event'] == 'Newstate' || $response['Event'] == 'Newchannel') && ($response[$state] == 'Ring' || $response[$state] == 'Ringing')){
		//get the caller information
		if(!empty($response['CallerID'])){
			$callerNumber = $response['CallerID'];
		}elseif(!empty($response['CallerIDNum'])){
			$callerNumber = $response['CallerIDNum'];
		}
		if(!empty($response['CallerIDName'])){
			$callerName = $response['CallerIDName'];
		}
		while(true){
			$response = $asterisk->getAsteriskResponse();
			if(($response['Event'] == 'Newexten') && (strstr($response['AppData'],"__DIALED_NUMBER") || strstr($response['AppData'],"EXTTOCALL"))){
				$temp = array();
				if(strstr($response['Channel'], $callerNumber)){
					$temp = explode("/",$response['Channel']);
					$callerType = $temp[0];
				}
				$temp = explode("=",$response['AppData']);
				$extension = $temp[1];
				
				if(checkExtension($extension, $adb)){
					//insert into database
					$sql = "insert into vtiger_asteriskincomingcalls values (?,?,?,?,?,?)";
					$flag= 0;
					$timer = time();
					$params = array($callerNumber, $callerName, $extension, $callerType,$flag,$timer);
					$adb->pquery($sql, $params);
					
					addToCallHistory($extension, $callerType.":".$callerNumber, $extension, "incoming", $adb);
					break;	//break the while loop
				}
			}
		}
	}else{
		return false;
	}
}


/**
 * this function takes a XML response and converts it to an array format
 * @param string $response - the xml response
 * @return the xml formatted into an array
 */
function getArray($xml){
	$lines = explode("\r\n", $xml);

	$response = array();
	foreach($lines as $line){
		list($key, $value) = explode(":", $line);
		$response[$key] = $value;
	}
	return $response;	
}

/**
 * this function checks if the given extension is a valid vtiger extension or not
 * if yes it returns true
 * if not it returns false
 * 
 * @param string $ext - the extension to be checked
 * @param object $adb - the peardatabase object
 */
function checkExtension($ext, $adb){
	$sql = "select * from vtiger_asteriskextensions where asterisk_extension='$ext'";
	$result = $adb->pquery($sql, array());
	
	if($adb->num_rows($result)>0){
		return true;
	}else{
		return false;
	}
}
?>
