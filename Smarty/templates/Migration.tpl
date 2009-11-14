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
<script type="text/javascript">
function showhidediv()
{ldelim}
	var div_style = document.getElementById("mig_info_div").style.display;
	if(div_style == "inline")
		document.getElementById("mig_info_div").style.display = "none";
	else
		document.getElementById("mig_info_div").style.display = "inline";
		
{rdelim}
</script>

<form name="Migration" method="POST" action="index.php" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
	<input type="hidden" name="parenttab" value="Settings">
	<input type="hidden" name="module" value="Migration">
	<input type="hidden" name="action" value="MigrationStep1">

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
							({$MOD.LBL_UPGRADE_FROM_VTIGER_5X})
						</td>
					   </tr>

					   <!-- Migration Notes - STARTS -->
					   <tr>
						<td colspan="2" class="hdrNameBg">
							<span class="genHeaderGray">Please read <a href="javascript:;" onclick="showhidediv();"><u>this migration notes</u></a>
                            				<span class="genHeaderSmall">before you proceed further.</span>
							<br />
			   			   <div id='mig_info_div' class='small' style="display:none;">
					  	   <ul>
							<li><font color="red">Changes made to database during migration cannot be reverted back. So we highly recommend to take database dump of the current working database before migration. </font>

							<li><font color="red"> Also we recommend to do the migration in the following way </font>
							<br>
								1. Take the dump of currect database (old database which we want to migrate). <a href='#take_db_dump'><b>Help</b></a><br />
								2. Edit the dump file, find and replace the string "latin1" with "utf8" in all places of the dump file ie., we have to find CHARSET=latin1 and replace with CHARSET=utf8 that appears along with the CREATE sql statement<br />
								3. Create a new database with default charset as utf8. <a href='#create_db'><b>Help</b></a><br>
								4. Apply the dump to this newly created database. <a href='#store_db_dump'><b>Help</b></a><br>
								5. Change the dbname into "new_databasename" in config.inc.php file parallel to index.php<br>

								6. Now run the migration ( from Old version to latest version )<br /><br />
							****************************************************************************************************
							</ul>
							<ul>
							<li> <a name='take_db_dump'></a> <b>How to take database dump?</b><br />
								1. Go inside mysql/bin directory from terminal (linux) or command prompt (windows)<br />
								2. Execute the following command to take database dump<br />&nbsp;&nbsp;
									mysqldump --user=mysql_username --password=mysql-password -h hostname --port=mysql_port &nbsp;database_name &gt; dump_filename<br />&nbsp;&nbsp;
									You can find the MySQL credentials in config.inc.php file.<br />
							<li> <a name='create_db'></a> <b>How to create a database?</b><br />
								1. Go inside mysql/bin directory from terminal (linux) or command prompt (windows)<br />
								2. Execute the following command to enter into mysql prompt<br />&nbsp;&nbsp;
									mysql --user=mysql_username --password=mysql-password -h hostname --port=mysql_port<br />&nbsp;&nbsp;
									You can find the MySQL credentials in config.inc.php file.<br />
								3. Execute the following command to create a new database<br />&nbsp;&nbsp;
									create database new_db_name;<br />&nbsp;&nbsp;
									You can set utf8 as default character set for the database on creation time through the following command:<br />&nbsp;&nbsp;
									create database new_db_name DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;<br />&nbsp;&nbsp;
									To change the default character set for an existing database you can use<br />&nbsp;&nbsp;
									alter database old_db_name DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;<br />&nbsp;&nbsp;
									More Information about database UTF-8 support is <a href="http://www.vtiger.com/products/crm/help/5.1.0/vtiger_CRM_Database_UTF8Config.pdf" target="_new"><b>here</b></a>.<br />
							<li><a name='store_db_dump'></a> <b>How to store the data from database dump file to a new database?</b><br />
								1. Edit the database dump file<br />&nbsp;&nbsp;
									SET FOREIGN_KEY_CHECKS = 0; =&gt; add this line at the start of the dump file.<br />&nbsp;&nbsp;
									SET FOREIGN_KEY_CHECKS = 1; =&gt; add this line at the end of the dump file.<br />
								2. Go inside mysql/bin directory from terminal (linux) or command prompt (windows) and ensure that the database dump file is available here.<br />
								3. Execute the following command to store the database dump to new database<br />&nbsp;&nbsp;
									mysql --user=mysql_username --password=mysql-password -h hostname --force --port=mysql_port &nbsp;database_name &lt;  dump_filename <br />&nbsp;&nbsp;
									You can find the MySQL credentials in config.inc.php file<br />
							<li><b>How to use the newly migrated database?</b><br />
									Once we done the migration, we have to restore the following folders from old vtiger installation to new installation<br />&nbsp;&nbsp;
									storage/ - which contains the attachment files<br />&nbsp;&nbsp;
									test/ - which contains some image files<br />&nbsp;&nbsp;
									user_privileges/ - which contains the access privileges for the users and some more files
						   </ul>
						   </div>
						   </span>
						   <br/>
						</td>
					   </tr>
					   <!-- Migration Notes - ENDS -->



					   <tr>
						<td colspan="2" class="hdrNameBg">
							<span class="genHeaderGray">{$MOD.LBL_STEP} 1 : </span>
					  		<span class="genHeaderSmall">{$MOD.LBL_SELECT_SOURCE}</span><br />
							{$MOD.LBL_PATCH_OR_MIGRATION}<br /><br />
						</td>
					   </tr>
					   <tr bgcolor="#FFFFFF">
						<td align="right" valign="top">
							<input type="radio" name="radio" id="migration" value="migration" onclick="this.form.action.value='MigrationStep1'; showSource()" checked/>
						</td>
						<td>
							<b>Migration from vtiger 4.2.x (4.2Patch2/4.2.3/4.2.4) to Current Version ({$CURRENT_VERSION})</b><br /><br />
							<b>{$MOD.LBL_NOTE_TITLE}</b> Used to migrate 4.2Patch2/4.2.3/4.2.4 to Current ({$CURRENT_VERSION}) Version. This option will need source ie., 4.2.x database details which will be get in next page
						</td>
					   </tr>
					   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
					   <tr bgcolor="#FFFFFF">
				
						<td></td>
						<td>{if !$MIG_CHECK}&nbsp;<font color='red'><b>Versions in database and source file are same. You cannot do 5.x migration. Please check the db and then do necessary steps.</b></font><br><br>{$CHARSET_CHECK}</td>{/if}
				           </tr>
					   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
					   <tr bgcolor="#FFFFFF">
						<td align="right" valign="top">
							<input type="radio" name="radio" id="patch" value="patch"  {if !$MIG_CHECK} disabled {/if} onclick="this.form.action.value='PatchApply'; showSource();"/>
						</td>
						<td>
							<b>Upgrade my vtiger 5.x version to Current Version ({$CURRENT_VERSION})</b><br /><br />
							<b>{$MOD.LBL_NOTE_TITLE}</b> This option will apply the dbchanges from Source 5.x version to the Current ({$CURRENT_VERSION}) version
						</td>
					   </tr>
					   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
					   <tr><td colspan="2" bgcolor="#FFFFFF" height="10"></td></tr>
	
					   <tr><td colspan="2" height="10"></td></tr>
					   <tr>
						<td colspan="2" class="hdrNameBg">
	
							<!-- Source Version Combo box div - starts -->
							<div id="mnuTab" style="display:none">
							<table width="100%" border="0" cellpadding="5" cellspacing="0" class="small">
							   <tr >
								<td colspan="2">
									<span class="genHeaderGray">{$MOD.LBL_STEP} 2 : </span>
									<span class="genHeaderSmall">Select Source Version</span><br />(Database Changes between this source version to current version will be applied)<br />
								</td>
							   </tr>
							   <tr>
								<td width="10%">&nbsp;</td>
								<td width="90%">
								<b><font color="red">
NOTE&nbsp;:&nbsp;{$APP.add_at_end_of_file} {$APP.before_migration}</font></b><br>// trim descriptions, titles in listviews to this value<br>$listview_max_textlength = 40;<br><br>
									Source vtiger Version
									{$SOURCE_VERSION}
								</td>
							   </tr>
							   <tr><td colspan="2" height="10"></td></tr>
							</table>
							</div>
							<!-- Source Version Combo box div - ends -->


		
						</td>
					   </tr>

					   <tr>
						<td colspan="2" style="padding:10px;" align="center">
							<input type="submit" name="migrate" value="  {$MOD.LBL_MIGRATE_BUTTON}  "  class="crmbutton small save" onclick="return getConfirmation('{$CURRENT_VERSION}');"/>
							&nbsp;<input type="button" name="cancel" value=" &nbsp;{$MOD.LBL_CANCEL_BUTTON}&nbsp; "  class="crmbutton small cancel" onClick="window.history.back();"/>
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
</form>

<script language="javascript" type="text/javascript">
{literal}
	function showSource()
	{
		if(document.getElementById("migration").checked == true)
			document.getElementById('mnuTab').style.display = 'none';
		else
			document.getElementById('mnuTab').style.display = 'block';
	}
	function getConfirmation(current_version)
	{
		if(document.getElementById("migration").checked == true)
			return true;
		else
		{
			var tagName = document.getElementById('source_version');
			var source_version = tagName.options[tagName.selectedIndex].text;
			{/literal}
                        if(confirm("{$APP.DATABASE_CHANGE_CONFIRMATION}"+source_version+"{$APP.TO}"+current_version+"?"))
                        {literal}
				return true;
			else
				return false;
		}
	}
{/literal}
</script>
