<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once('include/database/PearDatabase.php');
require_once("modules/Users/Users.php");
require_once 'include/Webservices/WebserviceField.php';
require_once 'include/Webservices/EntityMeta.php';
require_once 'include/Webservices/VtigerWebserviceObject.php';
require_once("include/Webservices/VtigerCRMObject.php");
require_once("include/Webservices/VtigerCRMObjectMeta.php");
require_once("include/Webservices/DataTransform.php");
require_once("include/Webservices/WebServiceError.php");
require_once 'include/utils/utils.php';
require_once 'include/utils/UserInfoUtil.php';
require_once 'include/Webservices/ModuleTypes.php';
require_once 'include/utils/VtlibUtils.php';
require_once 'include/Webservices/WebserviceEntityOperation.php';
require_once 'include/Webservices/PreserveGlobal.php';

/* Function to return all the users in the groups that this user is part of.
 * @param $id - id of the user
 * returns Array:UserIds userid of all the users in the groups that this user is part of.
 */
function vtws_getUsersInTheSameGroup($id){
	require_once('include/utils/GetGroupUsers.php');
	require_once('include/utils/GetUserGroups.php');
	
	$groupUsers = new GetGroupUsers();
	$userGroups = new GetUserGroups();
	$allUsers = Array();
	$userGroups->getAllUserGroups($id);
	$groups = $userGroups->user_groups;
	
	foreach ($groups as $group) {
		$groupUsers->getAllUsersInGroup($group);
		$usersInGroup = $groupUsers->group_users;
		foreach ($usersInGroup as $user) {
		if($user != $id){
				$allUsers[$user] = getUserName($user); 
			}
		}		
	}
	return $allUsers;
}

function vtws_generateRandomAccessKey($length=10){
	$source = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$accesskey = "";
	$maxIndex = strlen($source);
	for($i=0;$i<$length;++$i){
		$accesskey = $accesskey.substr($source,rand(null,$maxIndex),1);
	}
	return $accesskey;
}

/**
 * get current vtiger version from the database.
 */
function vtws_getVtigerVersion(){
	global $adb;
	$query = 'select * from vtiger_version';
	$result = $adb->pquery($query, array());
	$version = '';
	while($row = $adb->fetch_array($result))
	{
		$version = $row['current_version'];
	}
	return $version;
}

function vtws_getUserAccessibleGroups($moduleId, $user){
	global $adb;
	require('user_privileges/user_privileges_'.$user->id.'.php');
	require('user_privileges/sharing_privileges_'.$user->id.'.php');
	$tabName = getTabname($moduleId);
	if($is_admin==false && $profileGlobalPermission[2] == 1 && 
			($defaultOrgSharingPermission[$moduleId] == 3 or $defaultOrgSharingPermission[$moduleId] == 0)){
		$result=get_current_user_access_groups($tabName);
	}else{ 		
		$result = get_group_options();
	}
	
	$groups = array();
	if($result != null && $result != '' && is_object($result)){
		$rowCount = $adb->num_rows($result);
		for ($i = 0; $i < $rowCount; $i++) {
			$nameArray = $adb->query_result_rowdata($result,$i);
			$groupId=$nameArray["groupid"];
			$groupName=$nameArray["groupname"];
			$groups[] = array('id'=>$groupId,'name'=>$groupName);
		}
	}
	return $groups;
}

function vtws_getWebserviceGroupFromGroups($groups){
	global $adb;
	$webserviceObject = VtigerWebserviceObject::fromName($adb,'Groups');
	foreach($groups as $index=>$group){
		$groups[$index]['id'] = vtws_getId($webserviceObject->getEntityId(),$group['id']);
	}
	return $groups;
}

function vtws_getUserWebservicesGroups($tabId,$user){
	$groups = vtws_getUserAccessibleGroups($tabId,$user);
	return vtws_getWebserviceGroupFromGroups($groups);
}

function vtws_getIdComponents($elementid){
	return explode("x",$elementid);
}

function vtws_getId($objId, $elemId){
	return $objId."x".$elemId;
}

function getEmailFieldId($meta, $entityId,$fields){
	global $adb;
	if(sizeof($fields)>0){
		return $meta->getFieldIdFromFieldName($fields[0]);
	}
	//no email field accessible in the module. since its only association pick up the field any way.
	$query="SELECT fieldid,fieldlabel,columnname FROM vtiger_field WHERE tabid=? 
		and uitype=13 and presence in (0,2)";
	$result = $adb->pquery($query, array($meta->getEntityId()));
	//pick up the first field.
	$fieldId = $adb->query_result($result,0,'fieldid');
	return $fieldId;
}

function vtws_getParameter($parameterArray, $paramName,$default=null){
	
	if (!get_magic_quotes_gpc()) {
		$param = addslashes($parameterArray[$paramName]);
	} else {
		$param = $parameterArray[$paramName];
	}
	if(!$param){
		$param = $default;
	}
	return $param;
}

function vtws_getEntityNameFields($moduleName){
	
	global $adb;
	$query = "select fieldname,tablename,entityidfield from vtiger_entityname where modulename = ?";
	$result = $adb->pquery($query, array($moduleName));
	$rowCount = $adb->num_rows($result);
	$nameFields = array();
	if($rowCount > 0){
		$fieldsname = $adb->query_result($result,0,'fieldname');
		if(!(strpos($fieldsname,',') === false)){
			 $nameFields = explode(',',$fieldsname);
		}else{
			array_push($nameFields,$fieldsname);
		}
	}
	return $nameFields;	
}

/** function to get the module List to which are crm entities. 
 *  @return Array modules list as array
 */
function vtws_getModuleNameList(){
	global $adb;

	$sql = "select name from vtiger_tab where presence=0 and isentitytype=1 order by tabsequence";
	$res = $adb->pquery($sql, array());
	$mod_array = Array();
	while($row = $adb->fetchByAssoc($res)){
		array_push($mod_array,$row['name']);
	}
	return $mod_array;
}

function vtws_getWebserviceEntities(){
	global $adb;

	$sql = "select name,id,ismodule from vtiger_ws_entity";
	$res = $adb->pquery($sql, array());
	$moduleArray = Array();
	$entityArray = Array();
	while($row = $adb->fetchByAssoc($res)){
		if($row['ismodule'] == '1'){
			array_push($moduleArray,$row['name']);
		}else{
			array_push($entityArray,$row['name']);
		}
	}
	return array('module'=>$moduleArray,'entity'=>$entityArray);
}

function vtws_getModuleInstance($webserviceObject){
	$moduleName = $webserviceObject->getEntityName();
	return CRMEntity::getInstance($moduleName);
}

function vtws_isRecordOwnerUser($ownerId){
	global $adb;
	$result = $adb->pquery("select first_name from vtiger_users where id = ?",array($ownerId));
	$rowCount = $adb->num_rows($result);
	$ownedByUser = ($rowCount > 0);
	return $ownedByUser;
}

function vtws_isRecordOwnerGroup($ownerId){
	global $adb;
	$result = $adb->pquery("select groupname from vtiger_groups where groupid = ?",array($ownerId));
	$rowCount = $adb->num_rows($result);
	$ownedByGroup = ($rowCount > 0);
	return $ownedByGroup;
}

function vtws_getOwnerType($ownerId){
	if(vtws_isRecordOwnerGroup($ownerId) == true){
		return 'Groups';
	}
	if(vtws_isRecordOwnerUser($ownerId) == true){
		return 'Users';
	}
	throw new WebServiceException(WebServiceErrorCode::$INVALIDID,"Invalid owner of the record");
}

function vtws_runQueryAsTransaction($query,$params,&$result){
	global $adb;
	
	$adb->startTransaction();
	$result = $adb->pquery($query,$params);
	$error = $adb->hasFailedTransaction();
	$adb->completeTransaction();
	return !$error;
}

function vtws_getCalendarEntityType($id){
	global $adb;
	
	$sql = "select activitytype from vtiger_activity where activityid=?";
	$result = $adb->pquery($sql,array($id));
	$seType = 'Calendar';
	if($result != null && isset($result)){
		if($adb->num_rows($result)>0){
			$activityType = $adb->query_result($result,0,"activitytype");
			if($activityType !== "Task"){
				$seType = "Events";
			}
		}
	}
	return $seType;
}

/***
 * Get the webservice reference Id given the entity's id and it's type name
 */
function vtws_getWebserviceEntityId($entityName, $id){
	global $adb;
	$webserviceObject = VtigerWebserviceObject::fromName($adb,$entityName);
	return $webserviceObject->getEntityId().'x'.$id;
}

function vtws_addDefaultModuleTypeEntity($moduleName){
	global $adb;
	$isModule = 1;
	$moduleHandler = array('file'=>'include/Webservices/VtigerModuleOperation.php',
		'class'=>'VtigerModuleOperation');
	return vtws_addModuleTypeWebserviceEntity($moduleName,$moduleHandler['file'],$moduleHandler['class'],$isModule);
}

function vtws_addModuleTypeWebserviceEntity($moduleName,$filePath,$className){
	global $adb;	
	$checkres = $adb->pquery('SELECT id FROM vtiger_ws_entity WHERE name=? AND handler_path=? AND handler_class=?',
		array($moduleName, $filePath, $className));
	if($checkres && $adb->num_rows($checkres) == 0) {
		$isModule=1;
		$entityId = $adb->getUniqueID("vtiger_ws_entity");
		$adb->pquery('insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
			array($entityId,$moduleName,$filePath,$className,$isModule));
	}
}

function vtws_addDefaultActorTypeEntity($actorName,$actorNameDetails,$withName = true){
	$actorHandler = array('file'=>'include/Webservices/VtigerActorOperation.php',
		'class'=>'VtigerActorOperation');
	if($withName == true){
		vtws_addActorTypeWebserviceEntityWithName($actorName,$actorHandler['file'],$actorHandler['class'],
			$actorNameDetails);
	}else{
		vtws_addActorTypeWebserviceEntityWithoutName($actorName,$actorHandler['file'],$actorHandler['class'],
			$actorNameDetails);
	}
}

function vtws_addActorTypeWebserviceEntityWithName($moduleName,$filePath,$className,$actorNameDetails){
	global $adb;
	$isModule=0;
	$entityId = $adb->getUniqueID("vtiger_ws_entity");
	$adb->pquery('insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
		array($entityId,$moduleName,$filePath,$className,$isModule));
	vtws_addActorTypeName($entityId,$actorNameDetails['fieldNames'],$actorNameDetails['indexField'],
		$actorNameDetails['tableName']);
}

function vtws_addActorTypeWebserviceEntityWithoutName($moduleName,$filePath,$className,$actorNameDetails){
	global $adb;
	$isModule=0;
	$entityId = $adb->getUniqueID("vtiger_ws_entity");
	$adb->pquery('insert into vtiger_ws_entity(id,name,handler_path,handler_class,ismodule) values (?,?,?,?,?)',
		array($entityId,$moduleName,$filePath,$className,$isModule));
}

function vtws_addActorTypeName($entityId,$fieldNames,$indexColumn,$tableName){
	global $adb;
	$adb->pquery('insert into vtiger_ws_entity_name(entity_id,name_fields,index_field,table_name) values (?,?,?,?)',
		array($entityId,$fieldNames,$indexColumn,$tableName));
}

function vtws_getName($id,$user){
	global $log,$adb;
	
	$webserviceObject = VtigerWebserviceObject::fromId($adb,$id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	
	require_once $handlerPath;
	
	$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
	$meta = $handler->getMeta();
	return $meta->getName($id);
}

function vtws_preserveGlobal($name,$value){
	return VTWS_PreserveGlobal::preserveGlobal($name,$value);
}

/**
 * Takes the details of a webservices and exposes it over http.
 * @param $name name of the webservice to be added with namespace.
 * @param $handlerFilePath file to be include which provides the handler method for the given webservice.
 * @param $handlerMethodName name of the function to the called when this webservice is invoked.
 * @param $requestType type of request that this operation should be, if in doubt give it as GET,
 * 	general rule of thumb is that, if the operation is adding/updating data on server then it must be POST
 * 	otherwise it should be GET.
 * @param $preLogin 0 if the operation need the user to authorised to access the webservice and
 * 	1 if the operation is called before login operation hence the there will be no user authorisation happening
 * 	for the operation.
 * @return Integer operationId of successful or null upon failure.
 */
function vtws_addWebserviceOperation($name,$handlerFilePath,$handlerMethodName,$requestType,$preLogin = 0){
	global $adb;
	$createOperationQuery = "insert into vtiger_ws_operation(operationid,name,handler_path,handler_method,type,prelogin)
		values (?,?,?,?,?,?);";
	if(strtolower($requestType) != 'get' && strtolower($requestType) != 'post'){
		return null;
	}
	$requestType = strtoupper($requestType);
	if(empty($preLogin)){
		$preLogin = 0;
	}else{
		$preLogin = 1;
	}
	$operationId = $adb->getUniqueID("vtiger_ws_operation");
	$result = $adb->pquery($createOperationQuery,array($operationId,$name,$handlerFilePath,$handlerMethodName,
		$requestType,$preLogin));
	if($result !== false){
		return $operationId;
	}
	return null;
}

/**
 * Add a parameter to a webservice.
 * @param $operationId Id of the operation for which a webservice needs to be added.
 * @param $paramName name of the parameter used to pickup value from request(POST/GET) object.
 * @param $paramType type of the parameter, it can either 'string','datetime' or 'encoded'
 * 	encoded type is used for input which will be encoded in JSON or XML(NOT SUPPORTED).
 * @param $sequence sequence of the parameter in the definition in the handler method.
 * @return Boolean true if the parameter was added successfully, false otherwise
 */
function vtws_addWebserviceOperationParam($operationId,$paramName,$paramType,$sequence){
	global $adb;
	$supportedTypes = array('string','encoded','datetime','double','boolean');
	if(!is_numeric($sequence)){
		$sequence = 1;
	}if($sequence <=1){
		$sequence = 1;
	}
	if(!in_array(strtolower($paramType),$supportedTypes)){
		return false;
	}
	$createOperationParamsQuery = "insert into vtiger_ws_operation_parameters(operationid,name,type,sequence)
		values (?,?,?,?);";
	$result = $adb->pquery($createOperationParamsQuery,array($operationId,$paramName,$paramType,$sequence));
	return ($result !== false);
}

function vtws_getModuleHandlerFromName($name,$user){
	global $adb, $log;
	$webserviceObject = VtigerWebserviceObject::fromName($adb,$name);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	
	require_once $handlerPath;
	
	$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
	return $handler;
}

function vtws_getModuleHandlerFromId($id,$user){
	global $adb, $log;
	$webserviceObject = VtigerWebserviceObject::fromId($adb,$id);
	$handlerPath = $webserviceObject->getHandlerPath();
	$handlerClass = $webserviceObject->getHandlerClass();
	
	require_once $handlerPath;
	
	$handler = new $handlerClass($webserviceObject,$user,$adb,$log);
	return $handler;
}

?>
