<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

//get php configuration settings.  requires elaborate parsing of phpinfo() output

ob_start();
eval("phpinfo();");
$info = ob_get_contents();
ob_end_clean();

 foreach(explode("\n", $info) as $line) {
           if(strpos($line, "Client API version")!==false)
               $mysql_version = trim(str_replace("Client API version", "", strip_tags($line)));
 }

$current_dir = pathinfo(dirname(__FILE__));
$current_dir = $current_dir['dirname']."/";

$package_dir = $current_dir."packages/5.1.0/optional/";
require_once($current_dir."install/language/en_us.lang.php");
$handle = opendir($package_dir);
$optionalModules = array();
while($optionalModuleFileName = readdir($handle)){
	$moduleNameParts = explode(".",$optionalModuleFileName);
	if($moduleNameParts[count($moduleNameParts)-1] != 'zip'){
		continue;
	}
	array_pop($moduleNameParts);
	$moduleName = implode("",$moduleNameParts);	
	if(isset($optionalModuleStrings[$moduleName.'_description'])) {
		$optionalModules[$moduleName] = $optionalModuleStrings[$moduleName.'_description'];
	}
}
$optionalModuleNames = array_keys($optionalModules);

ob_start();
phpinfo(INFO_GENERAL);
$string = ob_get_contents();
ob_end_clean();

$pieces = explode("<h2", $string);
$settings = array();

if(isset($_REQUEST['filename'])){
	$file_name = $_REQUEST['filename'];
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Configuration Wizard - Installation Check</title>
	<link href="include/install/install.css" rel="stylesheet" type="text/css">
	<link href="themes/softed/style.css" rel="stylesheet" type="text/css">
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
				    <table cellspacing=0 cellpadding=2 width=95% align=center>
				    <tr>
				    <td align=left colspan=2><img src="include/install/images/confWizInstallCheck.gif" alt="Pre Installation Check" title="Pre Installation Check"><br>
					  </td>
					</tr>
					<tr><td colspan=2><hr noshade size=1></td></tr>
				    <tr>
				    	<td colspan=2 align="left">
				    		<table cellpadding="0" cellspacing="1" align=center width="100%" class="level3">
				    			<tr>
				    			<td colspan=2 style="font-size:13;">
				    				<strong>Select Optional Modules to Install :</strong>
				    				<hr size="1" noshade=""/>
				    			</td>
				    			</tr>
				    			<tr >
									<td align=left width=50% valign=top>
										<table cellpadding="5" cellspacing="1" align=right width="100%" border="0">

									<?php
										
										foreach($optionalModules as $moduleName=>$description) {
									?>
											<tr class='level1'>
				        						<td class='small' width= "5%" valign=top align="right"><input type="checkbox" id="<?php echo $moduleName; ?>" name="<?php echo $moduleName; ?>" value="<?php echo $moduleName; ?>" checked onChange='ModuleSelected("<?php echo $moduleName; ?>");'></td>
												<td class='small' valign=top ><?php echo $moduleName; ?></td>
												<td class='small' valign=top ><i><?php echo $description; ?></i></td>
											</tr>
									<?php
										}
									?>
				       				</table>
								<br>
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr valign=top width="50%">				
				<td align=left>
					<form action="javascript:window.history.back();" name="form1" id="form1">
						<input type="image" src="include/install/images/cwBtnBack.gif" alt="Back" border="0" title="Back" onClick="window.history.back();">
					</form>
				</td>
				<td align=right style="vertical-align: middle;">
					<form action="install.php" method="post" name="form" id="form">
						<input type="hidden" value="<?php echo implode(":",$optionalModuleNames)?>" id='selected_modules' name='selected_modules' />  
		                <?php echo '<input type="hidden" name="file" value="'.$file_name.'" />'; ?>
						<input type="image" src="include/install/images/cwBtnNext.gif" alt="Next" border="0" title="Next" onClick="submit();">
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
    	
<script language="javascript">
var selected_modules = '<?php echo implode(":",$optionalModuleNames)?>';

function ModuleSelected(module){
	if(document.getElementById(module).checked == true){
		if(selected_modules==''){
			selected_modules = selected_modules+document.getElementById(module).value;
		} else {
			selected_modules = selected_modules+":"+document.getElementById(module).value;
		}
	} else {
		if(selected_modules.indexOf(":"+module+":")>-1){
			selected_modules = selected_modules.replace(":"+module+":",":")
		} else if(selected_modules.indexOf(module+":")>-1){
			selected_modules = selected_modules.replace(module+":","")
		} else if(selected_modules.indexOf(":"+module)>-1){
			selected_modules = selected_modules.replace(":"+module,"")
		} else {
			selected_modules = selected_modules.replace(module,"")
		}
	}
	document.getElementById('selected_modules').value = selected_modules;
}
</script>
</body>
</html>	
