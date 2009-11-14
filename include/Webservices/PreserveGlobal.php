<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

class VTWS_PreserveGlobal{
	
	private static $globalData = array();
	
	static function preserveGlobal($name,$value){
		//$name store the name of the global.
		global $$name;
		
		if(!is_array(VTWS_PreserveGlobal::$globalData[$name])){
			VTWS_PreserveGlobal::$globalData[$name] = array();
			VTWS_PreserveGlobal::$globalData[$name]['exists'] = true;
			VTWS_PreserveGlobal::$globalData[$name]['value'] = $$name;
		}
		$$name = $value;
		return $$name;
	}
	
	static function restore($name){
		//$name store the name of the global.
		global $$name;
		
		if(VTWS_PreserveGlobal::$globalData[$name]['exists'] === true){
			$$name = VTWS_PreserveGlobal::$globalData[$name]['value'];
			return $$name;
		}
	}
	
	static function getGlobal($name){
		global $$name;
		return VTWS_PreserveGlobal::preserveGlobal($name,$$name);
	}
	
	static function flush(){
		foreach (VTWS_PreserveGlobal::$globalData as $name => $detail) {
			//$name store the name of the global.
			global $$name;
			$$name = VTWS_PreserveGlobal::$globalData[$name]['value'];
			return $$name;
		}
	}
	
}

?>