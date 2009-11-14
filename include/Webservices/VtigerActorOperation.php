<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

require_once 'include/Webservices/VtigerCRMActorMeta.php';
class VtigerActorOperation extends WebserviceEntityOperation {
	private $entityTableName;
	private $moduleFields;
	private $isEntity = false;
	
	public function VtigerActorOperation($webserviceObject,$user,$adb,$log){
		parent::__construct($webserviceObject,$user,$adb,$log);
		$this->entityTableName = $this->getActorTables();
		if($this->entityTableName === null){
			throw new WebServiceException(WebServiceErrorCode::$UNKOWNENTITY,"Entity is not associated with any tables");
		}
		$this->meta = new VtigerCRMActorMeta($this->entityTableName,$webserviceObject,$adb,$user);
		$this->moduleFields = null;
	}
	
	private function getActorTables(){
		static $actorTables = array();
		
		if(isset($actorTables[$this->webserviceObject->getEntityName()])){
			return $actorTables[$this->webserviceObject->getEntityName()];
		}
		$sql = 'select table_name from vtiger_ws_entity_tables where webservice_entity_id=?';
		$result = $this->pearDB->pquery($sql,array($this->webserviceObject->getEntityId()));
		$tableName = null;
		if($result){
			$rowCount = $this->pearDB->num_rows($result);
			for($i=0;$i<$rowCount;++$i){
				$row = $this->pearDB->query_result_rowdata($result,$i);
				$tableName = $row['table_name'];
			}
		}
		return $tableName;
	}
	
	public function getMeta(){
		return $this->meta;
	}
	
	public function create($elementType,$element){
		$element = DataTransform::sanitizeForInsert($element,$this->meta);
		if(strcasecmp($elementType,'Groups') === 0){
			$id=$this->pearDB->getUniqueId("vtiger_users");
		}else{
			$id = $this->pearDB->getUniqueId($this->entityTableName); 
		}
		
		$element = $this->restrictFields($element);
		$element[$this->meta->getObectIndexColumn()] = $id;
		
		//Insert into group vtiger_table
		$query = "insert into {$this->entityTableName}(".implode(',',array_keys($element)).") values(".
					generateQuestionMarks(array_keys($element)).")";
		$result = null;
		$transactionSuccessful = vtws_runQueryAsTransaction($query, array_values($element),$result);
		if(!$transactionSuccessful){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				"Database error while performing required operation create");
		}
		return $this->retrieve(vtws_getId($this->meta->getEntityId(),$id));
	}
	
	private function restrictFields($element){
		$fields = $this->getModuleFields();
		$newElement = array();
		foreach ($fields as $field) {
			if(isset($element[$field['name']])){
				$newElement[$field['name']] = $element[$field['name']];
			}
		}
		return $newElement;
	}
	
	public function retrieve($id){
		
		$ids = vtws_getIdComponents($id);
		$elemid = $ids[1];
		
		$query = "select * from {$this->entityTableName} where {$this->meta->getObectIndexColumn()}=?";
		$transactionSuccessful = vtws_runQueryAsTransaction($query,array($elemid),$result);
		if(!$transactionSuccessful){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				"Database error while performing required operation");
		}
		$adb = $this->pearDB;
		if($result){
			$rowCount = $adb->num_rows($result);
			if($rowCount >0){
				$element = $adb->query_result_rowdata($result,0);
			}
		}
		return DataTransform::filterAndSanitize($element,$this->meta);
	}
	
	public function update($element){
		$ids = vtws_getIdComponents($element["id"]);
		$element = DataTransform::sanitizeForInsert($element,$this->meta);
		$element = $this->restrictFields($element);
		
		$columnStr = 'set '.implode('=?,',array_keys($element)).' =? ';
		
		$query = 'update '.$this->entityTableName.' '.$columnStr.'where '.$this->meta->getObectIndexColumn().'=?';
		$params = array_values($element);
		array_push($params,$ids[1]);
		$result = null;
		$transactionSuccessful = vtws_runQueryAsTransaction($query,$params,$result);
		if(!$transactionSuccessful){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				"Database error while performing required operation");
		}
		
		return $this->retrieve(vtws_getId($ids[0],$ids[1]));
	}
	
	public function delete($id){
		$ids = vtws_getIdComponents($id);
		$elemId = $ids[1];
		
		$result = null;
		$query = 'delete from '.$this->entityTableName.' where '.$this->meta->getObectIndexColumn().'=?';
		$transactionSuccessful = vtws_runQueryAsTransaction($query,array($elemId),$result);
		if(!$transactionSuccessful){
			throw new WebServiceException(WebServiceErrorCode::$DATABASEQUERYERROR,
				"Database error while performing required operation");
		}
		return array("status"=>"successful");
	}
	
	public function describe($elementType){
		
		$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');
		$current_user = vtws_preserveGlobal('current_user',$this->user);;
		$label = (isset($app_strings[$elementType]))? $app_strings[$elementType]:$elementType;
		$createable = $this->meta->hasWriteAccess();
		$updateable = $this->meta->hasWriteAccess();
		$deleteable = $this->meta->hasDeleteAccess();
		$retrieveable = $this->meta->hasReadAccess();
		$fields = $this->getModuleFields();
		return array("label"=>$label,"name"=>$elementType,"createable"=>$createable,"updateable"=>$updateable,
				"deleteable"=>$deleteable,"retrieveable"=>$retrieveable,"fields"=>$fields,
				"idPrefix"=>$this->meta->getEntityId(),'isEntity'=>$this->isEntity,'labelFields'=>$this->meta->getNameFields());
	}
	
	function getModuleFields(){
		$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');
		if($this->moduleFields === null){
			$fields = array();
			$moduleFields = $this->meta->getModuleFields();
			foreach ($moduleFields as $fieldName=>$webserviceField) {
				array_push($fields,$this->getDescribeFieldArray($webserviceField));
			}
			$label = ($app_strings[$this->meta->getObectIndexColumn()])? $app_strings[$this->meta->getObectIndexColumn()]:
				$this->meta->getObectIndexColumn();
			$this->moduleFields = $fields;
		}
		return $this->moduleFields;
	}
	
	function getDescribeFieldArray($webserviceField){
		$app_strings = VTWS_PreserveGlobal::getGlobal('app_strings');
		$fieldLabel = $webserviceField->getFieldLabelKey();
		if(isset($app_strings[$fieldLabel])){
			$fieldLabel = $app_strings[$fieldLabel];
		}
		if(strcasecmp($webserviceField->getFieldName(),$this->meta->getObectIndexColumn()) === 0){
			return $this->getIdField($fieldLabel);
		}
		
		$typeDetails = $this->getFieldTypeDetails($webserviceField);
		
		//set type name, in the type details array.
		$typeDetails['name'] = $webserviceField->getFieldDataType();
		$editable = $this->isEditable($webserviceField);
		
		$describeArray = array('name'=>$webserviceField->getFieldName(),'label'=>$fieldLabel,'mandatory'=>
			$webserviceField->isMandatory(),'type'=>$typeDetails,'nullable'=>$webserviceField->isNullable(),
			"editable"=>$editable);
		if($webserviceField->hasDefault()){
			$describeArray['default'] = $webserviceField->getDefault();
		}
		return $describeArray;
	}
	
}
?>