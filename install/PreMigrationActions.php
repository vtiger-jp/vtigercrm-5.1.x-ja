<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

require_once('install/VerifyDBHealth.php');

$migrationInfo = $_SESSION['migration_info'];
$source_directory = $migrationInfo['source_directory'];
require_once($source_directory.'config.inc.php');
$dbHostName = $dbconfig['db_hostname']; 
$dbName = $dbconfig['db_name'];

$newDbForCopy = $newDbName = $migrationInfo['new_dbname'];
if($dbName == $newDbForCopy) {
	$newDbForCopy = '';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Configuration Wizard - Pre-Migration Tools</title>
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="include/js/general.js"></script>
	<script type="text/javascript" src="include/scriptaculous/prototype.js"></script>
	<script type="text/javascript" src="modules/com_vtiger_workflow/resources/jquery-1.2.6.js"></script>
	<script type="text/javascript">
		jQuery.noConflict();
		function fixDBHealth(){
			VtigerJS_DialogBox.progress();
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=VerifyDBHealth.php&ajax=true&updateTableEngine=true&updateEngineForAllTables=true&auth_key='+value;
			jQuery.post(url,function(data,status){
				fnvshNrm('responsePopupContainer');
				jQuery('#responsePopupContainer').show();
				var element = jQuery('#responsePopup');
				if(status == 'success'){
					if(trim(data) == 'TABLE_TYPE_FIXED'){
						element.attr('innerHTML', 'Database status was successfully fixed');
					} else {
						element.attr('innerHTML', 'Failed to fix the table types');
					}
				}else{
					element.attr('innerHTML', 'Failed to fix the table types');
				}
				jQuery('#databaseFixMessageDiv').hide();
				jQuery('#dbMirrorCopy').hide();
				VtigerJS_DialogBox.hideprogress();
				placeAtCenter(document.getElementById('responsePopupContainer'));
			});			
		}
		
		function viewDBReport(){
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=VerifyDBHealth.php&ajax=true&viewDBReport=true&auth_key='+value;
			window.open(url,'DBHealthCheck', 'width=700px, height=500px, resizable=1,menubar=0, location=0, toolbar=0,scrollbars=1');			
		}
		
		function getDbDump(){
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=MigrationDbBackup.php&mode=dump&auth_key='+value;
			window.open(url,'DatabaseDump', 'width=800px, height=600px, resizable=1,menubar=0, location=0, toolbar=0,scrollbars=1');
		}
		
		function doDBCopy(){
			var dbName = jQuery('#newDatabaseName').attr('value');
			if (trim(dbName) == '') {
				alert("Please specify new database name");
				jQuery('#newDatabaseName').focus();
				return false;
			}
			var rootUserName = jQuery('#rootUserName').attr('value');
			if (trim(rootUserName) == '') {
				alert("Please specify root user name");
				jQuery('#rootUserName').focus();
				return false;
			}
			VtigerJS_DialogBox.progress();
			var rootPassword = jQuery('#rootPassword').attr('value');			
			var value = jQuery('#auth_key').attr('value');
			var url = 'install.php?file=MigrationDbBackup.php&mode=copy&auth_key='+value;
			url += ('&newDatabaseName='+dbName+'&rootUserName='+rootUserName+'&rootPassword='+rootPassword+'&createDB=true');
			jQuery.post(url,function(data,status){
				fnvshNrm('responsePopupContainer');
				jQuery('#responsePopupContainer').show();
				var element = jQuery('#responsePopup');
				if(status == 'success'){
					if(data != 'true' && data != true){
						element.attr('innerHTML', '<span class="redColor">Failed</span> to create database copy, please do it manually.');
					}else{
						element.attr('innerHTML', 'Database copy was successfully created.<br />Click Next &#187; to proceed');
					}
				}else{
					element.attr('innerHTML', '<span class="redColor">Failed</span> to create database copy, please do it manually.');
				}
				jQuery('#dbMirrorCopy').hide();
				VtigerJS_DialogBox.hideprogress();
				placeAtCenter(document.getElementById('responsePopupContainer'));
			});
		}
		
		function showCopyPopup(){
			fnvshNrm('dbMirrorCopy');
			jQuery('#dbMirrorCopy').show();
			placeAtCenter(document.getElementById('dbMirrorCopy'));
		}
		
	</script>
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
					
					<tr>
						<td width=80% valign=top class="cwContentDisplay" align=center>
						<!-- Right side tabs -->
							<table cellspacing=5 cellpadding=2 width=95% align=center>
								<tr>
										<td align="left" colspan="2" class="small">
											<img title="Pre Migration Tools" alt="Pre Migration Tools" src="include/install/images/confWizPreMigrationTools.gif"/><br/>
					  						<hr size="1" noshade=""/>
					  					</td>
					  				</tr>
								<?php if($_SESSION[$newDbName.'_'.$dbHostName.'_HealthApproved'] != true) { ?>
								<tr>
									<td colspan=2>
										<div id="databaseFixMessageDiv" class="helpmessagebox paddingPoint5em smallFont" align="left">
											<span class="redColor fontBold">Important:</span>
											<hr />											
											Your database table engine is not the recomended engine 'Innodb'. Please make sure to change the engine before migration.<br/>
											<br />
											<a href="javascript:void(0)" onclick="fixDBHealth();">Fix Now</a>&nbsp; | &nbsp;<a href="javascript:void(0)" onclick="viewDBReport();">View Report</a>
										</div>
									</td>
								</tr>								
								<?php } ?>
								<tr>
									<td colspan=2>
										<table cellpadding="0" cellspacing="1" align=right width="100%" class="level3">
											<tr >
												<td align=left width=50% valign=top>
													<table cellpadding="5" cellspacing="1" align=right width="100%" border="0">
														<tr>
															<td width="48%" align="left">
																<table width="100%" cellspacing="0" cellpadding="5" border="0">
																	<tr>
																		<td width="50" valign="top" rowspan="2">
																			<input type="image" src="include/install/images/dbDump.gif" alt="DB Dump Download" border="0" title="DB Dump Download" onClick="getDbDump();">
																		</td>
																		<td valign="bottom" class="heading2">Database Backup</td>
																	</tr>
																	<tr>
																		<td valign="top" class="mediumLineHeight">
																			<b>Have not taken the database backup yet?</b><br>
																			<b>&#171; Click</b> on the left icon to start the dump and <b>Save</b> the copy of output.<br><br>
																			<div class="helpmessagebox"><b>Note</b>:<br> This process may take longer time depending on the database size.</div>
																		</td>
																	</tr>
																</table>
															</td>
															<td height="100%" width="2%" style="border-left:2px dotted #999999;"></td>
															<td width="48%" align="left">
																<table width="100%" cellspacing="0" cellpadding="5" border="0">
																	<tr>
																		<td width="50" valign="top" rowspan="2">
																			<input type="image" src="include/install/images/dbCopy.gif" alt="DB Copy" border="0" title="DB Copy" onClick="showCopyPopup();">
																		</td>
																		<td valign="bottom" class="heading2">Database Copy</td>
																	</tr>
																	<tr>
																		<td valign="top" class="mediumLineHeight">
																			<b>Are you migrating to new database?</b><br>
																			<b>&#171; Click</b> on the left icon to proceed if you have not setup new database with earlier data.																			
																			<br><br>
																			<div class="helpmessagebox"><b>Recommended</b>:<br>
																			Use tools like (mysql, phpMyAdmin) to setup new database with data.
																			</div>
																		</td>
																	</tr>
																</table>
															</td>
														</tr>
													</table>
													<br>
												</td>
											</tr>
										</table>
									</td>
								</tr>
								<tr valign=top>
									<td align=left >
										<input type="image" src="include/install/images/cwBtnBack.gif" alt="Back" border="0" title="Back" onClick="window.history.back();">
									</td>
									<td align=right>
										<form action="install.php" name="migrateform" id="migrateform" method="post">
											<input type="hidden" name="auth_key" id="auth_key" value="<?php echo $_SESSION['authentication_key']; ?>" />
											<input type="hidden" name="file" value="ConfirmMigrationConfig.php" />
											<input type="hidden" name="forceDbCheck" value="true" />											
											<input type="image" src="include/install/images/cwBtnNext.gif" alt="Next" border="0" title="Next" onClick="migrateform.submit();">
										</form>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<!-- Master display stops -->
				<br>
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
<div id="dbMirrorCopy" class="posLayPopup" style="display: none;">
	<div class="floatRightTiny" onmouseover="this.className= 'floatRightTinyOn';" onmouseout="this.className= 'floatRightTiny';"><a href="javascript: void(0);" onClick="fninvsh('dbMirrorCopy');"><img src="themes/images/close.gif" border=0></a></div>
	<div class="paddingPoint5em"><b>Copy your Existing database into New Database to be used for migration</b></div>
	<table cellpadding="5" cellspacing="2" width="100%" border="0">
		<tbody>
			<tr class="dvtCellLabel">
				<td width="25%" nowrap valign='top'>New Database Name:<sup><font class="redColor">*</font></sup></td>
				<td>
					<input type='text' class="detailedViewTextBox" name='newDatabaseName' id='newDatabaseName' value='<?php echo $newDbForCopy ?>'>
					<br>If database exists it will be recreated.			
				</td>								
			</tr>
			<tr class="dvtCellLabel">
				<td width="25%" nowrap valign='top'>Root User Name:<sup><font class="redColor">*</font></sup></td>
				<td><input type='text' class="detailedViewTextBox" name='rootUserName' id='rootUserName' value=''>
					<br>Should have privilege to CREATE DATABASE
				</td>
			</tr>
			<tr class="dvtCellLabel">
				<td width="25%">Root Password:</td>
				<td><input type='password' class="detailedViewTextBox" name='rootPassword' id='rootPassword' value=''></td>
			</tr>
			<tr class="dvtCellLabel">
				<td colspan="2" align="center"><input type='button' class='crmbuttom small create' name='copy' value='Copy Now' onclick='doDBCopy();'></td>
			</tr>
		</tbody>
	</table>
	<br>
	<div class="helpmessagebox"><span class='redColor fontBold'>Note:</span> This process may take longer time based on your database size.</div>
</div>
<div id='responsePopupContainer' class="posLayPopup" style="display: none;" align="center">
	<div class="floatRightTiny" onmouseover="this.className= 'floatRightTinyOn';" onmouseout="this.className= 'floatRightTiny';"><a href="javascript: void(0);" onClick="fninvsh('responsePopupContainer');"><img src="themes/images/close.gif" border=0></a></div>
	<div id='responsePopup' style="margin-top: 1.1em;" class="fontBold">&nbsp;</div>
</div>
</body>
</html>