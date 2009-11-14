<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
/* 
 * During migration config.inc.php might not be populated.
 * We will create this instance later based on the configuration file as later binding.
 */
$adb = true; 
require_once('include/utils/utils.php');
require_once('include/logging.php');
$migrationlog = & LoggerManager::getLogger('MIGRATION');

$log =& LoggerManager::getLogger('PLATFORM');
$completed = false;

session_start();

if($_REQUEST['migration_verify']=='true') {
	$migration_array = array();
	if (isset($_REQUEST['source_path'])) $source_directory = $_REQUEST['source_path'];
	$tmp = strlen($source_directory);
	if(!empty($source_directory)){
		if($source_directory[$tmp-1]!= "/" || $source_directory[$tmp-1]!= "\\"){
			$source_directory .= "/";
		}
		$migration_array['source_directory'] = $source_directory;
	}
	if (isset($_REQUEST['user_name'])){ 
		$migration_array['user_name'] = $_REQUEST['user_name'];
	}
	if (isset($_REQUEST['user_pwd'])){ 
		$migration_array['user_pwd'] = $_REQUEST['user_pwd'];
	}
	if (isset($_REQUEST['old_version'])){ 
		$migration_array['old_version'] = $_REQUEST['old_version'];
	}
	if (isset($_REQUEST['new_dbname'])){ 
		$migration_array['new_dbname'] = $_REQUEST['new_dbname'];
	}
	if (isset($_REQUEST['root_directory'])){ 
		$migration_array['root_directory'] = $_REQUEST['root_directory'];
	}
	$_SESSION['migration_info'] = $migration_array;
	vtiger_MigrationVerify::process();
}

if($_REQUEST['migration_start']=='true' && $_SESSION['authentication_key']==$_REQUEST['auth_key']){
	vtiger_DatabaseMigration::process();
}

class vtiger_MigrationVerify{
	function process(){
		set_time_limit(0);//ADDED TO AVOID UNEXPECTED TIME OUT WHILE MIGRATING
		if (isset($_SESSION['migration_info']['source_directory'])) $source_directory = $_SESSION['migration_info']['source_directory'];
		if(is_dir($source_directory)){
			if(!is_file($source_directory."config.inc.php")){
				echo "NO_CONFIG_FILE";
				return false;
			}
			if(!is_dir($source_directory."user_privileges")){
				echo "NO_USER_PRIV_DIR";
				return false;
			}
			if(!is_dir($source_directory."storage")){
				echo "NO_STORAGE_DIR";
				return false;
			}
		} else {
			echo "NO_SOURCE_DIR";
			return false;
		}
		global $dbconfig;
		require_once($source_directory."config.inc.php");
		$old_db_name = $dbconfig['db_name'];
		$db_hostname = $dbconfig['db_server'].$dbconfig['db_port'];
		$db_username = $dbconfig['db_username'];
		$db_password = $dbconfig['db_password'];
		$db_type = $dbconfig['db_type'];
		
		if (isset($_SESSION['migration_info']['user_name'])) $user_name = $_SESSION['migration_info']['user_name'];
		if (isset($_SESSION['migration_info']['user_pwd'])) $user_pwd = $_SESSION['migration_info']['user_pwd'];
		if (isset($_SESSION['migration_info']['old_version'])) $source_version = $_SESSION['migration_info']['old_version'];
		if (isset($_SESSION['migration_info']['new_dbname'])) $new_db_name = $_SESSION['migration_info']['new_dbname'];
		
		$_SESSION['migration_info']['db_type'] = $db_type;
		$_SESSION['migration_info']['db_hostname'] = $db_hostname;
		$_SESSION['migration_info']['db_username'] = $db_username;
		$_SESSION['migration_info']['db_password'] = $db_password;
		$_SESSION['migration_info']['old_dbname'] = $old_db_name;
		
		$_SESSION['migration_info']['db_server'] = $dbconfig['db_server'];
		$_SESSION['migration_info']['db_port'] = $dbconfig['db_port'];
		$_SESSION['migration_info']['admin_emailid'] = $HELPDESK_SUPPORT_EMAIL_ID;
		$_SESSION['migration_info']['currency_name'] = $currency_name;		
	
		$db_type_status = false; // is there a db type?
		$db_server_status = false; // does the db server connection exist?
		$old_db_exist_status = false; // does the old database exist?
		$db_utf8_support = false; // does the database support utf8?
		$new_db_exist_status = false; // does the new database exist?
		
		require_once('include/DatabaseUtil.php');
		//Checking for database connection parameters and copying old database into new database
		if($db_type) {
			$conn = &NewADOConnection($db_type);
			$db_type_status = true;
			if(@$conn->Connect($db_hostname,$db_username,$db_password)) {
				$db_server_status = true;
				$serverInfo = $conn->ServerInfo();
				if($db_type=='mysql') {
					$version = explode('-',$serverInfo);
					$mysql_server_version=$version[0];
				}
		
				// test the connection to the old database
				$olddb_conn = &NewADOConnection($db_type);
				if(@$olddb_conn->Connect($db_hostname, $db_username, $db_password, $old_db_name))
				{
					$old_db_exist_status = true;
					
					if(authenticate_user($user_name,$user_pwd)==true){
						$is_admin = true;
					}
					else{
						echo 'NOT_VALID_USER';
						return false;
					}
					$olddb_conn->Close();
				}
		
				// test the connection to the new database
				$newdb_conn = &NewADOConnection($db_type);
				if(@$newdb_conn->Connect($db_hostname, $db_username, $db_password, $new_db_name))
				{
					$new_db_exist_status = true;
					$_SESSION['migration_info']['db_utf8_support'] = check_db_utf8_support($newdb_conn);
					$newdb_conn->Close();
				}		
			}
			$conn->Close();
		}
		
		if(!$db_type_status || !$db_server_status) {
			$error_msg = 'ERR - Unable to connect to database Server. Invalid mySQL Connection Parameters specified';
			$error_msg_info = 'This may be due to the following reasons:<br>
					-  specified database user, password, hostname, database type, or port is invalid. <a href="http://www.vtiger.com/products/crm/help/5.1.0/vtiger_CRM_Database_Hostname.pdf" target="_blank">More Information</a><BR>
					-  specified database user does not have access to connect to the database server from the host';
		} elseif($db_type == 'mysql' && $mysql_server_version < '4.1') {
			$error_msg = 'ERR - MySQL version '.$mysql_server_version.' is not supported, kindly connect to MySQL 4.1.x or above';
		} elseif(!$old_db_exist_status) {
			$error_msg = 'ERR - The Database "'.$old_db_name.'" is not found. Provide the correct database name';
		} elseif(!$new_db_exist_status) {
			$error_msg = 'ERR - The Database "'.$new_db_name.'" is not found. Provide the correct database name';
		} else {
			$_SESSION['authentication_key'] = md5(microtime());
			return true;
		}
		echo $error_msg."\n".$error_msg_info;
		return false;
	}
}

class vtiger_DatabaseMigration{
	function process(){
		set_time_limit(0);//ADDED TO AVOID UNEXPECTED TIME OUT WHILE MIGRATING
		
		$returnValue = vtiger_DatabaseMigration::initMigration();
		if ($returnValue !== true) {
			echo $returnValue;
			return false;
		}
		
		global $dbconfig;
		require (dirname(__FILE__) . '/config.inc.php');
		$dbtype		= $dbconfig['db_type'];
		$host		= $dbconfig['db_server'].$dbconfig['db_port'];
		$dbname		= $dbconfig['db_name'];
		$username	= $dbconfig['db_username'];
		$passwd		= $dbconfig['db_password'];
				
		global $adb,$migrationlog;
		$adb = new PearDatabase($dbtype,$host,$dbname,$username,$passwd);
		
		// Why do we do this here? We shouldn't alter here if its not in UTF8.
		$query = " ALTER DATABASE ".$dbname." DEFAULT CHARACTER SET utf8";
		$adb->query($query);
		
		$source_directory = $_SESSION['migration_info']['source_directory'];
		if(file_exists($source_directory.'user_privileges/CustomInvoiceNo.php')) {
			require_once($source_directory.'user_privileges/CustomInvoiceNo.php');
		}
			
		$versions_non_utf8 = array("50","501","502","503rc2","503","504rc");
		$php_max_execution_time = 0;
	
		$migrationlog =& LoggerManager::getLogger('MIGRATION');
		if (isset($_SESSION['migration_info']['old_version'])) $source_version = $_SESSION['migration_info']['old_version'];
		if(!isset($source_version) || empty($source_version)) {
			//If source version is not set then we cannot proceed
			echo "<br> Source Version is not set. Please check vtigerversion.php and contiune the Patch Process";
			exit;
		}
	
		$reach = 0;
		include(dirname(__FILE__)."/modules/Migration/versions.php");
		foreach($versions as $version => $label) {
			if($version == $source_version || $reach == 1) {
				$reach = 1;
				$temp[] = $version;
			}
		}
		$temp[] = $current_version;

		global $adb, $dbname;
		$_SESSION['adodb_current_object'] = $adb;
		
		@ini_set('zlib.output_compression', 0);
		@ini_set('output_buffering','off');
		ob_implicit_flush(true);
		echo '<table width="98%" border="1px" cellpadding="3" cellspacing="0" height="100%">';
		echo "<tr><td colspan='2'><b>Going to apply the Database Changes...</b></td><tr>";
	
		for($patch_count=0;$patch_count<count($temp);$patch_count++) {
			//Here we have to include all the files (all db differences for each release will be included)
			$filename = "modules/Migration/DBChanges/".$temp[$patch_count]."_to_".$temp[$patch_count+1].".php";
			$empty_tag = "<tr><td colspan='2'>&nbsp;</td></tr>";
			$start_tag = "<tr><td colspan='2'><b><font color='red'>&nbsp;";
			$end_tag = "</font></b></td></tr>";
	
			if(is_file($filename)) {
				echo $empty_tag.$start_tag.$temp[$patch_count]." ==> ".$temp[$patch_count+1]." Database changes -- Starts.".$end_tag;
		
				include($filename);//include the file which contains the corresponding db changes
		
				echo $start_tag.$temp[$patch_count]." ==> ".$temp[$patch_count+1]." Database changes -- Ends.".$end_tag;
			}
		}
	
		//Here we have to update the version in table. so that when we do migration next time we will get the version
		$res = $adb->query('SELECT * FROM vtiger_version');
		global $vtiger_current_version;
		require(dirname(__FILE__).'/vtigerversion.php');
		if($adb->num_rows($res)) {
			$res = ExecuteQuery("UPDATE vtiger_version SET old_version='$versions[$source_version]',current_version='$vtiger_current_version'");
			$completed = true;
		} else {
			ExecuteQuery("INSERT INTO vtiger_version (id, old_version, current_version) values (".$adb->getUniqueID('vtiger_version').", '$versions[$source_version]', '$vtiger_current_version');");
			$completed = true;
		}
		echo '</table><br><br>';
		if($completed==true){
			echo "<script type='text/javascript'>window.parent.Migration_Complete();</script>";
		}
		
		create_tab_data_file();
		create_parenttab_data_file();
		if($completed ==true){
			return true; 
		}
	}
	
	function initMigration() {
		if(vtiger_DatabaseMigration::createConfigFile() == false) {
			return "FAILURE: Writing to config file. check permissions.";
		}
		
		$migrationInfo = $_SESSION['migration_info'];
		$sourceDirectory = $migrationInfo['source_directory'];
		$destinationDirectory = $migrationInfo['root_directory'];
		if (realpath($sourceDirectory) != realpath($destinationDirectory)) {
			vtiger_DatabaseMigration::copyRequiredFiles($sourceDirectory, $destinationDirectory);
		}
		return true;
	}
	
	function createConfigFile() {
		
		$migrationInfo = $_SESSION['migration_info'];
		require_once($migrationInfo['source_directory'].'config.inc.php');
		//Writing to Config file
		if (is_file('config.inc.php'))
			$is_writable = is_writable('config.inc.php');
		else
			$is_writable = is_writable('.');
		
		$dbtype=$migrationInfo['db_type'];
		$host=$migrationInfo['db_hostname'];
		$dbname=$migrationInfo['new_dbname'];
		$dbusername=$migrationInfo['db_username'];
		$dbpasswd=$migrationInfo['db_password'];
		$dbserver=$migrationInfo['db_server'];
		$dbport=$migrationInfo['db_port'];
		
		$root_directory = str_replace("\\\\","/",$migrationInfo['root_directory']);
		$vt_charset = ($migrationInfo['db_utf8_support'])? "UTF-8" : "ISO-8859-1";
		$admin_emailid = $migrationInfo['admin_emailid'];
		$currency_name = $migrationInfo['currency_name'];
		
		$cache_dir = "cache/";
		
		$web_root = ($_SERVER["HTTP_HOST"]) ? $_SERVER["HTTP_HOST"]:$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		$web_root .= $_SERVER["REQUEST_URI"];
		$web_root = eregi_replace("/install.php(.)*", "", $web_root);
		$site_URL = "http://".$web_root;
		
		/* open template configuration file read only */
		$templateFilename = 'config.template.php';
		$templateHandle = fopen($templateFilename, "r");
		if($templateHandle) {
			/* open include configuration file write only */
			$includeFilename = 'config.inc.php';
			$includeHandle = fopen($includeFilename, "w");
			if($includeHandle) {
				while (!feof($templateHandle)) {
					$buffer = fgets($templateHandle);
			
					/* replace _DBC_ variable */
					$buffer = str_replace( "_DBC_SERVER_", $dbserver, $buffer);
					$buffer = str_replace( ":_DBC_PORT_", $dbport, $buffer);
					$buffer = str_replace( "_DBC_USER_", $dbusername, $buffer);
					$buffer = str_replace( "_DBC_PASS_", $dbpasswd, $buffer);
					$buffer = str_replace( "_DBC_NAME_", $dbname, $buffer);
					$buffer = str_replace( "_DBC_TYPE_", $dbtype, $buffer);
			
					$buffer = str_replace( "_SITE_URL_", $site_URL, $buffer);
			
					/* replace dir variable */
					$buffer = str_replace( "_VT_ROOTDIR_", $root_directory, $buffer);
					$buffer = str_replace( "_VT_CACHEDIR_", $cache_dir, $buffer);
					$buffer = str_replace( "_VT_TMPDIR_", $cache_dir."images/", $buffer);
					$buffer = str_replace( "_VT_UPLOADDIR_", $cache_dir."upload/", $buffer);
					$buffer = str_replace( "_DB_STAT_", "true", $buffer);
			
					/* replace charset variable */
					$buffer = str_replace( "_VT_CHARSET_", $vt_charset, $buffer);
			
					/* replace master currency variable */
					$buffer = str_replace( "_MASTER_CURRENCY_", $currency_name, $buffer);
			
					/* replace the application unique key variable */
					$buffer = str_replace( "_VT_APP_UNIQKEY_", md5($root_directory), $buffer);
					/* replace support email variable */
					$buffer = str_replace( "_USER_SUPPORT_EMAIL_", $admin_emailid, $buffer);
			
					fwrite($includeHandle, $buffer);
				}
				flush();	
				fclose($includeHandle);
			}
			fclose($templateHandle);
		}
		  
		if (!($templateHandle && $includeHandle)) {
			return false;
		}
		return true;
	}
	
	function copyRequiredFiles($sourceDirectory, $destinationDirectory) {
		@get_files_from_folder($sourceDirectory."user_privileges/",$destinationDirectory."user_privileges/",
								// Force copy these files - Overwrite if they exist in destination directory.
								array($sourceDirectory."user_privileges/default_module_view.php") 
							);	
		@get_files_from_folder($sourceDirectory."storage/",$destinationDirectory."storage/");
	}
}

function authenticate_user($user_name,$user_password){
	$salt = substr($user_name, 0, 2);
	
	$sql = mysql_query("SELECT * from vtiger_users WHERE user_name = '$user_name'");
	$result = mysql_fetch_array($sql);
	$crypt_type = $result['crypt_type'];
	if($crypt_type == 'MD5') {
		$salt = '$1$' . $salt . '$';
	} else if($crypt_type == 'BLOWFISH') {
		$salt = '$2$' . $salt . '$';
	}
	$encrypted_password = crypt($user_password, $salt);	
	$password =  $result['user_password'];
	$status =  $result['status'];
	$is_admin =  $result['is_admin'];
	
	if(!($password == $encrypted_password) || !($status=='Active') || !($is_admin=='on')){
		return false;
	}
	return true;
}

function get_files_from_folder($source, $dest, $forcecopy=false) {
	if(!$forcecopy) $forcecopy = Array();
	
	if ($handle = opendir($source)) {
		while (false != ($file = readdir($handle))) {
			if (is_file($source.$file)) {
				if(!file_exists($dest.$file) || in_array($source.$file, $forcecopy)){
					$file_handle = fopen($dest.$file,'w');
					fclose($file_handle);
					copy($source.$file, $dest.$file);
				}
			} elseif ($file != '.' && $file != '..' && is_dir($source.$file)) {
				mkdir($dest.$file.'/',0777);
				get_files_from_folder($source.$file.'/', $dest.$file.'/');
			}
		}
	}
	@closedir($handle);
}

//Function used to execute the query and display the success/failure of the query
function ExecuteQuery($query)
{
	global $adb;
	global $conn, $query_count, $success_query_count, $failure_query_count, $success_query_array, $failure_query_array;
	global $migrationlog;

	//For third option migration we have to use the $conn object because the queries should be executed in 4.2.3 db
	$status = $adb->query($query);
	$query_count++;
	if(is_object($status))
	{
		echo '
			<tr width="100%">
				<td width="10%"><font color="green"> SUCCESS </font></td>
				<td width="80%">'.$query.'</td>
			</tr>';
		$success_query_array[$success_query_count++] = $query;
		$migrationlog->debug("Query Success ==> $query");
	}
	else
	{
		echo '
			<tr width="100%">
					<td width="5%"><font color="red"> FAILURE </font></td>
				<td width="70%">'.$query.'</td>
			</tr>';
		$failure_query_array[$failure_query_count++] = $query;
		$migrationlog->debug("Query Failed ==> $query \n Error is ==> [".$adb->database->ErrorNo()."]".$adb->database->ErrorMsg());
	}
}

?>