<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

session_start();
if(empty($_SESSION['authentication_key'])) {
	die("Not Authorized to perform this operation");
}
if($_REQUEST['ajax'] == true) {
	if($_SESSION['authentication_key'] != $_REQUEST['auth_key']) {
		die("Not Authorized to perform this operation");
	}	
}

$migrationInfo = $_SESSION['migration_info'];
require_once('adodb/adodb.inc.php');
$db = &NewADOConnection($migrationInfo['db_type']);
$db->NConnect($migrationInfo['db_hostname'], $migrationInfo['db_username'], $migrationInfo['db_password'], $migrationInfo['new_dbname']);
require_once('include/utils/DBHealthCheck.php');
$dbHealthCheck = new DBHealthCheck($db);
$dbHostName = $dbHealthCheck->dbHostName;
$dbName = $dbHealthCheck->dbName;
if(!empty($_REQUEST['forceDbCheck']) || $_SESSION[$dbName.'_'.$dbHostName.'_HealthApproved'] != true) {
	
	if($_REQUEST['updateTableEngine'] == true) {
		 if(!empty($_REQUEST['updateEngineForTable'])) {
		 	$dbHealthCheck->updateTableEngineType(htmlentities($_REQUEST['updateEngineForTable']));
		 }
		 elseif($_REQUEST['updateEngineForAllTables'] == true) {
		 	$dbHealthCheck->updateAllTablesEngineType();
		 }
	}

	$unHealthyTablesList = $dbHealthCheck->getUnhealthyTablesList();
	$noOfTables = count($unHealthyTablesList);
	if ($noOfTables <= 0) {
		$_SESSION[$dbName.'_'.$dbHostName.'_HealthApproved'] = true;
		if($_REQUEST['ajax'] == true) {
			echo "TABLE_TYPE_FIXED";
		}
	} else {		
		$_SESSION[$dbName.'_'.$dbHostName.'_HealthApproved'] = false;
	}
}

if ($_REQUEST['viewDBReport'] == true) {
	if($_SESSION['authentication_key'] != $_REQUEST['auth_key']) {
		die("Not Authorized to perform this operation");
	}
	$auth_key = $_REQUEST['auth_key'];
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Database Check</title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<script type='text/javascript'>
	function correctTableEngineType(tableName) {
		var form = document.updateForm;
		form.updateEngineForTable.value = tableName;
		form.submit();
	}
	</script>
</head>
<body class="small cwPageBg">
	<div style="height: 30em">
		<table width="99%" cellspacing="0" cellpadding="0" border="0" align="center">
			<tr>
				<td align="left" class="cwHeadBg heading2">&nbsp;Database Check</td>
				<td align="right" class="cwHeadBg1"><img title="vtiger CRM 5" alt="vtiger CRM 5" src="include/install/images/vtigercrm5.gif"/></td>
				<td width="2%" class="cwHeadBg1"/>
			</tr>
		</table>
		<table cellpadding="10" cellspacing="0" border="0" width="99%" height="45%" align="center">
			<tr>
				<td bgcolor="#4572be" align="center" class="small">				
					<?php if($_SESSION[$dbName.'_'.$dbHostName.'_HealthApproved'] == true) { ?>
					<table class="cwContentDisplay" cellpadding="10" cellspacing="10" border="0" width="97%" style="height: 100%" align="center">
						<tr>
							<td class="contentDisplay">
								<div class="textCenter higherLineHeight">
									Required tables were detected to be in proper Engine type (InnoDB) <br />
									You can close this window and proceed further with migration. <br />								
									<input type="button" class="small edit" value="Close" name="Close" onClick="window.close()" />
								</div>
							</td>
						</tr>
					</table>
					<?php } else { ?>
					<table class="cwContentDisplay" cellpadding="10" cellspacing="10" border="0" width="97%" align="center">
						<tr>
							<td class="contentDisplay">
								<div>
									For proper functionality of vtiger CRM, we recommend the following:					
									<ul>
								    	<li>Tables to have InnoDB engine type. ( <a href="http://dev.mysql.com/doc/refman/5.0/en/innodb.html" target="_about">What is InnoDB?</a>).<br/></li>
								    	<li>To get complete UTF-8 support, tables should have default charset UTF8.<br/></li>
									</ul>
								</div>
							</td>
						</tr>
					</table>	 
					<table class="cwContentDisplay" cellpadding="10" cellspacing="0" border="0" width="97%" align="center">
						<tr>
							<td>
								<table cellpadding="0" cellspacing="0" border="0" width="95%" align="center">
									<tr>
										<td align="right">
											<form action="install.php" name="submitForm">
												<input type="hidden" name="file" value="VerifyDBHealth.php" />
												<input type="hidden" name="viewDBReport" value="true" />
												<input type="hidden" name="auth_key" value="<?php echo $auth_key; ?>" />
												<input type="hidden" name="updateTableEngine" value="true" />
												<input type="hidden" name="updateEngineForAllTables" value="true" />
												<input align="right" class="small edit" type="button" name="back" title="Fix Engine For All Tables" value="Fix Engine For All Tables" onclick="document.submitForm.submit();" />
											</form>
											<form action='install.php' name='updateForm'>
												<input type='hidden' name='file' value='VerifyDBHealth.php' />
												<input type="hidden" name="viewDBReport" value="true" />
												<input type='hidden' name='auth_key' value='<?php echo $auth_key; ?>' />
												<input type='hidden' name='updateTableEngine' value='true' />
												<input type='hidden' name='updateEngineForTable' value='' />
											</form>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table id="unhealthyTablesList" width="95%" border="0px" cellpadding="3" cellspacing="0" align="center">
									<tr>
										<td class="fontBold small">Table</td>
										<td class="fontBold small">Character Set</td>
										<td class="fontBold small">Type</td>
									</tr>
									<?php for($i=0; $i<$noOfTables; ++$i) {
											$tableName = $unHealthyTablesList[$i]['name'];
											$engineType = $unHealthyTablesList[$i]['engine'];
											$characterSet = $unHealthyTablesList[$i]['characterset'];
									?>
									<tr>
										<td><?php echo $tableName; ?></td>
										<td><?php echo $characterSet; ?></td>
										<td><font style='color:red;'><?php echo $engineType; ?></font>&nbsp;&nbsp;(<a href='javascript:correctTableEngineType("<?php echo $tableName; ?>");' title='Correct Engine Type'  style='cursor:pointer;'>Fix Now</a>)</td>
									</tr>
									<?php } ?>
								</table>
								<?php } ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</div>
</body>
</html>
<?php } ?>