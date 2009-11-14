<?php
/*+*******************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ********************************************************************************/
set_time_limit(0);
session_start();
$migrationInfo = $_SESSION['migration_info'];
$root_directory = $migrationInfo['root_directory'];
$source_directory = $migrationInfo['source_directory'];
session_destroy();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>vtiger CRM 5 - Configuration Wizard - Finish</title>

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
		<td background="include/install/images/topInnerShadow.gif" align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=0 width=97%>
			<tr>
				<td width=80% valign=top class="cwContentDisplay" align=left>
				<!-- Right side tabs -->
					<table border=0 cellspacing=0 cellpadding=10 width=100%>
					<tr><td class=small align=left><img src="include/install/images/confWizFinish.gif" alt="Configuration Completed" title="Configuration Completed"><br>
					  <hr noshade size=1></td></tr>

					<tr>
					<td align=center class="small" style="height:250px;"> 

<?php


//this is to rename the installation file and folder so that no one destroys the setup
$renamefile = uniqid(rand(), true);

$file_renamed = false;
//@rename("install.php", $renamefile."install.php.txt");
if(!@rename("install.php", $renamefile."install.php.txt"))
{
	if (@copy ("install.php", $renamefile."install.php.txt"))
       	{
        	 unlink($renamefile."install.php.txt");
			$file_renamed = true;
     	}
	else
	{
		echo "<b><font color='red'>We strongly suggest you to rename the install.php file.</font></b>";
	}
}

$mig_file_renamed = false;
//@rename("migrate.php", $renamefile."migrate.php.txt");
if(!@rename("migrate.php", $renamefile."migrate.php.txt"))
{
	if (@copy ("migrate.php", $renamefile."migrate.php.txt"))
       	{
        	 unlink($renamefile."migrate.php.txt");
 			$mig_file_renamed = true;
     	}
	else
	{
		echo "<b><font color='red'>We strongly suggest you to rename the migrate.php file.</font></b>";
	}
}

$dir_renamed = false;
//@rename("install/", $renamefile."install/");
if(!@rename("install/", $renamefile."install/"))
{
	if (@copy ("install/", $renamefile."install/"))
       	{
        	 unlink($renamefile."install/");
        	 $dir_renamed = true;
     	}
	else
	{
		echo "<br><b><font color='red'>We strongly suggest you to rename the install directory.</font></b><br>";
	}
$_SESSION['VTIGER_DB_VERSION']='5.1.0';
}
//populate Calendar data


?>
		<table border=0 cellspacing=0 cellpadding=5 align="center" width="80%" style="background-color:#E1E1FD;border:1px dashed #111111;">
		<tr>
			<td align=center class=small>
			<b>Migration Successfully finished. All new vtigercrm-5.1.0-RC is set to go!</b>
			<hr noshade size=1>
			<div style="width:100%;padding:10px; "align=left>
			<ul>
			<?php 
				if($file_renamed==true){
					echo "<li>Your install.php file has been renamed to ".$renamefile."install.php.txt.";
				}
				if($dir_renamed==true){
					echo "<li>Your install folder too has been renamed to ".$renamefile."install/.";
				}  
				if($mig_file_renamed==true){
					echo "<li>Your migrate.php file has been renamed to ".$renamefile."migrate.php.txt.";
				}  
			?>
			<li>Your older version is available at <?php echo $source_directory;?>.
			<li>Your current source path is <?php echo $root_directory;?>.
			<li>Please log in using the "admin" user name and password of your old installation.
			<li>Do not forget to set the outgoing emailserver, setup accessible from Settings-&gt;Outgoing Server
			</ul>
			<ul>
			<li>Rename htaccess.txt file to .htaccess to control public file access. &nbsp;
			   <a href="javascript:;" onclick="showhidediv();">More Information</a>
			   <div id='htaccess_div' style="display:none">
				<br><br>This .htaccess file will work if "<b>AllowOverride All</b>" is set on Apache server configuration file (httpd.conf) for the DocumentRoot or for the current vtiger path.
			       	<br>If this AllowOverride is set as None ie., "<b>AllowOverride None</b>" then .htaccess file will not take into effect. 
				<br><br>If AllowOverride is None then add the following configuration in the apache server configuration file (httpd.conf) 
				<br><b>&lt;Directory "C:/Program Files/vtigercrm/apache/htdocs/vtigerCRM"&gt;<br>Options -Indexes<br>&lt;/Directory&gt;</b>
				<br>So that without .htaccess file we can restrict the directory listing
			   </div>
			</ul>
			<ul>
			<li><b><font color='#0000FF'>You are very important to us!</font></b>
<li><b> We take pride in being associated with you</li></b>
			<p>
			<b>Talk to us at <a href='http://forums.vtiger.com' target="_blank">forums</a></b>
			<p>
			<b>Discuss with us at <a href='http://blogs.vtiger.com' target="_blank">blogs</a></b>
			<p>
			<b>We aim to be - simply the best. Come on over, there is space for you too!</b>
			</ul>
			</div>

			</td>
		</tr>
		</table>
		<br>	
		<table border=0 cellspacing=0 cellpadding=10 width=100%>
		<tr><td colspan=2 align="center">
				 <form action="index.php" method="post" name="form" id="form">
				 <input type="hidden" name="default_user_name" value="admin">
			 	 <input  type="image" src="include/install/images/cwBtnFinish.gif" name="next" title="Finish" value="Finish" />
				 </form>
		</td></tr>
		</table>		
		</td>

		</tr>
		</table>
		<!-- Master display stops -->
		
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
