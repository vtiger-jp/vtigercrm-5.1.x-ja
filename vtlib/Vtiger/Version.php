<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
include_once('vtigerversion.php');

/**
 * Provides utility APIs to work with Vtiger Version detection
 * @package vtlib
 */
class Vtiger_Version {

	/**
	 * Get current version of vtiger in use.
	 */
	static function current() {
		global $vtiger_current_version;
		return $vtiger_current_version;
	}

	/**
	 * Check current version of vtiger with given version
	 * @param String Version against which comparision to be done
	 * @param String Condition like ( '=', '!=', '<', '<=', '>', '>=')
	 */
	static function check($with_version, $condition='=') {
		$current_version = self::current();
		return version_compare($current_version, $with_version, $condition);
	}
}
?>
