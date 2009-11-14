{*<!--
/*********************************************************************************
  ** The contents of this file are subject to the vtiger CRM Public License Version 1.0
   * ("License"); You may not use this file except in compliance with the License
   * The Original Code is:  vtiger CRM Open Source
   * The Initial Developer of the Original Code is vtiger.
   * Portions created by vtiger are Copyright (C) vtiger.
   * All Rights Reserved.
  *
 ********************************************************************************/
-->*}


<script language="JavaScript" type="text/javascript" src="include/js/menu.js"></script>

<form name="Migration" method="POST" action="index.php" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Migration">
<input type="hidden" name="action" value="MigrationCheck">
<input type="hidden" name="migration_option" value="">
<input type="hidden" name="parenttab" value="Settings">
<input type="hidden" id="getmysqlpath" name="getmysqlpath" value="{$GET_MYSQL_PATH}">

<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%" class="small">
   <tr>
	<td class="showPanelBg" valign="top" width="95%"  style="padding-left:20px; "><br />
		<span class="lvtHeaderText"> {$MOD.LBL_SETTINGS} &gt; {$MOD.LBL_UPGRADE_VTIGER}</span>
		<hr noshade="noshade" size="1" />
	</td>
	<td width="5%" class="showPanelBg">&nbsp;</td>
   </tr>
   <tr>
	<td width="98%" style="padding-left:20px;" valign="top">
		<!-- module Select Table -->
		<table width="95%"  border="0" cellspacing="0" cellpadding="0" align="center" class="mailClient">
		   <tr>
			<td class="mailClientBg" width="7"></td>
			<td class="mailClientBg" style="padding-left:10px;padding-top:10px;vertical-align:top;">
				<table width="100%"  border="0" cellpadding="5" cellspacing="0" class="small">
				   <tr>
					<td width="10%"><img src="{'migrate.gif'|@vtiger_imageurl:$THEME}" align="absmiddle"/></td>
					<td width="90%">
						<span class="genHeaderBig">{$MOD.LBL_UPGRADE_VTIGER}</span><br />
						({$MOD.LBL_UPGRADE_FROM_VTIGER_423})
					</td>
				   </tr>
					   <tr>
					<td colspan="2" class="hdrNameBg">
						<span class="genHeaderGray">{$MOD.LBL_STEP} 1 : </span>
				  		<span class="genHeaderSmall">{$MOD.LBL_SELECT_SOURCE}</span><br />
						{$MOD.LBL_STEP1_DESC}<br /><br />
					</td>
				   </tr>
				   <tr bgcolor="#FFFFFF">
					<td align="right" valign="top">
						<input type="radio" name="radio" id="db_details" value="db_details" onclick="fnChangeMigrate()" "{$DB_DETAILS_CHECKED}" />
					</td>
					<td>
						<b>{$MOD.LBL_RADIO_BUTTON1_TEXT}</b><br /><br />
						<b>{$MOD.LBL_NOTE_TITLE}</b> {$MOD.LBL_RADIO_BUTTON1_DESC}
					</td>
				   </tr>
				   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
				   <tr bgcolor="#FFFFFF">
					<td align="right" valign="top">
						<input type="radio" name="radio" id="dump_details" value="dump_details" onclick="fnChangeMigrate()" "{$DUMP_DETAILS_CHECKED}"/>
					</td>
					<td>
						<b>{$MOD.LBL_RADIO_BUTTON2_TEXT}</b><br /><br />
						<b>{$MOD.LBL_NOTE_TITLE}</b> {$MOD.LBL_RADIO_BUTTON2_DESC}
					</td>
				   </tr>
				   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
				   <tr bgcolor="#FFFFFF">
					<td align="right" valign="top">
						<input type="radio" name="radio" id="alter_db_details" value="alter_db_details" onclick="fnChangeMigrate()" "{$ALTER_DB_DETAILS_CHECKED}"/>
					</td>
					<td>
						<b>{$MOD.LBL_RADIO_BUTTON3_TEXT}</b><br /><br /><b>{$MOD.LBL_NOTE_TITLE}</b> {$MOD.LBL_RADIO_BUTTON3_DESC}
					</td>
				   </tr>
				   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>

				   <tr><td colspan="2" height="10"></td></tr>
				   <tr>
					<td colspan="2" class="hdrNameBg">


						<!-- OPTION 1 -->
						<div id="mnuTab" style="display:{$SHOW_DB_DETAILS}">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
							   <tr >
								<td colspan="2">
									<span class="genHeaderGray">{$MOD.LBL_STEP} 2 : </span>
									<span class="genHeaderSmall">{$MOD.LBL_HOST_DB_ACCESS_DETAILS}</span><br /><br />
								</td>
							   </tr>
							   <tr>
								<td width="30%" align="right">{$MOD.LBL_SOURCE_HOST_NAME}</td>
								<td width="70%"><input type="text" name="old_host_name" class="importBox" value="{$OLD_HOST_NAME}" /></td>
							   </tr>
							   <tr>
								<td align="right">{$MOD.LBL_SOURCE_MYSQL_PORT_NO}</td>
								<td><input type="text" name="old_port_no" class="importBox" value="{$OLD_PORT_NO}" /></td>
							   </tr>
							   <tr>
								<td align="right">{$MOD.LBL_SOURCE_MYSQL_USER_NAME}</td>
								<td><input type="text" name="old_mysql_username" class="importBox" value="{$OLD_MYSQL_USERNAME}" /></td>
							   </tr>
							   <tr>
								<td align="right">{$MOD.LBL_SOURCE_MYSQL_PASSWORD}</td>
								<td><input type="text" name="old_mysql_password" class="importBox" value="{$OLD_MYSQL_PASSWORD}" /></td>
							   </tr>
							   <tr>
								<td align="right">{$MOD.LBL_SOURCE_DB_NAME}</td>
								<td><input type="text" name="old_dbname" class="importBox" value="{$OLD_DBNAME}" /></td>
							   </tr>
							</table>
						</div>

						<!-- OPTION 2 -->
						<div id="mnuTab1" style="display:{$SHOW_DUMP_DETAILS}">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
							   <tr >
								<td colspan="2">
									<span class="genHeaderGray">{$MOD.LBL_STEP} 2 : </span>
									<span class="genHeaderSmall">{$MOD.LBL_LOCATE_DB_DUMP_FILE}</span><br /><br />
								</td>
							   </tr>
							   <tr>
								<td width="10%">&nbsp;</td>
								<td width="90%">
									{$MOD.LBL_DUMP_FILE_LOCATION}
									<input type="file" name="old_dump_filename" class="txtBox"  onchange="validateFilename(this);" />
									<input type="hidden" name="old_dump_filename_hidden" value="" />
								</td>
							   </tr>
							   <tr><td colspan="2" height="10"></td></tr>
							   <tr bgcolor="#FFFFFF">
								<td align="right" valign="top"><b>{$MOD.LBL_NOTE_TITLE}</b></td>
								<td>{$MOD.LBL_NOTES_DUMP_PROCESS}</td>
							   </tr>
							</table>
						</div>


						<!-- OPTION 3 -->
						<div id="mnuTab2" style="display:{$SHOW_ALTER_DB_DETAILS}">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
							   <tr>
								<td colspan="3">
									<span class="genHeaderGray">{$MOD.LBL_STEP} 2 : </span>
									<span class="genHeaderSmall">{$MOD.LBL_HOST_DB_ACCESS_DETAILS}</span><br /><br />
								</td>
							   </tr>
							   <tr>
								<td width="5%">&nbsp;</td>
								<td width="20%" align="right">{$MOD.LBL_MYSQL_HOST_NAME_IP}</td>
								<td width="75%"><input type="text" name="alter_old_host_name" class="importBox" value="{$ALTER_OLD_HOST_NAME}" /></td>
							   </tr>
							   <tr>
								<td>&nbsp;</td>
								<td align="right">{$MOD.LBL_MYSQL_PORT}</td>
								<td><input type="text" name="alter_old_port_no" class="importBox" value="{$ALTER_OLD_PORT_NO}" /></td>
							   </tr>
							   <tr>
								<td>&nbsp;</td>
								<td align="right">{$MOD.LBL_MYSQL_USER_NAME}</td>
								<td><input type="text" name="alter_old_mysql_username" class="importBox" value="{$ALTER_OLD_MYSQL_USERNAME}" /></td>
							   </tr>
							   <tr>
								<td>&nbsp;</td>
								<td align="right">{$MOD.LBL_MYSQL_PASSWORD}</td>
								<td><input type="text" name="alter_old_mysql_password" class="importBox" value="{$ALTER_OLD_MYSQL_PASSWORD}" /></td>
							   </tr>
							   <tr>
								<td>&nbsp;</td>
								<td align="right">{$MOD.LBL_DB_NAME}</td>
								<td><input type="text" name="alter_old_dbname" class="importBox" value="{$ALTER_OLD_DBNAME}" /></td>
							   </tr>
							   <tr><td colspan="3" height="10"></td></tr>
							   <tr bgcolor="#FFFFFF">
								<td align="right" valign="top"><b>{$MOD.LBL_NOTE_TITLE}</b></td>
								<td width="90%" colspan="2">

									{$MOD.LBL_RADIO_BUTTON3_PROCESS}
								</td>
							   </tr>

							</table>
						</div>


					</td>
				   </tr>

				   <!-- this if condition is added to display the text box to get the mysql server path -->
				   {if $GET_MYSQL_PATH eq 1}
				   <tr><td colspan="2" height="10"></td></tr>
				   <tr>
					<td colspan="2" class="hdrNameBg">
						<!-- OPTION 3 -->
						<div id="mnuTab3" style="width:100%; display:{$SHOW_MYSQL_PATH}">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
							   <tr>
								<td colspan="2" >
									<span class="genHeaderGray">{$MOD.LBL_STEP} 3 : </span>
									<span class="genHeaderSmall">{$MOD.LBL_ENTER_MYSQL_SERVER_PATH}</span><br>{$MOD.LBL_SERVER_PATH_DESC}<br /><br />
								</td>
							   </tr>
							   <tr>
								<td align="right" width="30%">{$MOD.LBL_MYSQL_SERVER_PATH}</td>
								<td width="70%">
									<input type="text" name="server_mysql_path" class="txtBox" value="{$SERVER_MYSQL_PATH}" />
								</td>
							   </tr>
							</table>
						</div>


					</td>
				   </tr>
				   {/if}
				   <tr>
					<td colspan="2" style="padding:10px;" align="center">
						<input type="submit" name="migrate" value="  {$MOD.LBL_MIGRATE_BUTTON}  "  class="crmbutton small save" onclick="return validate_migration(Migration);"/>
						&nbsp;<input type="submit" name="cancel" value=" &nbsp;{$MOD.LBL_CANCEL_BUTTON}&nbsp; "  class="crmbutton small cancel" onclick="this.form.module.value='Settings';this.form.action.value='index';"/>
 					</td>
				   </tr>
				</table>
			</td>
			<td class="mailClientBg" width="8"></td>
		   </tr>
		  </table>
		<br />
	</td>
	<td>&nbsp;</td>
   </tr>
</table>
<!-- END -->
</form>

<script language="javascript" type="text/javascript">
	//function to show and hide the db_details or dump_details details based on the radio option selected
	function fnChangeMigrate()
	{ldelim}
		var opt_one = document.getElementById('db_details').checked;
		var opt_two = document.getElementById('dump_details').checked;
		var opt_three = document.getElementById('alter_db_details').checked;
		if(opt_one)
		{ldelim}
			document.getElementById('mnuTab').style.display = 'block';
			document.getElementById('mnuTab1').style.display = 'none';
			document.getElementById('mnuTab2').style.display = 'none';
		{rdelim}
		else if(opt_two)
		{ldelim}
			document.getElementById('mnuTab').style.display = 'none';
			document.getElementById('mnuTab1').style.display = 'block';
			document.getElementById('mnuTab2').style.display = 'none';
		{rdelim}
		else
		{ldelim}
			document.getElementById('mnuTab').style.display = 'none';
			document.getElementById('mnuTab1').style.display = 'none';
			document.getElementById('mnuTab2').style.display = 'block';
		{rdelim}

		//show/hide MySQL server path
		if(document.getElementById('getmysqlpath').value == 1 && document.getElementById('mnuTab2').style.display == 'none')
		{ldelim}
			//show MySQL server path
			document.getElementById('mnuTab3').style.display = 'block';
		{rdelim}
		else
		{ldelim}
			//hide MySQL server path
			document.getElementById('mnuTab3').style.display = 'none';
		{rdelim}
	{rdelim}

	//function to validate the input values based on the radio option selected
	function validate_migration(formname)
	{ldelim}

		var error = false;
		var mig_option = '';

		if(document.getElementById("db_details").checked == true)
		{ldelim}
			formname.migration_option.value = 'db_details';
			//check whether the user entered the valid Source MySQL database details when db details selected
			if(trim(formname.old_host_name.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_SOURCE_HOST}";
				error = true;
			{rdelim}
			else if(trim(formname.old_port_no.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_SOURCE_MYSQL_PORT}";
				error = true;
			{rdelim}
			else if(trim(formname.old_mysql_username.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_SOURCE_MYSQL_USER}";
				error = true;
			{rdelim}
			else if(trim(formname.old_dbname.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_SOURCE_DATABASE}";
				error = true;
			{rdelim}
		{rdelim}
		else if(document.getElementById("dump_details").checked == true)
		{ldelim}
			formname.migration_option.value = 'dump_details';
			//check whether the user entered the MySQL File when dump file details selected
			if(trim(formname.old_dump_filename.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_SOURCE_MYSQL_DUMP}";
				error = true;
			{rdelim}
		{rdelim}
		else if(document.getElementById("alter_db_details").checked == true)
		{ldelim}
			formname.migration_option.value = 'alter_db_details';
			//check whether the user entered the valid Source MySQL database details when db details selected
			if(trim(formname.alter_old_host_name.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_HOST}";
				error = true;
			{rdelim}
			else if(trim(formname.alter_old_port_no.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_MYSQL_PORT}";
				error = true;
			{rdelim}
			else if(trim(formname.alter_old_mysql_username.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_MYSQL_USER}";
				error = true;
			{rdelim}
			else if(trim(formname.alter_old_dbname.value) == '')
			{ldelim}
				error_msg = "{$MOD.ENTER_DATABASE}";
				error = true;
			{rdelim}
		{rdelim}
		else
		{ldelim}
			formname.migration_option.value = '';
			error_msg = "{$MOD.SELECT_ANYONE_OPTION}";
			error = true;
		{rdelim}

		//this is added to check whether the getmysql path is true and the user has entered the path or not
		if(error != true)
		{ldelim}
			if(document.getElementById("getmysqlpath").value == 1 && trim(formname.server_mysql_path.value) == '' && document.getElementById("alter_db_details").checked != true)
			{ldelim}
				//alert(document.getElementById("getmysqlpath").value+" Enter the mysql path");
				error_msg = "{$MOD.ENTER_CORRECT_MYSQL_PATH}";
				error = true;
			{rdelim}
			else
			{ldelim}
				//alert(document.getElementById("getmysqlpath").value+" MySQL path found");
				error = false;
			{rdelim}
		{rdelim}

		//if there is any error then alert the user and return false;
		if(error == true)
		{ldelim}
			alert(error_msg);
			return false;
		{rdelim}
		else
		{ldelim}
			return true;
		{rdelim}
	{rdelim}
</script>

