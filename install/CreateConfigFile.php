<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

include('vtigerversion.php');

session_start();

// vtiger CRM version number; do not edit!
$vtiger_version = "5.1.0";
$release_date = "July 2009";

if (isset($_REQUEST['db_hostname']))
{
	if(strpos($_REQUEST['db_hostname'], ":"))
	{
		list($db_hostname,$db_port) = split(":",$_REQUEST['db_hostname']);
	}	
	else
	{
		$db_hostname = $_REQUEST['db_hostname'];
		/*if($db_type == "pgsql")
		     $db_port = '5432';
		else
		     $db_port = '3306';*/
	}	
}
if (isset($_REQUEST['db_username']))$db_username = $_REQUEST['db_username'];

if (isset($_REQUEST['db_password']))$db_password = $_REQUEST['db_password'];

if (isset($_REQUEST['db_name']))$db_name = $_REQUEST['db_name'];

if (isset($_REQUEST['db_type'])) $db_type = $_REQUEST['db_type'];

if (isset($_REQUEST['db_drop_tables'])) $db_drop_tables = $_REQUEST['db_drop_tables'];

if (isset($_REQUEST['db_create'])) $db_create = $_REQUEST['db_create'];

if (isset($_REQUEST['db_populate'])) $db_populate = $_REQUEST['db_populate'];

if (isset($_REQUEST['site_URL'])) $site_URL = $_REQUEST['site_URL'];
 
if (isset($_REQUEST['admin_email'])) $admin_email = $_REQUEST['admin_email'];

if (isset($_REQUEST['admin_password'])) $admin_password = $_REQUEST['admin_password'];
if (isset($_REQUEST['standarduser_email'])) $standarduser_email = $_REQUEST['standarduser_email'];

if (isset($_REQUEST['standarduser_password'])) $standarduser_password = $_REQUEST['standarduser_password'];

if (isset($_REQUEST['currency_name'])) $currency_name = $_REQUEST['currency_name'];

if (isset($_REQUEST['currency_code'])) $currency_code = $_REQUEST['currency_code'];

if (isset($_REQUEST['currency_symbol'])) $currency_symbol = $_REQUEST['currency_symbol'];

if (isset($_REQUEST['ftpserver'])) $ftpserver = $_REQUEST['ftpserver'];

if (isset($_REQUEST['ftpuser'])) $ftpuser = $_REQUEST['ftpuser'];

if (isset($_REQUEST['ftppassword'])) $ftppassword = $_REQUEST['ftppassword'];

// If vtiger charset is set (based on database charset check from last page) use it
if (isset($_REQUEST['vt_charset'])) $vt_charset = $_REQUEST['vt_charset'];
else $vt_charset = 'UTF-8';

// update default port
if ($db_port == '')
{
	if($db_type == 'mysql')
	{
		$db_port = "3306";
	}
	elseif($db_type == 'pgsql')
	{
		$db_port = "5432";
	}
	elseif($db_type == 'oci8')
	{
		$db_port = '1521';
	}
}

$cache_dir = 'cache/';


?>
	
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>vtiger CRM 5 - Configuration Wizard - Config File Creation</title>

    <link rel='stylesheet' type='text/css' href='themes/softed/style.css'></link>
    <script type="text/javascript" src="include/js/en_us.lang.js"></script>
    <script type="text/javascript" src="include/scriptaculous/prototype.js"></script>
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
		<td background="include/install/images/topInnerShadow.gif" align=left><img height="10" src="include/install/images/topInnerShadow.gif" ></td>

	</tr>
	</table>
	<table border=0 cellspacing=0 cellpadding=10 width=80% align=center>
	<tr>
		<td class="small" bgcolor="#4572BE" align=center>
			<!-- Master display -->
			<table border=0 cellspacing=0 cellpadding=0 width=97%>
			<tr>
				<td width=80% valign=top class="cwContentDisplay" align=center>
				<table border=0 cellspacing=0 cellpadding=5 width=95%>
				<tr><td class=small align=left><img id="title_img" src="include/install/images/confWizConfFile.gif" alt="Config File Creation" title="Config File Creation"><img id="title_img1" src="include/install/images/confWizDbGeneration.gif" style="display:none;" alt="Database Generation" title="Database Generation"><br>
					  <hr noshade size=1></td></tr>
				<tr>
					<td align=left><br>
					<table width="100%" cellpadding="0" border="0" align=center class="level3" cellspacing="1">
					<tr><td>
					<?php
					if (isset($_REQUEST['root_directory']))
						$root_directory = $_REQUEST['root_directory'];
		
					if (is_file('config.inc.php'))
					    	$is_writable = is_writable('config.inc.php');
					else
	      					$is_writable = is_writable('.');

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
				      			$buffer = str_replace( "_DBC_SERVER_", $db_hostname, $buffer);
				      			$buffer = str_replace( "_DBC_PORT_", $db_port, $buffer);
				      			$buffer = str_replace( "_DBC_USER_", $db_username, $buffer);
				      			$buffer = str_replace( "_DBC_PASS_", $db_password, $buffer);
				      			$buffer = str_replace( "_DBC_NAME_", $db_name, $buffer);
				      			$buffer = str_replace( "_DBC_TYPE_", $db_type, $buffer);

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
					      		$buffer = str_replace( "_VT_APP_UNIQKEY_", md5(time() + rand(1,9999999) + md5($root_directory)) , $buffer);
							/* replace support email variable */
							$buffer = str_replace( "_USER_SUPPORT_EMAIL_", $admin_email, $buffer);

					      		fwrite($includeHandle, $buffer);
					      		}

				      		fclose($includeHandle);
				      		}

				      	fclose($templateHandle);
				      	}
  
	if ($templateHandle && $includeHandle) {?>
			<div id="config_info">
			<p><strong class="big">Successfully created configuration file (config.inc.php) in :</strong><br>
			<font color="green"><b><?php echo $root_directory; ?></b></font><br>
			The installation will take at least 4 minutes.<br> Grab a coffee,sit back and relax or browse through our <a href='http://blogs.vtiger.com/index.php' target="_blank">blogs</a><br>
			
			</div>	
	<?php } 
	else {?>
			<div id="config_info"><p><strong class="big"><font color="red">Cannot write configuration file (config.inc.php ) in : </font></strong><br><br>
			<font color="green"><b><?php echo $root_directory; ?></b></font><br><br>
			<P>You can continue this installation by manually creating the config.inc.php file and pasting the configuration information below inside.However, you <strong>must</strong> create the configuration file before you continue to the next step.<P><br><br>
	<?php			
	$config = "<?php \n";
 	$config .= "/*********************************************************************************\n";
 	$config .= " * The contents of this file are subject to the SugarCRM Public License Version 1.1.2\n";
 	$config .= " * (\"License\"); You may not use this file except in compliance with the \n";
 	$config .= " * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL\n";
 	$config .= " * Software distributed under the License is distributed on an  \"AS IS\"  basis,\n";
 	$config .= " * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for\n";
 	$config .= " * the specific language governing rights and limitations under the License.\n";
 	$config .= " * The Original Code is:  SugarCRM Open Source\n";
 	$config .= " * The Initial Developer of the Original Code is SugarCRM, Inc.\n";
 	$config .= " * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;\n";
 	$config .= " * All Rights Reserved.\n";
 	$config .= " * Contributor(s): ______________________________________.\n";
 	$config .= "********************************************************************************/\n\n";
 	$config .= "include('vtigerversion.php');\n\n";
 	$config .= "ini_set('memory_limit','64M');\n\n";
 	$config .= "// show or hide calendar, world clock, calculator, chat and FCKEditor\n";
	$config .= "// Do NOT remove the quotes if you set these to false! \n"; 
	$config .= "$CALENDAR_DISPLAY = 'true';\n";
 	$config .= "\$WORLD_CLOCK_DISPLAY = 'true';\n";
 	$config .= "\$CALCULATOR_DISPLAY = 'true';\n";
	$config .= "$CHAT_DISPLAY = 'true';\n"; 
 	$config .= "\$FCKEDITOR_DISPLAY = 'true';\n\n";
 	
 	$config .= "//This is the URL for customer portal. (Ex. http://vtiger.com/portal)\n";
 	$config .= "\$PORTAL_URL = 'http://yourdomain.com/customerportal';\n\n";
 	$config .= "//These two are the HelpDesk support email id and the support name. ";
 	$config .= "(Ex. 'support@vtiger.com' and 'vtiger Support')\n";
 	$config .= "\$HELPDESK_SUPPORT_EMAIL_ID = 'support@yourdomain.com';\n";
 	$config .= "\$HELPDESK_SUPPORT_NAME = 'yourdomain Name';\n\n";
 	
 	$config .= "/* Database configuration\n";
 	$config .= "      db_host_name:     MySQL Database Hostname\n";
 	$config .= "      db_user_name:        MySQL Username\n";
 	$config .= "      db_password:         MySQL Password\n";
 	$config .= "      db_name:             MySQL Database Name\n*/\n";
 	$config .= "\$dbconfig['db_server'] =     '".$db_hostname."';\n";
 	$config .= "\$dbconfig['db_port'] =     ':".$db_port."';\n";
 	$config .= "\$dbconfig['db_username'] =     '".$db_username."';\n";
 	$config .= "\$dbconfig['db_password'] =         '".$db_password."';\n";
 	$config .= "\$dbconfig['db_name'] =             '".$db_name."';\n";
 	$config .= "\$dbconfig['db_type'] = '".$db_type."';\n";
	$config .= "\$dbconfig['db_status'] = 'true';\n";

	$config .= "// TODO: test if port is empty\n";
	$config .= "// TODO: set db_hostname dependending on db_type\n";
 	$config .= "\$dbconfig['db_hostname'] = \$dbconfig['db_server'].\$dbconfig['db_port'];\n\n";

	
	$config .= "// log_sql default value = false\n";
	$config .= "\$dbconfig['log_sql'] = false;\n\n";
	
	$config .= "// persistent default value = true\n";
	$config .= "\$dbconfigoption['persistent'] = true;\n\n";

	$config .= "// autofree default value = false\n";
	$config .= "\$dbconfigoption['autofree'] = false;\n\n";
	
	$config .= "// debug default value = 0\n";
	$config .= "\$dbconfigoption['debug'] = 0;\n\n";

	$config .= "// seqname_format default value = '%s_seq'\n";
	$config .= "\$dbconfigoption['seqname_format'] = '%s_seq';\n\n";

	$config .= "// portability default value = 0\n";
	$config .= "\$dbconfigoption['portability'] = 0;\n\n";

	$config .= "// ssl default value = false\n";
	$config .= "\$dbconfigoption['ssl'] = false;\n\n";

	$config .= "\$host_name = \$dbconfig['db_hostname'];\n\n";
	
	$config .= "\$site_URL ='$site_URL';\n\n";

	$config .= "// root directory path\n";
	$config .= "\$root_directory = '$root_directory';\n\n";

	$config .= "// cache direcory path\n";
	$config .= "\$cache_dir = '$cache_dir';\n\n";

	$config .= "// tmp_dir default value prepended by cache_dir = images/\n";
	$config .= "\$tmp_dir = '$cache_dir"."images/';\n\n";

	$config .= "// import_dir default value prepended by cache_dir = import/\n";
	$config .= "\$tmp_dir = '$cache_dir"."import/';\n\n";

	$config .= "// upload_dir default value prepended by cache_dir = upload/\n";
	$config .= "\$tmp_dir = '$cache_dir"."upload/';\n\n";

	$config .= "// maximum file size for uploaded files in bytes also used when uploading import files\n";
	$config .= "// upload_maxsize default value = 3000000\n";
	$config .= "\$upload_maxsize = 3000000;\n\n";

	$config .= "// flag to allow export functionality\n";
	$config .= "// 'all' to allow anyone to use exports \n";
	$config .= "// 'admin' to only allow admins to export\n";
	$config .= "// 'none' to block exports completely\n";
	$config .= "// allow_exports default value = all\n";
	$config .= "\$allow_exports = 'all';\n\n";

 	$config .= "// Files with one of these extensions will have '.txt' appended to their filename on upload\n";
	$config .= "// upload_badext default value = php, php3, php4, php5, pl, cgi, py, asp, cfm, js, vbs, html, htm\n";
 	$config .= "\$upload_badext = array('php', 'php3', 'php4', 'php5', 'pl', 'cgi', 'py', 'asp', 'cfm', 'js', 'vbs', 'html', 'htm', 'phtml', 'exe', 'bin', 'bat', 'sh', 'dll', 'phps');\n\n";

 	$config .= "// This is the full path to the include directory including the trailing slash\n";
	$config .= "// includeDirectory default value = $root_directory..'include/\n";
 	$config .= "\$includeDirectory = \$root_directory.'include/';\n\n";
	
	$config .= "// list_max_entries_per_page default value = 20\n";
	$config .= "\$list_max_entries_per_page = '20';\n\n";

 	$config .= "// change this number to whatever you want. This is the number of pages that will appear in the pagination. by Raju \n";
 	$config .= "\$limitpage_navigation = '5';\n\n";
	
	$config .= "// define list of menu tabs \n";
	$config .= "//$moduleList = Array('Home', 'Dashboard', 'Contacts', 'Accounts', 'Opportunities', 'Cases', 'Notes', 'Calls', 'Emails', 'Meetings', 'Tasks','MessageBoard'); \n\n";

	$config .= "// history_max_viewed default value = 5\n";
 	$config .= "\$history_max_viewed = '5';\n\n";
 	
 	$config .= "// Map Sugar language codes to jscalendar language codes\n";
 	$config .= "// Unimplemented until jscalendar language files are fixed\n";
 	$config .= "// \$cal_codes = array('en_us'=>'en', 'ja'=>'jp', 'sp_ve'=>'sp', 'it_it'=>'it', 'tw_zh'=>'zh', 'pt_br'=>'pt', 'se'=>'sv', 'cn_zh'=>'zh', 'ge_ge'=>'de', 'ge_ch'=>'de', 'fr'=>'fr');\n\n";

	$config .= "//set default module and action\n";
 	$config .= "\$default_module = 'Home';\n";
 	$config .= "\$default_action = 'index';\n\n";

 	$config .= "//set default theme\n";
 	$config .= "\$default_theme = 'bluelagoon';\n\n";

 	$config .= "// If true, the time to compose each page is placed in the browser.\n";
 	$config .= "\$calculate_response_time = true;\n";

 	$config .= "// Default Username - The default text that is placed initially in the login form for user name.\n";
 	$config .= "\$default_user_name = '';\n";
	
 	$config .= "// Default Password - The default text that is placed initially in the login form for password.\n";
 	$config .= "\$default_password = '';\n";

 	$config .= "// Create default user - If true, a user with the default username and password is created.\n";
 	$config .= "\$create_default_user = false;\n";
 	$config .= "\$default_user_is_admin = false;\n";

 	$config .= "// disable persistent connections - If your MySQL/PHP configuration does not support persistent connections set this to true to avoid a large performance slowdown\n";
 	$config .= "\$disable_persistent_connections = false;\n";
 	$config .= "// Defined languages available.  The key must be the language file prefix.  E.g. 'en_us' is the prefix for every 'en_us.lang.php' file. \n";
 	
 	$language_value = "Array('en_us'=>'US English',)";
 	if(isset($_SESSION['language_keys']) && isset($_SESSION['language_values']))
 	{
 	    $language_value = 'Array(';
 	    $language_keys = explode(',', urldecode($_SESSION['language_keys']));
 	    $language_values = explode(',', urldecode($_SESSION['language_values']));
 	    $size = count($language_keys);
 	    for($i = 0; $i < $size; $i+=1)
 	    {
 	        $language_value .= "'$language_keys[$i]'=>'$language_values[$i]',";
 	    }
 	    $language_value .= ')';
 	}
 	$config .= "\$languages = $language_value;\n";
	$config .= "// Master currency name\n";
 	$config .= "\$currency_name = '$currency_name';\n";
 	$config .= "// Default charset if the language specific character set is not found.\n";
 	$config .= "\$default_charset = 'UTF-8';\n";
 	$config .= "// Default language in case all or part of the user's language pack is not available.\n";
 	$config .= "\$default_language = 'en_us';\n";
 	$config .= "// Translation String Prefix - This will add the language pack name to every translation string in the display.\n";
 	$config .= "\$translation_string_prefix = false;\n";
 	
 	$config .= "//Option to cache tabs permissions for speed.\n";
 	$config .= "\$cache_tab_perms = true;\n\n";
 	
 	$config .= "//Option to hide empty home blocks if no entries.\n";
 	$config .= "\$display_empty_home_blocks = false;\n\n";
 	
 	$config .= "//Disable Stat Tracking of vtiger CRM instance.\n";  
	$config .= "\$disable_stats_tracking = false;\n\n";

 	$config .= "// Generating Unique Application Key\n";
 	$config .= "\$application_unique_key = '".md5(time() + rand(1,9999999) + md5($root_directory)) ."';\n\n";

	$config .= "// trim descriptions, titles in listviews to this value\n";
	$config .= "\$listview_max_textlength = 40;\n\n";

	$config .= "// Maximum time limit for PHP script execution (in seconds)\n";
	$config .= "\$php_max_execution_time = 0;\n\n";

	$config .= "// Set the default timezone as per your preference\n";
	$config .= "//\$default_timezone = '';\n\n";
 	$config .= "?>";
		
			echo "<TEXTAREA class=\"dataInput\" rows=\"15\" cols=\"80\">".$config."</TEXTAREA><br><br>";
			echo "<P>Did you remember to create the config.inc.php file ?</p>";
	
				  
		}	?>
				</div>
				</td>					</tr>
					</table>
				
				</td>
				</tr>
				<tr>
				<td align=right style="height:60px;">
				 <form action="install.php" method="post" name="form" id="form">
				 <!--<form action="install.php" method="post" name="form" id="form"> -->
				 <input type="hidden" name="file" value="CreateTables.php">
				 <input type="hidden" class="dataInput" name="db_hostname" value="<?php if (isset($db_hostname)) echo "$db_hostname"; ?>" />
				 <input type="hidden" class="dataInput" name="db_username" value="<?php if (isset($db_username)) echo "$db_username"; ?>" />
				 <input type="hidden" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" />
				 <input type="hidden" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" />
				 <input type="hidden" class="dataInput" name="db_drop_tables" value="<?php if (isset($db_drop_tables)) echo "$db_drop_tables"; ?>" />
				 <input type="hidden" class="dataInput" name="db_create" value="<?php if (isset($db_create)) echo "$db_create"; ?>" />
				 <input type="hidden" class="dataInput" name="db_populate" value="<?php if (isset($db_populate)) echo "$db_populate"; ?>" />
				 <input type="hidden" class="dataInput" name="admin_email" value="<?php if (isset($admin_email)) echo "$admin_email"; ?>" />
				 <input type="hidden" class="dataInput" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; ?>" />
				 <input type="hidden" class="dataInput" name="standarduser_email" value="<?php if (isset($standarduser_email)) echo "$standarduser_email"; ?>" />
                 <input type="hidden" class="dataInput" name="standarduser_password" value="<?php if (isset($standarduser_password)) echo "$standarduser_password"; ?>" />
				 <input type="hidden" class="dataInput" name="currency_name" value="<?php if (isset($currency_name)) echo "$currency_name"; ?>" />
				 <input type="hidden" class="dataInput" name="currency_code" value="<?php if (isset($currency_code)) echo "$currency_code"; ?>" />
				 <input type="hidden" class="dataInput" name="currency_symbol" value="<?php if (isset($currency_symbol)) echo "$currency_symbol"; ?>" />
				 <input  type="image" title="Install" name="next" value="Next" id="next_btn" src="include/install/images/cwBtnNext.gif" onClick="createtablejs();window.location=('install.php');" />
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
    <script type="text/javascript">
	function createtablejs()
	{
		//document.getElementById('dbcreate_tab').innerHTML = '<div align="left"><b>Database Generation</b></div>';
		//document.getElementById('configfile_tab').className = 'small cwUnSelectedTab';
		//document.getElementById('configfile_tab').innerHTML = '<div align="left">Config File Creation</div>';
		//document.getElementById('dbcreate_tab').className = 'small cwSelectedTab';
		oImg = document.getElementById('title_img').style.display = 'none';
		//oImg = document.getElementById('divId').style.display = 'block';
		document.getElementById("next_btn").style.display = 'none';
		window.document.title = 'vtiger CRM 5 - Configuration Wizard - Database Generation ...';
		VtigerJS_DialogBox.progress('include/install/images/loading.gif');
	}
	</script>
	
	<!-- To prefetch the images for blocking the screen -->
	<img style="display: none;" src="include/install/images/loading.gif">
    <img style="display: none;" src="themes/softed/images/layerPopupBg.gif">
	<!-- END -->
	
</body>
</html>
