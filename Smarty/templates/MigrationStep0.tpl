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

{literal}
function checkDataConversion()
{
	var data_conversion_enabled = document.getElementById("convert_utf8_data").checked;
	if(data_conversion_enabled == true) {
		if (document.Migration.config_charset.value == 0) {
			document.Migration.proceed.disabled = true;
			document.Migration.proceed.className = "crmbutton";
			alert("Please change the config file for UTF-8 support and then refresh the page");
			return;
		} else {
			document.Migration.dbconversionutf8.value = "yes";
		}
	} else {
		document.Migration.dbconversionutf8.value = "";
	}
	document.Migration.proceed.disabled = false;
	document.Migration.proceed.className = "crmbutton small edit";
	return;
}
{/literal}
</script>


<form name="Migration" method="POST" action="index.php" enctype="multipart/form-data" onsubmit="VtigerJS_DialogBox.block();">
<input type="hidden" name="module" value="Migration">
<input type="hidden" name="action" value="index">
<input type="hidden" name="migration_option" value="">
<input type="hidden" name="parenttab" value="Settings">
<input type="hidden" name="dbconversionutf8" value="">
<input type="hidden" name="migration_charstcheck" value="true">
<input type="hidden" name="config_charset" value="{$CONFIG_STATUS}">

<table width="100%" border="0" cellpadding="0" cellspacing="0" height="100%" class="small">
	<tr>
		<td class="showPanelBg" valign="top" width="97%"  style="padding-left:20px; "><br />
			<span class="lvtHeaderText"> {$MOD.LBL_SETTINGS} &gt; {$MOD.LBL_UPGRADE_VTIGER}</span>
			<hr noshade="noshade" size="1" />
		</td>
		<td width="3%" >&nbsp;</td>
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
<!--							<tr>
								<td class="hdrNameBg" colspan="2">&nbsp;</td>
					   		</tr>
							<tr>
								<td colspan="2" align="center">
									<table width="40%" cellpadding="0" cellspacing="5" style="border: 1px solid #777;">
										<tr><th>vtigerCRM Charset</th><th>Database Charset</th></tr>
										<tr><th>{$CONFIG_CHARSET}
											{if $CONFIG_STATUS eq '1'} <img src="themes/images/yes.gif" /> 
											{else} <img src="themes/images/no.gif" /> 
											{/if}
											</th>
											<th>{$DB_CHARSET}
												{if $DB_STATUS eq '1'} <img src="themes/images/yes.gif" /> 
												{else} <img src="themes/images/no.gif" /> 
												{/if}
											</th>
										</tr>
									</table>
								</td>				
							</tr>
							<tr>
	           					<td colspan="2" align="center">
						            For UTF-8 Support:&nbsp;
						            <img src="themes/images/no.gif" /> Not recommended setting &nbsp;
						            <img src="themes/images/yes.gif" /> Meets recommended setting
					           </td>
					        </tr>
						
							<tr>
								<td colspan="2">
									<table width="80%" align="center" cellpadding="3" cellspacing="0" class="small" style="margin-top: 5px;margin-bottom: 20px;">
										<tr>
											<td style="text-align:center;color:red;font-weight:bold;">
												{$CONVERSION_MSG.msg1}
											</td>
										</tr>
										<tr>
											<td style="text-align:center;font-weight:bold;">
												<input type="checkbox" id='convert_utf8_data' onchange="checkDataConversion();"
													{if $CONVERSION_MSG.checked eq "true"}
														checked
													{/if}
												/>&nbsp;Convert data to UTF-8
											</td>
										</tr>
										<tr>
											<td style="text-align:center;">
												{$CONVERSION_MSG.msg2}
											</td>
										</tr>
										<tr>
											<td align="center">
												<input type="submit" name='proceed' class="crmbutton small edit" value="Proceed" />
											</td>
										</tr>
									</table>
								</td>
							</tr> -->
				
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
							<tr> <td align ="center" colspan ="2" ><ul>
											<li><font color="red">Changes made to database during migration cannot be reverted back. So we highly recommend to take database dump of the current working database before migration. </font> </li> </ul> </td> </tr>
							<tr>
								<td colspan="2" align="center">
									<table width="40%" cellpadding="0" cellspacing="5" style="border: 1px solid #777;">
										<tr><th>vtigerCRM Charset (config.inc.php)</th><th>Database Charset</th></tr>
										<tr><th>{$CONFIG_CHARSET}
											{if $CONFIG_STATUS eq '1'} <img src="{'yes.gif'|@vtiger_imageurl:$THEME}" /> 
											{else} <img src="{'no.gif'|@vtiger_imageurl:$THEME}" /> 
											{/if}
											</th>
											<th>{$DB_CHARSET}
												{if $DB_STATUS eq '1'} <img src="{'yes.gif'|@vtiger_imageurl:$THEME}" /> 
												{else} <img src="{'no.gif'|@vtiger_imageurl:$THEME}" /> 
												{/if}
											</th>
										</tr>
									</table>
								</td>				
							</tr>
							<tr>
	           					<td colspan="2" align="center">
						            <img src="{'no.gif'|@vtiger_imageurl:$THEME}" /> Not recommended setting for UTF-8 Support. &nbsp;
						            <img src="{'yes.gif'|@vtiger_imageurl:$THEME}" /> Meets recommended setting for UTF-8 Support.
					           </td>
					        </tr>
						
							<tr>
								<td colspan="2">
									<table width="80%" align="center" cellpadding="3" cellspacing="0" class="small" style="margin-top: 5px;margin-bottom: 20px;">
										<tr>
											<td style="text-align:center;color:red;font-weight:bold;">
												{$CONVERSION_MSG.msg1}
											</td>
										</tr>
										<tr>
											<td style="text-align:center;font-weight:bold;">
												<input type="checkbox" id='convert_utf8_data' onchange="checkDataConversion();"
													{if $CONVERSION_MSG.checked eq "true"}
														checked
													{/if}
												/>&nbsp;Convert data to UTF-8
											</td>
										</tr>
										<tr>
											<td style="text-align:center;">
												{$CONVERSION_MSG.msg2}
											</td>
										</tr>
										<tr>
											<td align="center">
												<input type="submit" name='proceed' class="crmbutton small edit" value="Proceed" />
											</td>
										</tr>
									</table>
								</td>
							</tr>

						</table>
					</td>
				</tr>
			</td>
		</tr>
	</table>
</table>

</form>

{literal}
<script type='text/javascript'>checkDataConversion();</script>
{/literal}
