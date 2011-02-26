<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="__import" value="1" />
	<input type="hidden" name="raidid" value="<?php echo $raid->id; ?>" />
	Import raid roster:
	<input type="text" name="raidgroup" size="40" /><br />
	<input type="submit" name="__check_signup" class="btnlite" value="Check" />
	<input type="submit" name="__record_attendance" class="btnlite" value="Save" />
</form>
<input type="button" class="btnlite" onclick="$('#__importmacro').show();$(this).hide();" value="Show macro" />
<span style="display:none;" id="__importmacro">/run StaticPopupDialogs["D"]={timeout=2,text="Raid list",button1="Ok",hasEditBox=true}l={}for i=1,GetNumRaidMembers()do n,r,s=GetRaidRosterInfo(i)l[i]=s..":"..n end z=StaticPopup_Show("D")z.editBox:SetText(table.concat(l,","))z.editBox:HighlightText()<br />[Alternate] This version MUST be in a macro to work: /run l={}for i=1,GetNumRaidMembers()do n,r,s=GetRaidRosterInfo(i)l[i]=s..":"..n end z=ChatFrameEditBox;z:Show()z:SetText(table.concat(l,","))z:HighlightText()</span>
<?php
	if($this->raidcheck['notsigned'] || $this->raidcheck['signedout'] || $this->raidcheck['noshowup'])
	{
		$what = array(
			'notsigned' => 'Players that didn\'t sign up',
			'signedout' => 'Players that signed as out',
			'noshowup'  => 'Players that didn\'t show up'
		);
?>
<div>
<?php
		foreach($this->raidcheck as $k => $chars)
		{
?>
	<table style="float:left;">
		<thead>
			<tr><th><?php echo $what[$k]; ?></th></tr>
		</thead>
		<tbody>
<?php
			foreach($chars as $char)
			{
?>
			<tr><td><?php echo $char; ?></td></tr>
<?php
			}
?>
		</tbody>
	</table>
<?php
		}
?>
	<div style="clear:left;"></div>
</div><?php
	}
?>
