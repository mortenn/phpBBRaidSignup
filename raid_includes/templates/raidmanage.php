<?php
	require($templatedir.'raidcalendar.php');
	if(!$raid)
		return;

	require($templatedir.'raidquickmanage.php');

	$icons = glob('raid_includes/icons/*');
?>
<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="action" value="manage" />
	<input type="hidden" name="raidid" value="<?php echo $raid->id; ?>" />
	<table style="float: left;margin: 0 1em 1em 0;" class="tablebg" cellspacing="0">
		<colgroup>
			<col style="width: 10em;" />
			<col />
			<col />	
		</colgroup>
		<tr>
			<th colspan="4" class="cat">Raid details</th>
		</tr>
		<tr>
			<td rowspan="6" style="vertical-align: top;"><img id="raidicon" style="margin:2px;" src="raid_includes/icons/<?php echo $raid->thumbnail; ?>" /></td>
			<td>Raidstart</td>
			<td><input type="text" value="<?php echo FormatDate($raid->raidstart, '%F %R'); ?>" name="raidstart" size="15" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Signup deadline</td>
			<td><input type="text" value="<?php echo FormatDate($raid->deadline, '%F %R'); ?>" name="deadline" size="15" /></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>Raid icon</td>
			<td><select name="thumbnail" onchange="document.getElementById('raidicon').src = 'raid_includes/icons/'  + this.value;"><?php

	
	foreach($icons as $file)
		printf('<option value="%1$s"%2$s>%1$s</option>', basename($file), basename($file) == $raid->thumbnail ? ' selected="selected"' : '');
			
?></select></td>
		</tr>
		<tr>
			<td>WWS Link</td>
			<td><input type="text" value="<?php echo trim($raid->wws_url); ?>" name="wws_url" size="40" /></td>
		</tr>
		<tr>
			<td>Attendance</td>
			<td><input type="checkbox" name="attendance"<?php if($raid->attendance) echo ' checked="checked"'; ?> /> Include in statistics</td>
		</tr>
		<tr>
			<td colspan="3">Raid comment (500 letters)</td>
		</tr>
		<tr>
			<td colspan="3"><textarea name="comment" rows="4" cols="50"><?php echo $raid->comment; ?></textarea></td>
		</tr>
	</table>
	<div style="float:left;"> 
		<input type="submit" class="btnlite" value="Update raid" />
		<input class="btnlite" type="button" value="Delete raid" onclick="this.style.display='none';document.getElementById('delconfirm').style.display='';" />
		<span style="display: none;" id="delconfirm">
			<input type="checkbox" name="delete_raid" /> I really want to delete this raid and all associated data.
		</span>
	</div>

	<table style="clear:both;" class="tablebg" cellspacing="0">
		<colgroup>
			<col style="width: 7em;" />
			<col style="width: 4em;" />
			<col style="width: 3em;" />
			<col style="width: 1em;" />
			<col style="width: 1em;" />
			<col style="width: 1em;" />
			<col style="width: 1em;" />
			<col style="width: 1em;" />
			<col style="width: 1em;" />
			<col style="width: 20em;" />
			<col style="width: 20em;" />
			<col style="width: 12em;" />
			<col style="width: 12em;" />
		</colgroup>
		<tr>
			<th colspan="11" class="cat">Raid signups</th>
			<th colspan="2" class="cat" style="text-align:right;">
				<select name="add_player" onchange="this.form.submit();">
					<option value="">Sign up a raider..</option>
<?php
	global $WoWclasses;
	$players = Player::GetAll();

	if(count($players))
		foreach($players as $player)
		{
			if(isset($raid->raiders[$player->userid]))
				continue;
?>
					<option value="<?php echo $player->userid; ?>" style="background:black;color: <?php echo $WoWclasses[$player->class]['color']; ?>;"><?php echo $player->character; ?></option>
<?php
		}
?>
				</select>
			</th>
		</tr>
		<tr>
			<th>Toon</th>
			<th>Class</th>
			<th>Role</th>
			<th title="Remove the listing">X</th>
			<th title="Sign up as in">+</th>
			<th title="Sign up as 50/50">?</th>
			<th title="Sign up as out">-</th>
			<th title="Attended">A</th>
			<th title="Sat out">S</th>
			<th>Comment</th>
			<th>Officer note</th>
			<th>Added</th>
			<th>Modified</th>
		</tr>
<?php
	$cc = array(0=>0,50=>0,100=>0);
	$ac = 0;
	$sc = 0;
	$i = 1;
	foreach($raid->raiders as $raider)
	{
		if(!$raider->Complete())
			continue;

		if($raider->attended)
			$ac++;
		if($raider->satout)
			$sc++;
		$cc[$raider->confidence]++;
?>
		<tr class="row<?php echo $i++%2; ?> raidhack">
			<td><?php echo $raider->ColorizeName(); ?></td>
			<td><?php echo $raider->class; ?></td>
			<td><?php echo $raider->role; ?></td>
			<td><input type="radio" name="confidence[<?php echo $raider->userid; ?>]" value="-1" /></td>
			<td><input type="radio" name="confidence[<?php echo $raider->userid; ?>]" value="100" <?php if($raider->confidence == 100) echo 'checked="checked"'; ?> /></td>
			<td><input type="radio" name="confidence[<?php echo $raider->userid; ?>]" value="50" <?php if($raider->confidence == 50) echo 'checked="checked"'; ?>/></td>
			<td><input type="radio" name="confidence[<?php echo $raider->userid; ?>]" value="0" <?php if($raider->confidence === 0) echo 'checked="checked"'; ?> /></td>
			<td><input type="checkbox" name="attended[<?php echo $raider->userid; ?>]" <?php if($raider->attended) echo 'checked="checked"'; ?> /></td>
			<td><input type="checkbox" name="sitout[<?php echo $raider->userid; ?>]" <?php if($raider->satout) echo 'checked="checked"'; ?> /></td>
			<td><input type="text" name="usercomment[<?php echo $raider->userid; ?>]" value="<?php echo stripslashes($raider->comment); ?>" style="background: transparent; border: none; width: 20em;" onkeyup="this.style.background = (this.value==this.defaultValue?'transparent':'maroon');" /></td>
			<td><input type="text" name="admincomment[<?php echo $raider->userid; ?>]" value="<?php echo stripslashes($raider->admincomment); ?>" style="background: transparent; border: none; width: 20em;" onkeyup="this.style.background = (this.value==this.defaultValue?'transparent':'maroon');" /></td>
			<td<?php if($raider->added_by) echo ' title="Added by '.$raider->added_by->character.'"'; ?>><?php if($raider->added) echo FormatDate($raider->added, '%d.%m.%Y %H:%M'); ?></td>
			<td<?php if($raider->modified_by) echo ' title="Changed by '.$raider->modified_by->character.'"'; ?>><?php if($raider->modified) echo FormatDate($raider->modified, '%d.%m.%Y %H:%M'); ?></td>
		</tr>
<?php
	}
?>
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td style="text-align:right"><?php echo $cc[100]; ?></td>
			<td style="text-align:right"><?php echo $cc[50]; ?></td>
			<td style="text-align:right"><?php echo $cc[0]; ?></td>
			<td style="text-align:right"><?php echo $ac; ?></td>
			<td style="text-align:right"><?php echo $sc; ?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
	</table>
</form>
<br />