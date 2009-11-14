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
<link rel="stylesheet" type="text/css" media="all" href="jscalendar/calendar-win2k-cold-1.css">
<script type="text/javascript" src="jscalendar/calendar.js"></script>
<script type="text/javascript" src="jscalendar/lang/calendar-{$APP.LBL_JSCALENDAR_LANG}.js"></script>
<script type="text/javascript" src="jscalendar/calendar-setup.js"></script>
<script language="JavaScript" type="text/javascript" src="include/calculator/calc.js"></script>
{$BLOCKJS_STD}
<table class="small" bgcolor="#ffffff" border="0" cellpadding="5" cellspacing="0" height="530" width="100%">
	<tbody><tr>
	<td colspan="2">
	<span class="genHeaderGray">{$MOD.LBL_FILTERS}</span><br>
	{$MOD.LBL_SELECT_FILTERS_TO_STREAMLINE_REPORT_DATA}
	<hr>
	</td>
	</tr>
	<tr><td colspan="2">
		<div id='adv_filter_div' name='adv_filter_div'>
		<table class="small" border="0" cellpadding="5" cellspacing="0" width="100%">
		<tbody>
		<tr>
		<td class="detailedViewHeader" colspan="2" width="60%">
		<b>{$MOD.LBL_STANDARD_FILTER}</b>
		</td>
		<td class="detailedViewHeader" width="20%">&nbsp;</td>
		<td class="detailedViewHeader" width="20%">&nbsp;</td>
		</tr>
		<tr>
		<td class="dvtCellLabel">{$MOD.LBL_SF_COLUMNS}:</td>
		<td class="dvtCellLabel">&nbsp;</td>
		<td class="dvtCellLabel">{$MOD.LBL_SF_STARTDATE}:</td>
		<td class="dvtCellLabel">{$MOD.LBL_SF_ENDDATE}:</td>
		</tr>
		<tr>
		<td class="dvtCellInfo" width="60%">
		<select name="stdDateFilterField" class="detailedViewTextBox" onchange='standardFilterDisplay();'>
		{$BLOCK1_STD}
		</select>
		</td>
		<td class="dvtCellInfo" width="25%">
		<select name="stdDateFilter" id="stdDateFilter" onchange='showDateRange( this.options[ this.selectedIndex ].value )' class="repBox">
		{$BLOCKCRITERIA_STD}
		</select>
		</td>
		<td class="dvtCellInfo">
		<input name="startdate" id="jscal_field_date_start" style="border: 1px solid rgb(186, 186, 186);" size="10" maxlength="10" value="{$STARTDATE_STD}" type="text" ><br>
		<img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_start" >
		<font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
		<script type="text/javascript">
                                        Calendar.setup ({ldelim}
                                        inputField : "jscal_field_date_start", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_start", singleClick : true, step : 1
                                        {rdelim})
                </script>
		</td>
		<td class="dvtCellInfo">
		<input name="enddate" id="jscal_field_date_end" style="border: 1px solid rgb(186, 186, 186);" size="10" maxlength="10" value="{$ENDDATE_STD}" type="text"><br>
                <img src="{$IMAGE_PATH}btnL3Calendar.gif" id="jscal_trigger_date_end" >
		<font size="1"><em old="(yyyy-mm-dd)">({$DATEFORMAT})</em></font>
                <script type="text/javascript">
                                        Calendar.setup ({ldelim}
                                        inputField : "jscal_field_date_end", ifFormat : "{$JS_DATEFORMAT}", showsTime : false, button : "jscal_trigger_date_end", singleClick : true, step : 1
                                        {rdelim})
                </script>
		</td>
		</tr>
		<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		</tr>
			<tr><td colspan="2" class="detailedViewHeader"><b>{$MOD.LBL_ADVANCED_FILTER}</b>
			</td>
			<td class="detailedViewHeader">&nbsp;</td>
			<td class="detailedViewHeader">&nbsp;</td>
			</tr>
			<tr>
			<td colspan="4">
			<ul>
			<li>{$MOD.LBL_AF_HDR2}</li> 
			<li>{$MOD.LBL_AF_HDR3}</li>
			</ul>  
			</td>	
			</tr>
			<tr>
			<td class="dvtCellLabel">
			<select name="fcol1" id="fcol1" onchange="updatefOptions(this, 'fop1');updateRelFieldOptions(this, 'fval_1');" class="detailedViewTextBox">
			<option value="">{$MOD.LBL_NONE}</option>
	        {$BLOCK1}
			</select>
			</td>
			<td class="dvtCellLabel">
			<select name="fop1" id="fop1" class="repBox" style="width:100px;">
			<option value="">{$MOD.LBL_NONE}</option>
			{$FOPTION1}
			</select>
			</td>
			<td class="dvtCellLabel"><input name="fval1" id="fval1" class="repBox" type="text" value="{$VALUE1}">
			<img height=20 width=20 style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src='themes/images/terms.gif' onClick="placeAtCenterOfDiv(adv_filter_div,show_val1);showHideSelectDiv('show_val1');"/>
			<input type="image" align="absmiddle" style="cursor: pointer;" onclick="$('fval1').value='';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>
			</td>
			<td class="dvtCellLabel">{$MOD.LBL_AND}</td>
			</tr>
			<tr>
			<td class="dvtCellInfo">
			<select name="fcol2" id="fcol2" onchange="updatefOptions(this, 'fop2');updateRelFieldOptions(this, 'fval_2');" class="detailedViewTextBox">
			<option value="">{$MOD.LBL_NONE}</option>
	        {$BLOCK2}
			</select>
			</td>
			<td class="dvtCellInfo">
			<select name="fop2" id="fop2" class="repBox" style="width:100px;">
			<option value="">{$MOD.LBL_NONE}</option>
	        {$FOPTION2}
			</select>
			</td>
			<td class="dvtCellInfo"><input name="fval2" id="fval2" class="repBox" type="text" value="{$VALUE2}">
			<img height=20 width=20 style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src='themes/images/terms.gif' onClick="placeAtCenterOfDiv(adv_filter_div,show_val2);showHideSelectDiv('show_val2');"/>
			<input type="image" align="absmiddle" style="cursor: pointer;" onclick="$('fval2').value='';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>
			</td>
			<td class="dvtCellInfo">{$MOD.LBL_AND}</td>
			</tr>
			<tr>
			<td class="dvtCellLabel">
			<select name="fcol3" id="fcol3" onchange="updatefOptions(this, 'fop3');updateRelFieldOptions(this, 'fval_3');" class="detailedViewTextBox">
			<option value="">{$MOD.LBL_NONE}</option>
			{$BLOCK3}
			</select>
			</td>
			<td class="dvtCellLabel">
			<select name="fop3" id="fop3" class="repBox" style="width:100px;">
			<option value="">{$MOD.LBL_NONE}</option>
			{$FOPTION3}
			</select>
			</td>
			<td class="dvtCellLabel"><input name="fval3" id="fval3" class="repBox" type="text" value="{$VALUE3}">
			<img height=20 width=20 style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src='themes/images/terms.gif' onClick="placeAtCenterOfDiv(adv_filter_div,show_val3);showHideSelectDiv('show_val3');"/>
			<input type="image" align="absmiddle" style="cursor: pointer;" onclick="$('fval3').value='';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>
			</td>
			<td class="dvtCellLabel">{$MOD.LBL_AND}</td>
			</tr>
			<tr>
			<td class="dvtCellInfo">
			<select name="fcol4" id="fcol4" onchange="updatefOptions(this, 'fop4');updateRelFieldOptions(this, 'fval_4');" class="detailedViewTextBox">
			<option value="">{$MOD.LBL_NONE}</option>
			{$BLOCK4}
			</select>
			</td>
			<td class="dvtCellInfo">
			<select name="fop4" id="fop4" class="repBox" style="width:100px;">
			<option value="">{$MOD.LBL_NONE}</option>
			{$FOPTION4}
			</select>
			</td>
			<td class="dvtCellInfo"><input name="fval4" id="fval4" class="repBox" type="text" value="{$VALUE4}">
			<img height=20 width=20 style="cursor: pointer;" title="{$APP.LBL_FIELD_FOR_COMPARISION}" alt="{$APP.LBL_FIELD_FOR_COMPARISION}" src='themes/images/terms.gif' onClick="placeAtCenterOfDiv(adv_filter_div,show_val4);showHideSelectDiv('show_val4');"/>
			<input type="image" align="absmiddle" style="cursor: pointer;" onclick="$('fval4').value='';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/>
			</td>
			<td class="dvtCellInfo">{$MOD.LBL_AND}</td>
			</tr>
			<tr>
			<td class="dvtCellLabel">
			<select name="fcol5" id="fcol5" onchange="updatefOptions(this, 'fop5');updateRelFieldOptions(this, 'fval_5');" class="detailedViewTextBox">
			<option value="">{$MOD.LBL_NONE}</option>
			{$BLOCK5}		
			</select>
			</td>
			<td class="dvtCellLabel">
			<select name="fop5" id="fop5" class="repBox" style="width:100px;">
			<option value="">{$MOD.LBL_NONE}</option>
			{$FOPTION5}
			</select>
			</td>
			<td class="dvtCellLabel"><input name="fval5" id="fval5" class="repBox" type="text" value="{$VALUE5}">
				<img height=20 width=20 style="cursor: pointer;" title="Fields for Comparision" alt="Fields for Comparision" src='themes/images//terms.gif' onClick="placeAtCenterOfDiv(adv_filter_div,show_val5);showHideSelectDiv('show_val5');"/>
				<input type="image" align="absmiddle" style="cursor: pointer;" onclick="$('fval5').value='';return false;" language="javascript" title="{$APP.LBL_CLEAR}" alt="{$APP.LBL_CLEAR}" src="themes/images/clear_field.gif"/</td>
			<td class="dvtCellLabel">&nbsp;</td>
			</tr>
		</tbody>
	</table>
	</div>
	</td></tr>
	</tbody>
</table>
					<div class="layerPopup" id='show_val1'style="border:0; position: absolute; width:300px; z-index: 50; display: none;">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
						<tbody><tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">
							<tbody><tr background="themes/images/qcBg.gif" class="mailSubHeader">
								<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>
								<td align=right> <img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="showHideSelectDiv('show_val1');"/></td
							</tbody></table>
						
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
							<tbody><tr>
								<td>
								<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">
									<tbody><tr>
									<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>
									<td width="30%" align="left" class="cellText">
										<select name="fval_1" id="fval_1" onChange='AddFieldToFilter(1,this);' class="detailedViewTextBox">
										<option value="">{$MOD.LBL_NONE}</option>
						        		{$REL_FIELDS1}
						        		</select>
									</td>
								</tr>
								</tbody></table>	
								<!-- save cancel buttons -->
								<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
								<tbody><tr>
									<td width="50%" align="center">
										<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="showHideSelectDiv('show_val1');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>
									</td>
								</tr>
								</tbody></table>
						
								</td>
							</tr>
							</tbody></table>
						</td>
						</tr>
						</tbody></table>
					</div>
					<div class="layerPopup" id='show_val2'style="border:0; position: absolute; width:300px; z-index: 50; display: none;">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
						<tbody><tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">
							<tbody><tr background="themes/images/qcBg.gif" class="mailSubHeader">
								<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>
								<td align=right> <img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="showHideSelectDiv('show_val2');"/></td
							</tbody></table>
						
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
							<tbody><tr>
								<td>
								<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">
									<tbody><tr>
									<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>
									<td width="30%" align="left" class="cellText">
										<select name="fval_2" id="fval_2" onChange='AddFieldToFilter(2,this);' class="detailedViewTextBox">
										<option value="">{$MOD.LBL_NONE}</option>
						        		{$REL_FIELDS2}
						        		</select>
									</td>
								</tr>
								</tbody></table>	
								<!-- save cancel buttons -->
								<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
								<tbody><tr>
									<td width="50%" align="center">
										<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="showHideSelectDiv('show_val2');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>
									</td>
								</tr>
								</tbody></table>
						
								</td>
							</tr>
							</tbody></table>
						</td>
						</tr>
						</tbody></table>
					</div>
					<div class="layerPopup" id='show_val3'style="border:0; position: absolute; width:300px; z-index: 50; display: none;">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
						<tbody><tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">
							<tbody><tr background="themes/images/qcBg.gif" class="mailSubHeader">
								<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>
								<td align=right> <img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="showHideSelectDiv('show_val3');"/></td
							</tbody></table>
						
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
							<tbody><tr>
								<td>
								<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">
									<tbody><tr>
									<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>
									<td width="30%" align="left" class="cellText">
										<select name="fval_3" id="fval_3" onChange='AddFieldToFilter(3,this);' class="detailedViewTextBox">
										<option value="">{$MOD.LBL_NONE}</option>
						        		{$REL_FIELDS3}
						        		</select>
									</td>
								</tr>
								</tbody></table>	
								<!-- save cancel buttons -->
								<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
								<tbody><tr>
									<td width="50%" align="center">
										<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="showHideSelectDiv('show_val3');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>
									</td>
								</tr>
								</tbody></table>
						
								</td>
							</tr>
							</tbody></table>
						</td>
						</tr>
						</tbody></table>
					</div>
					<div class="layerPopup" id='show_val4'style="border:0; position: absolute; width:300px; z-index: 50; display: none;">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
						<tbody><tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">
							<tbody><tr background="themes/images/qcBg.gif" class="mailSubHeader">
								<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>
								<td align=right> <img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="showHideSelectDiv('show_val4');"/></td
							</tbody></table>
						
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
							<tbody><tr>
								<td>
								<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">
									<tbody><tr>
									<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>
									<td width="30%" align="left" class="cellText">
										<select name="fval_4" id="fval_4" onChange='AddFieldToFilter(4,this);' class="detailedViewTextBox">
										<option value="">{$MOD.LBL_NONE}</option>
						        		{$REL_FIELDS4}
						        		</select>
									</td>
								</tr>
								</tbody></table>	
								<!-- save cancel buttons -->
								<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
								<tbody><tr>
									<td width="50%" align="center">
										<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="showHideSelectDiv('show_val4');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>
									</td>
								</tr>
								</tbody></table>
						
								</td>
							</tr>
							</tbody></table>
						</td>
						</tr>
						</tbody></table>
					</div>
					<div class="layerPopup" id='show_val5'style="border:0; position: absolute; width:300px;z-index: 50; display: none;">
						<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="mailClient mailClientBg">
						<tbody><tr>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="layerHeadingULine">
							<tbody><tr background="themes/images/qcBg.gif" class="mailSubHeader">
								<td width=90% class="genHeaderSmall"><b>{$MOD.LBL_SELECT_FIELDS}</b></td>
								<td align=right> <img border="0" align="absmiddle" src="themes/images/close.gif" style="cursor: pointer;" alt="{$APP.LBL_CLOSE}" title="{$APP.LBL_CLOSE}" onclick="showHideSelectDiv('show_val5');"/></td
							</tbody></table>
						
							<table width="100%" cellspacing="0" cellpadding="0" border="0" class="small">
							<tbody><tr>
								<td>
								<table width="100%" cellspacing="0" cellpadding="5" border="0" bgcolor="white" class="small">
									<tbody><tr>
									<td width="30%" align="left" class="cellLabel small">{$MOD.LBL_RELATED_FIELDS}</td>
									<td width="30%" align="left" class="cellText">
										<select name="fval_5" id="fval_5" onChange='AddFieldToFilter(5,this);' class="detailedViewTextBox">
										<option value="">{$MOD.LBL_NONE}</option>
						        		{$REL_FIELDS5}
						        		</select>
									</td>
								</tr>
								</tbody></table>	
								<!-- save cancel buttons -->
								<table width="100%" cellspacing="0" cellpadding="5" border="0" class="layerPopupTransport">
								<tbody><tr>
									<td width="50%" align="center">
										<input type="button" style="width: 70px;" value="{$APP.LBL_DONE}" name="button" onclick="showHideSelectDiv('show_val5');" class="crmbutton small create" accesskey="X" title="{$APP.LBL_DONE}"/>
									</td>
								</tr>
								</tbody></table>
						
								</td>
							</tr>
							</tbody></table>
						</td>
						</tr>
						</tbody></table>
					</div>
<script>
 var rel_fields = {$REL_FIELDS};
var constructedOptionValue;
var constructedOptionName;
</script>
{literal}
<script>

function showHideSelectDiv(id){
	for(var i=1;i<=5;i++){
		if(id=='show_val'+i){
			if(document.getElementById(id).style.display=='block')
				document.getElementById(id).style.display='none';
			else
				document.getElementById(id).style.display='block';
		}
		else
			document.getElementById('show_val'+i).style.display='none';
	}
	return true;
}
</script>

<script>    
    var filter = document.NewReport.stdDateFilter.options[document.NewReport.stdDateFilter.selectedIndex].value
    if( filter != "custom" )
    {
        showDateRange( filter );
    }
</script>
<script>
for(var i=1;i<=5;i++)
{
	var obj=document.getElementById("fcol"+i);
	if(obj.selectedIndex != 0)
		updatefOptions(obj, 'fop'+i);
}
</script>

<script>
// If current user has no access to date fields, we should disable selection
standardFilterDisplay();
</script>

<script language="JavaScript" type="text/JavaScript">    
function placeAtCenterOfDiv(node1, node2){
	var centerPixel = getDimension(node1);
	node2.style.position = "absolute";
	
	var point = getDimension(node2);
	var x = findPosX(node1);
	var y = findPosY(node1);
	var ua=navigator.userAgent.toLowerCase();

	if(ua.indexOf('msie')!=-1){
		node2.style.top = centerPixel.y - (centerPixel.y/2+point.y)-30 +"px";
		node2.style.left = centerPixel.x - (centerPixel.x/2+point.x/2) + "px";
	
	} else {
		node2.style.top = y+centerPixel.y - (centerPixel.y/2+point.y)-30 +"px";
		node2.style.left = x+centerPixel.x - (centerPixel.x/2+point.x/2) + "px";
	}
}

function getDimension(node){
	var ht = node.offsetHeight;
	var wdth = node.offsetWidth;
	var nodeChildren = node.getElementsByTagName("*");
	var noOfChildren = nodeChildren.length;
	for(var index =0;index<noOfChildren;++index){
		ht = Math.max(nodeChildren[index].offsetHeight, ht);
		wdth = Math.max(nodeChildren[index].offsetWidth,wdth);
	}
	return {x: wdth,y: ht};
}

</script>
{/literal}
