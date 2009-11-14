<script src="modules/com_vtiger_workflow/resources/vtigerwebservices.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript" charset="utf-8">
var moduleName = '{$entityName}';
var eventStatus = '{$task->status}';
var eventType = '{$task->eventType}';
</script>
<script src="modules/com_vtiger_workflow/resources/createeventtaskscript.js" type="text/javascript" charset="utf-8"></script>


<div id="view">
	<table border="0" cellpadding="5" cellspacing="0" width="100%" class="small">
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b><font color=red>*</font> Event Name</b></td>
		<td class='dvtCellInfo'><input type="text" name="eventName" value="{$task->eventName}" id="workflow_eventname" class="form_input"></td>
	</tr>
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>Description</b></td>
		<td class='dvtCellInfo'><textarea name="description" rows="8" cols="40" class='detailedViewTextBox'>{$task->description}</textarea></td>
	</tr>
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>Status</b></td>
		<td class='dvtCellInfo'>
			<span id="event_status_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="event_status" value="{$task->status}" name="status" class="small" style="display: none;"></select>
		</td>
	</tr> 
	<tr valign="top">
		<td class='dvtCellLabel' align="right" width=15% nowrap="nowrap"><b>Type</b></td>
		<td class='dvtCellInfo'>
			<span id="event_type_busyicon"><b>{$MOD.LBL_LOADING}</b><img src="{'vtbusy.gif'|@vtiger_imageurl:$THEME}" border="0"></span>
			<select id="event_type" value="{$task->eventType}" name="eventType" class="small" style="display: none;"></select>
		</td>
	</tr>
	<tr><td colspan="2"><hr size="1" noshade="noshade" /></td></tr>
	<tr>
		<td align="right"><b>Start Time</b></td>
		<td><input type="hidden" name="startTime" value="{$task->startTime}" id="workflow_time" style="width:60px"  class="time_field"></td>
	</tr>
	<tr>
		<td align="right"><b>Start Date</b></td>
		<td>
			<input type="text" name="startDays" value="{$task->startDays}" id="start_days" style="width:30px" class="small"> days 
			<select name="startDirection" value="{$task->startDirection}" class="small">
				<option>After</option>
				<option>Before</option>
			</select>
			<select name="startDatefield" value="{$task->startDatefield}" class="small">
				{foreach key=name item=label from=$dateFields}
				<option value='{$name}' {if $task->startDatefield eq $name}selected{/if}>
					{$label}
				</option>
				{/foreach}
			</select>
		</td>
	</tr>
	<tr>
		<td align="right"><b>End Time</b></td>
		<td><input type="hidden" name="endTime" value="{$task->endTime}" id="end_time" style="width:60px" class="time_field"></td>
	</tr>
	<tr>
		<td align="right"><b>End Date</b></td>
		<td><input type="text" name="endDays" value="{$task->endDays}" id="end_days" style="width:30px" class="small"> days 
			<select name="endDirection" value="{$task->endDirection}" class="small">
				<option>After</option>
				<option>Before</option>
			</select>
			<select name="endDatefield" value="{$task->endDatefield}" class="small">
				{foreach key=name item=label from=$dateFields}
				<option value='{$name}' {if $task->endDatefield eq $name}selected{/if}>
					{$label}
				</option>
				{/foreach}
			</select>
		</td>
	</tr>
	</table>
</div>
