<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
session_start();
$auth_key = $_REQUEST['auth_key'];
if($_SESSION['authentication_key'] != $auth_key) {
	die("Not Authorized to perform this operation");
}

require_once('install/VerifyDBHealth.php');

$migrationInfo = $_SESSION['migration_info'];

$dbType = $migrationInfo['db_type'];
$dbHostName = $migrationInfo['db_hostname'];
$oldDbName = $migrationInfo['old_dbname'];
$newDbName = $migrationInfo['new_dbname'];

if($_SESSION[$newDbName.'_'.$dbHostName.'_HealthApproved'] != true) {
	header("Location:install.php?file=PreMigrationActions.php");
} else {
	$innodbEngineCheck = true;
}

$oldVersion = $migrationInfo['old_version'];
$sourceDirectory = str_replace('\\\\','\\',rtrim($migrationInfo['source_directory'],'/'));
$rootDirectory = str_replace('\\\\','\\',rtrim($migrationInfo['root_directory'],'/'));
$adminUserName = $migrationInfo['user_name'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Configuration Wizard - Migration</title>
	<script language="javascript" type="text/javascript" src="include/scriptaculous/prototype.js"></script>		
	<script type="text/javascript" src="include/js/general.js"></script>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

	<br>
	<!-- Table for cfgwiz starts -->

	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="Configuration Wizard" hspace="20" title="Configuration Wizard"></td>
		<td class="cwHeadBg1" align=right><img src="include/install/images/vtigercrm5.gif" alt="vtiger CRM 5" title="vtiger CRM 5"></td>
		<td class="cwHeadBg1" width=2%></td>
	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" colspan=2 align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
		<tr>
			<td class="small" bgcolor="#4572BE" align=center>
				<!-- Master display -->
				<table border=0 cellspacing=0 cellpadding=0 width=97%>
					<tr id="confirmSettingsWindow">
						<td width=80% valign=top class="cwContentDisplay" align=center colspan=2>
							<table width="100%" cellspacing="0" cellpadding="10" border="0">
								<tr>
									<td align="left" colspan="2" class="small">
										<img title="Confirm Configuration Settings" alt="Confirm Configuration Settings" src="include/install/images/confWizConfirmSettings.gif"/><br/>
				  						<hr size="1" noshade=""/>
				  					</td>
				  				</tr>
				  				<tr>
									<td width="80%" align="left" style="padding-left: 10%;" class="small">
										<table width="100%" cellspacing="1" cellpadding="0" border="0" align="center" class="level3">
											<tr>
												<td colspan="2"><strong>Database Configuration</strong><hr size="1" noshade=""/></td>
											</tr>
											<tr>
												<td width="40%" nowrap="">Database Type</td>
												<td nowrap="" align="left"> <font class="dataInput"><i><?php echo $dbType; ?></i></font></td>
											</tr>
											<tr>
												<td width="40%" nowrap="">Old Database Name</td>
													<td nowrap="" align="left"> <font class="dataInput"><i><?php echo $oldDbName; ?></i></font></td>
												</tr>
											<tr>
												<td width="40%" nowrap="">New Database Name</td>
												<td nowrap="" align="left"> <font class="dataInput"><?php echo $newDbName; ?></font></td>
											</tr>
											<tr>
												<td width="40%" nowrap="">InnoDB Engine Check</td>
												<td nowrap="" align="left">
													<?php if ($innodbEngineCheck == 1) { ?>
													<font class="dataInput">Fixed</font>
													<?php } else { ?>
													<font class="dataInput"><span class="redColor">Not Fixed</span></font></td>
													<?php } ?>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td width="80%" align="left" style="padding-left: 10%;" class="small">
										<table width="100%" cellspacing="1" cellpadding="0" border="0" align="center" class="level3">
											<tr>
												<td colspan="2"><strong>Source Configuration</strong><hr size="1" noshade=""/></td>
											</tr>
											<tr>
												<td width="40%">Previous Installation Version</td>
												<td align="left"> <i><?php echo $oldVersion; ?></i></td>
											</tr>
											<tr>
												<td width="40%">Previous Installation Path</td>
												<td align="left"> <i><?php echo $sourceDirectory; ?></i></td>
											</tr>
											<tr>
												<td width="40%">New Installation Path</td>
												<td align="left"> <i><?php echo $rootDirectory; ?></i></td>
											</tr>
											<tr>
												<td width="40%">Admin User Name</td>
												<td align="left"> <i><?php echo $adminUserName; ?></i></td>
											</tr>
										</table>
									</td>
								</tr>
								<tr>
									<td align="left">
										<input type="image" border="0" onclick="window.history.back();" title="Back" alt="Back" id="back" src="include/install/images/cwBtnBack.gif"/>
									</td>
									<td align="right">
										<form action="install.php" name="migrateform" id="migrateform" method="post">
											<input type="hidden" name="auth_key" id="auth_key" value="<?php echo $_SESSION['authentication_key']; ?>" />
											<input type="hidden" name="file" value="MigrationProcess.php" />
											<input type="image" src="include/install/images/cwBtnNext.gif" alt="Next" border="0" title="Migrate" onClick="migrateform.submit();">
										</form>
									</td>
								</tr>
				  			</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
		</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
			<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
		</tr>
	</table>	
	<table border=0 cellspacing=0 cellpadding=0 width=80% align=center>
		<tr>
        	<td class=small align=center> <a href="http://www.vtiger.com" target="_blank">www.vtiger.com</a></td>
      	</tr>
    </table>	    
</body>
</html>