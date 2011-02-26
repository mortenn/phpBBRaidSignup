<?php
		require($templatedir.'raidcalendar.php');

		$icons = glob('raid_includes/icons/*');
?>
<form method="post" action="<?php echo $_SESSION['REQUEST_URI']; ?>">
	<input type="hidden" name="action" value="create" />
	<table class="tablebg" cellspacing="0">
		<tr>
			<th class="cat" colspan="3">Create a new raid event</th>
		</tr>
		<tr>
			<td></td>
			<td colspan="2"><input id="cb" type="checkbox" name="auto" /> Auto-create all raids for <?php echo date('F',strtotime($raid->raidstart)); ?></td>
		</tr>
		<tr>
			<td rowspan="5" style="vertical-align: top;"><img id="raidicon" style="margin:2px;" src="<?php echo $icons[0]; ?>" /></td>
			<td>Raidstart</td>
			<td><input type="text" value="<?php echo FormatDate($raid->raidstart, '%F %R'); ?>" name="raidstart" size="25" /></td>
		</tr>
		<tr>
			<td>Signup deadline</td>
			<td><input type="text" value="<?php echo FormatDate($raid->deadline, '%F %R'); ?>" name="deadline" size="25" /></td>
		</tr>
		<tr>
			<td>Raid icon</td>
			<td><select name="thumbnail" onchange="document.getElementById('raidicon').src = 'raid_includes/icons/'  + this.value;"><?php

	
	foreach($icons as $icon)
		printf('<option value="%1$s">%1$s</option>', basename($icon));
			
?></select></td>
		</tr>
		<tr>
			<td>Attendance</td>
			<td><input type="checkbox" name="attendance" checked="checked" /> Include in statistics</td>
		</tr>
		<tr>
			<td colspan="2">Raid comment (500 letters)</td>
		</tr>
		<tr>
			<td colspan="2"><textarea name="comment" style="width: 98%;height: 6em;"></textarea></td>
		</tr>
		<tr><td colspan="2" style="text-align:right;"><input type="submit" class="btnlite" /></td></tr>
	</table>
</form>