<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
</script>
<script src="modules/com_vtiger_workflow/resources/emailtaskscript.js" type="text/javascript" charset="utf-8"></script>

<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> Recepient</b></td>
		<td class='dvtCellInfo'><input type="text" name="recepient" value="{$task->recepient}" id="save_recepient" class="form_input" style='width: 250px;'>
			<span id="task-emailfields-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="task-emailfields" class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select></td>
	</tr>
	<tr>
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> Subject</b></td>
		<td class='dvtCellInfo'><input type="text" name="subject" value="{$task->subject}" id="save_subject" class="form_input"></td>
	</tr>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="small">
	<tr>
		<td style='padding-top: 10px;'>
			<span id="task-fieldnames-busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id='task-fieldnames' class="small" style="display: none;"><option value=''>{$MOD.LBL_SELECT_OPTION_DOTDOTDOT}</option></select>
		</td>
		<td align="right" style='padding-top: 10px;'>
			<span class="helpmessagebox" style="font-style: italic;">{$MOD.LBL_WORKFLOW_NOTE_CRON_CONFIG}</span>
		</td> 
	</tr>
</table>
<p>
	<textarea name="content" rows="15" cols="40" id="save_content" class='detailedViewTextBox'>{$task->content}</textarea>
</p>


