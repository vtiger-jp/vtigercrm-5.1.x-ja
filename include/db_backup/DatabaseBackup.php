<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
include_once('adodb/adodb.inc.php');

$langString = array(
	'SourceConnectFailed'=>'Source database connect failed',
	'DestConnectFailed'=>'Destination database connect failed',
	'TableListFetchError'=>'Failed to get Table List for database',
	'SqlExecutionError'=>'Execution of following query failed',
);

class DatabaseConfig{
	private $hostName = null;
	private $username = null;
	private $password = null;
	private $dbName = null;
	private $rootUsername = null;
	private $rootPassword = null;
	function DatabaseConfig($dbserver, $username, $password,$dbName, $rootusername='', $rootpassword=''){
		$this->hostName = $dbserver;
		$this->username = $username;
		$this->password = $password;
		$this->dbName = $dbName;
		$this->rootUsername = $rootusername;
		$this->rootPassword = $rootpassword;
	}
	
	function getHostName(){
		return $this->hostName;
	}
	
	function getUsername(){
		return $this->username;
	}
	
	function getPassword(){
		return $this->password;
	}
	
	function getRootUsername(){
		return $this->rootUsername;
	}
	
	function getRootPassword(){
		return $this->rootPassword;
	}
	
	function getDatabaseName(){
		return $this->dbName;
	}
	
}

class DatabaseBackupException extends Exception{
	public $code;
	public $message;
	
	function DatabaseBackupException($errCode,$msg){
		$this->code = $errCode;
		$this->message = $msg;
	}
}

class DatabaseErrorCode{
	public static $DB_CONNECT_ERROR = 'CONNECT_ERROR';
	public static $TABLE_NAME_ERROR = 'TABLE_LIST_FETCH_ERROR';
	public static $SQL_EXECUTION_ERROR = 'SQL_EXECUTION_ERROR';
}

class DatabaseBackup {
	private $sourceConfig = null;
	private $destinationConfig = null;
	private $dbType = null;
	private $sourceCon = null;
	private $destCon = null;
	private static $langString = null;
	private $downloadMode = null;
	private $createTarget = null;
	private $supportUTF8 = null;
	function DatabaseBackup($dbType='mysql',$createTarget=false,$utf=true){
		$this->dbType = strtolower($dbType);
		if(!is_array(DatabaseBackup::$langString)){
			DatabaseBackup::$langString = getLanguageStrings();
		}
		$this->downloadMode = false;
		$this->createTarget = $createTarget;
		$this->supportUTF8 = $utf;
	}
	
	function setSourceConfig($sourceConfig){
		$this->sourceConfig = $sourceConfig;
		$this->initSourceConnection();
	}
	
	function setDestinationConfig($destinationConfig){
		$this->destinationConfig = $destinationConfig;
		$this->initDestinationConnection();
	}
	
	function enableDownloadMode(){
		$this->downloadMode = true;
	}
	
	function isDownloadMode(){
		return $this->downloadMode;
	}
	
	function isUTF8SupportEnabled() {
		return $this->supportUTF8;
	}
	
	function getSourceConnection(){
		return $this->sourceCon;
	}
	
	function getDestinationConnection(){
		return $this->destCon;
	}
	
	function initSourceConnection(){
		$this->sourceCon = &NewADOConnection($this->dbType);
		$ok = $this->sourceCon->NConnect($this->sourceConfig->getHostName(),$this->sourceConfig->getUserName(),
			$this->sourceConfig->getPassword(),$this->sourceConfig->getDatabaseName());
		if(!$ok){
			throw new DatabaseBackupException(DatabaseErrorCode::$DB_CONNECT_ERROR,
				DatabaseBackup::$langString['SourceConnectFailed']);
		}
		$this->sourceCon->_Execute("SET NAMES 'utf8'",false);
		$result = $this->sourceCon->_Execute("SET interactive_timeout=28800",false);
		$result = $this->sourceCon->_Execute("SET wait_timeout=28800",false);
		$result = $this->sourceCon->_Execute("SET net_write_timeout=900",false);
		$result = $this->sourceCon->_Execute("SET net_read_timeout=900",false);
	}
	
	function initDestinationConnection(){
		$this->destCon = &NewADOConnection($this->dbType);
		if($this->createTarget){
			$this->createTargetDB();
		}
		$ok = $this->destCon->NConnect($this->destinationConfig->getHostName(),$this->destinationConfig->getUserName(),
			$this->destinationConfig->getPassword(),$this->destinationConfig->getDatabaseName());
		if(!$ok){
			throw new DatabaseBackupException(DatabaseErrorCode::$DB_CONNECT_ERROR,
				DatabaseBackup::$langString['DestConnectFailed']);
		}
		$result = $this->destCon->_Execute("SET interactive_timeout=28800",false);
		$result = $this->destCon->_Execute("SET wait_timeout=28800",false);
		$result = $this->destCon->_Execute("SET net_write_timeout=900",false);
	}
	
	function createTargetDB(){
		if(!$this->isDownloadMode()){
			if($this->dbType == 'mysql'){
				$ok = $this->destCon->NConnect($this->destinationConfig->getHostName(),$this->destinationConfig->getRootUserName(),
					$this->destinationConfig->getRootPassword());
				if(!$ok){
					throw new DatabaseBackupException(DatabaseErrorCode::$DB_CONNECT_ERROR,
						DatabaseBackup::$langString['DestConnectFailed']);
				}
				// Drop database if already exists
				$sql = "drop database IF EXISTS ".$this->destinationConfig->getDatabaseName();
				$result = $this->destCon->Execute($sql);
				$this->checkError($result,$sql);
				
				$sql = 'create database '.$this->destinationConfig->getDatabaseName();
				if( $this->supportUTF8 == true){
					$sql .= " default character set utf8 default collate utf8_general_ci";
				}
				$result = $this->destCon->Execute($sql);
				$this->checkError($result,$sql);
				$this->destCon->Close();
				$this->destCon = &NewADOConnection($this->dbType);
			}
		}
	}
	
	function initBackup(){
		if(!$this->isDownloadMode()){
			$sql = "SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0";
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
			$sql = "SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';";
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
			$sql = 'SET NAMES utf8';
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
			return;
		}else{
			$this->writeLine('-- Dump generated by vtigerCRM');
			$this->writeLine('-- Date: ' . date("D, M j, G:i:s T Y"));
			$this->writeLine('-- HOST: ' . $this->sourceConfig->getHostName(). 
				' Database: '.$this->sourceConfig->getDatabaseName());
			$this->writeLine("-- ----------------------------------");
			$this->writeLine("");
	
			$this->writeLine("/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;");
			$this->writeLine("/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;");
			$this->writeLine("/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;");
			$this->writeLine("/*!40101 SET NAMES utf8 */;");
			$this->writeLine("/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;");
			$this->writeLine("/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;");
			$this->writeLine("/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;");
			$this->writeLine("/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;");
		}
	}
	
	function writeLine($string){
		echo $string,"\n";
	}
	
	function finalizeBackup(){
		if(!$this->isDownloadMode()){
			$sql = "SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS";
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
			$sql = "SET SQL_MODE=@OLD_SQL_MODE;";
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
			return;
		}else{
			$this->writeLine("/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;");
			$this->writeLine("/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;");
			$this->writeLine("/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;");
			$this->writeLine("/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;");
			$this->writeLine("/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;");
			$this->writeLine("/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;");
			$this->writeLine("/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;");
			$this->writeLine("-- DONE");
		}
	}
	
	function processTableCreate($tableName,$sql){
		if(!$this->isDownloadMode()){
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
		}else{
			$this->writeLine("");
			$this->writeLine("--");
			$this->writeLine("-- Table structure for table $tableName");
			$this->writeLine("--");
			$this->writeLine("");
			$this->writeLine("DROP TABLE IF EXISTS $tableName;");
			$this->writeLine($sql.';');
			$this->writeLine("");
			$this->writeLine("--");
			$this->writeLine("-- Dumping data for table $tableName");
			$this->writeLine("--");
			$this->writeLine("");
		}
	}
	
	function processStatement($sql){
		if(!$this->isDownloadMode()){
			$result = $this->destCon->_Execute($sql,false);
			$this->checkError($result,$sql);
		}else{
			$this->writeLine($sql.';');
		}
	}
	
	function checkError($result,$sql){
		if($result === false){
			throw new DatabaseBackupException(DatabaseErrorCode::$SQL_EXECUTION_ERROR,
				DatabaseBackup::$langString['SqlExecutionError'].' '.$sql);
		}
	}
	
	function backup(){
		$this->initBackup();
		set_time_limit(0);
		$tableNameList = $this->sourceCon->MetaTables('TABLES');
		if($tableNameList === false){
			throw new DatabaseBackupException(DatabaseErrorCode::$TABLE_NAME_ERROR,
				DatabaseBackup::$langString['TableListFetchError']);
		}
		foreach ($tableNameList as $tableName) {
			$sql = $this->getTableCreateStatement($this->sourceCon,$tableName);
			$this->processTableCreate($tableName,$sql);
			$sql = "select * from $tableName";
			$result = $this->sourceCon->_Execute($sql,false);
			$this->checkError($result,$sql);
			while(!$result->EOF){
				$row = $result->GetRowAssoc(2);
				$sql = $this->sourceCon->GetInsertSQL($tableName,$row);
				$this->processStatement($sql);
				$result->MoveNext();
			}
		}
		$this->finalizeBackup();
	}
	
	function getTableCreateStatement($con, $tableName){
		if($this->dbType == 'mysql'){
			$sql = "show create table $tableName";
			$result = $con->_Execute($sql,false);
			$this->checkError($result,$sql.' '.$con->ErrorMsg().' '.$con->ErrorNo());
			$data = $result->FetchRow();
			return $data[1];
		}
	}
	
}

function getLanguageStrings(){
	global $langString;
	return $langString;
}

?>