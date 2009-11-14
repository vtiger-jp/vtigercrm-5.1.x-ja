<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
class VTSimpleTemplate{
	
	function __construct($templateString){
		$this->template = $templateString;
	}
	
	function render($entityCache, $entityId){
		$this->cache = $entityCache;
		$this->parent = $this->cache->forId($entityId);
		return $this->parseTemplate();
	}
	
	private function matchHandler($match){
		preg_match('/\((\w+) : \((\w+)\) (\w+)\)/', $match[1], $matches);
		if(count($matches)==0){
			$fieldname = $match[1];
			$data = $this->parent->getData();
			$result = $data[$fieldname];
		}else{
			list($full, $referenceField, $referenceModule, $fieldname) = $matches;
			$referenceId = $this->parent->get($referenceField);
			if($referenceId==null){
				$result="";
			}else{
				$entity = $this->cache->forId($referenceId);
				if($entity->getModuleName()==$referenceModule){
					$data = $entity->getData();
					$result = $data[$fieldname];
				}else{
					$result = "";
				}
			}
		}
		return $result;
	}
	
	function parseTemplate(){
		return preg_replace_callback('/\\$(\w+|\((\w+) : \((\w+)\) (\w+)\))/', array($this,"matchHandler"), $this->template);
	}
}
?>