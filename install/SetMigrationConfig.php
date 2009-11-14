<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
session_start();
$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";

if (is_file("config.php") && is_file("config.inc.php")) {
	require_once("config.inc.php");	
	$cur_dir_path = false;
	if(!isset($dbconfig['db_hostname']) || $dbconfig['db_status']=='_DB_STAT_') {
		$cur_dir_path = true;
	}
														
	global $dbconfig;

	if(isset($_REQUEST['root_directory'])) $root_directory = $_REQUEST['root_directory'];
	else $root_directory = $current_dir;

	if(isset($_REQUEST['source_directory'])) $source_directory = $_REQUEST['source_directory'];
	else $source_directory = '';

} else {
	!isset($_REQUEST['root_directory']) ? $root_directory = $current_dir : $root_directory = stripslashes($_REQUEST['root_directory']);
	!isset($_REQUEST['source_directory']) ? $source_directory = "" : $source_directory = stripslashes($_REQUEST['source_directory']);
}

if(isset($_REQUEST['selected_modules'])) {
	$_SESSION['selectedOptionalModules'] = $_REQUEST['selected_modules'] ;
}

include("modules/Migration/versions.php");
$version_sorted = $versions;
uasort($version_sorted,version_compare);
$version_sorted = array_reverse($version_sorted,true);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Configuration Wizard - System Configuration</title>

    <link rel='stylesheet' type='text/css' href='themes/softed/style.css'></link>
    <script type="text/javascript" src="include/js/en_us.lang.js"></script>
    <script type="text/javascript" src="include/scriptaculous/prototype.js"></script>
    <script type="text/javascript" src="include/js/general.js"></script>

	<link href="include/install/install.css" rel="stylesheet" type="text/css">
</head>

<body class="small cwPageBg" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<script type="text/javascript" language="Javascript">

function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	// Here we decide whether to submit the form.
	if (trim(form.source_directory.value) =='') {
		isError = true;
		errorMessage += "\n path";
		form.source_directory.focus();
	}
	if (trim(form.user_name.value) =='') {
		isError = true;
		errorMessage += "\n username";
		form.user_name.focus();
	}
	if (trim(form.new_dbname.value) =='') {
		isError = true;
		errorMessage += "\n database name";
		form.new_dbname.focus();
	}
	if(form.old_version.value == ""){
		alert("Please Select Previous Insallation Version");
		form.old_version.focus();
		return false;
	}		
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert("Missing required fields:" + errorMessage);
		return false;
	}
return true;
}

function verify_credentials(){
	VtigerJS_DialogBox.progress('include/install/images/loading.gif');
	
	var source_path = document.getElementById("source_directory").value;
	var root_directory = document.getElementById("root_directory").value;
	var user_name = document.getElementById("user_name").value;
	var old_version = document.getElementById("old_version").value;
	var user_pwd = document.getElementById("password").value;
	var new_dbname = document.getElementById("new_dbname").value;
	new Ajax.Request(
		'migrate.php',
		{queue: {position: 'end', scope: 'command'},
			method: 'post',
			postBody: 'migration_verify=true&source_path='+source_path+'&user_name='+user_name+'&user_pwd='+user_pwd+'&old_version='+old_version+'&new_dbname='+new_dbname+'&root_directory='+root_directory,
			onComplete: function(response) {
					var validationFailed = true;
 					var str = response.responseText
 					str = trim(str);
 					if(str.indexOf('NO_CONFIG_FILE') > -1){
						alert("The Source you have specified doesn't have a config file. \n Please provide a proper Source.");
					}
					else if(str.indexOf('NO_USER_PRIV_DIR') > -1){
						alert("The Source specified doesn't have a user privileges directory. \n Please provide a proper Source.");
					}
					else if(str.indexOf('NO_SOURCE_DIR') > -1){
						alert("The Source specified doesn't seem to be existing. \n Please provide a proper Source.");
					}
					else if(str.indexOf('NO_STORAGE_DIR') > -1){
						alert("The Source specified doesn't have a Storage directory. \n Please provide a proper Source.");
					}
					else if(str.indexOf('NOT_VALID_USER') > -1){
						alert("Not a valid user. Provide an Admin user login details");
					}
					else if(str.indexOf('ERR -') > -1){
						alert(str);
					}
					else if(str.indexOf('FAILURE -') > -1){
						alert(str);
					}
					else
					{
						validationFailed = false;
						document.installform.submit();
						return true;
					}
					if (validationFailed == true) {
						VtigerJS_DialogBox.hideprogress();
					}
			}
		}
	);
	return false;
}
</script>

<br>
	<!-- Table for cfgwiz starts -->
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td class="cwHeadBg" align=left><img src="include/install/images/configwizard.gif" alt="Configuration Wizard" hspace="20" title="Configuration Wizard"></td>
		<td class="cwHeadBg1" align=right><img src="include/install/images/vtigercrm5.gif" alt="vtiger CRM 5" title="vtiger CRM 5"></td>
		<td class="cwHeadBg1" width=2%></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td background="include/install/images/topInnerShadow.gif" align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=10 width=85% align=center>
	<tr valign="top">
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=10 width=97%>
				<tr>
					<td width=80% valign=top class="cwContentDisplay" align=left>
			    		<table border=0 cellspacing=0 cellpadding=5 width=100%>
			    			<tr>
			    				<td colspan=2 class=small align=left>
			    					<img src="include/install/images/confWizSysConfig.gif" alt="System Configuration" title="System Configuration"><br>
				  					<hr noshade size=1>
				  				</td>
				  			</tr>
			    			<tr valign="top">
								<td align=left class="small" style="padding-left:5px" width="60%">
							    	<form action="install.php" method="post" name="installform" id="form" name="setConfig">
										<input type="hidden" name="file" value="PreMigrationActions.php" />
										<table width="100%" cellpadding="4" align=center border="0" cellspacing="0" class="level3"><tbody>
											<tr>
												<td colspan=2><strong>Previous Installation Information</strong><hr size="1" noshade=""/></td>
											</tr>
											<tr>
												<td  nowrap width = 35%>Previous Installation Path<sup><font color=red>*</font></sup></td>
												<td align="left">
													<?php
													if($cur_dir_path == true){
													?>					
													<input  class="small" type="text" name="source_directory" id="source_directory" value="<?php if (isset($source_directory)) echo "$source_directory"; ?>" size="50" /> 
													<?php
													} else {
														echo $root_directory;
													?>					
													<input  class="small" type="hidden" name="source_directory" id="source_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="50" /> 
													<?php
													}
													?>
													<input class="dataInput" type="hidden" name="root_directory" id="root_directory" value="<?php if (isset($root_directory)) echo "$root_directory"; ?>" size="40" />			
												</td>
											</tr>
											<tr>
												<td width = 35% >Previous Installation Version<sup><font color=red>*</font></sup></td>
												<td align="left">
													<select class="small" name='old_version' id='old_version'>
														<?php
															if(!isset($_SESSION['VTIGER_DB_VERSION'])){ 
																echo "<option value='' selected>--SELECT--</option>";
															} else {
																echo "<option value=''>--SELECT--</option>";
															}
																
															foreach($version_sorted as $index=>$value){
																if($value==$_SESSION['VTIGER_DB_VERSION'] && isset($_SESSION['VTIGER_DB_VERSION']))
																	echo "<option value='$index' selected>$value</option>";
																else
																	echo "<option value='$index'>$value</option>"; 
															}
														?>
													</select>
													</select>
												</td>
											</tr>
											<tr>
												<td width = 35% >Admin Username<sup><font color=red>*</font></sup></td>
												<td align="left"><input class="small" type="text" name="user_name" id="user_name" value="<?php if (isset($user_name)) echo $user_name; else echo 'admin';?>" size="50" /> </td>
											</tr>
											<tr>
												<td width = 35%>Admin Password<sup><font color=red></font></sup></td>
												<td align="left"><input class="small" type="password" name="password" id="password" value="" size="50" /> </td>
											</tr>
											<tr>
												<td width = 35%>Database Name for Migration<sup><font color=red>*</font></sup></td>
												<td align="left"><input class="small" type="text" name="new_dbname" id="new_dbname" value="" size="50" /> </td>
											</tr>
										</table>
									</form>
								</td>
								<td class="small" style="padding-left:5px" with="40%" height="100%">
									<table width="100%" cellpadding="0" align=center border="0" cellspacing="0">
										<tr>
											<td>
												<div class="helpmessagebox paddingPoint5em">
													<span class="redColor fontBold">Important Note:</span>
													<hr />												
													<ul>
														<li>Make sure to take <b>backup (dump) of database</b> before proceeding further.</li>
														<li><b>Migrate using new database</b>?<br>
															<ol style='padding: 0; padding-left: 15px;'>
															<li>Create the database first with UTF8 charset support.<br>
															<font class='fontBold'>eg:</font> CREATE DATABASE <newDatabaseName> DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;</li>
															<li><b>Copy the data (dump)</b> from earlier database into this new one.</li>
															</ol>
														</li>
													</ul>
  												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							
							<tr>
								<td align="left">
									<input type="image" src="include/install/images/cwBtnBack.gif" alt="Back" border="0" title="Back" onClick="window.history.back();">
								</td>
								<td align="right">
									<input type="image" src="include/install/images/cwBtnNext.gif" id="starttbn" alt="Next" border="0" title="Next" onClick="if(verify_data(installform) == true) return verify_credentials()">
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
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td background="include/install/images/bottomGradient.gif"><img src="include/install/images/bottomGradient.gif"></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
		<td align=center><img src="include/install/images/bottomShadow.jpg"></td>
	</tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 width=85% align=center>
	<tr>
    	<td class=small align=center> <a href="http://www.vtiger.com" target="_blank">www.vtiger.com</a></td>
	</tr>
</table>

<!-- To prefetch the images for blocking the screen -->
<img style="display: none;" src="include/install/images/loading.gif">
<img style="display: none;" src="themes/softed/images/layerPopupBg.gif">
<!-- END -->
</body>
</html>