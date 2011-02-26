<?php
	$statuscolor = array(
		100 => '#6CB56C',
		50  => '#D8D83A',
		0   => '#F44'
	);
?>
<table style="margin-bottom:5px;">
	<colgroup>
		<col style="width:50px;" />
		<col style="width:130px;" />
	</colgroup>
	<tr>
		<td rowspan="2">
<?php
	if(RaidController::$Access > 2)
	{
?>
			<a href="raid.php?action=manage&raid=<?php echo $raid->id; ?>">
				<img src="raid_includes/icons/<?php echo $raid->thumbnail; ?>" width="50" />
			</a>
<?php
	}
	else
	{
?>
			<img width="50" src="raid_includes/icons/<?php echo $raid->thumbnail; ?>" />
<?php
	}
?>
		</td>
		<td id="mini_<?php echo $raid->id; ?>" style="color:black;padding:2px;background:gray"><?php echo FormatDate($raid->raidstart, 'Raid of the %e. %H:%M'); ?></td>
	</tr>
	<tr>
		<td style="height:32px;text-align:right; vertical-align:top;">
			<input title="Sign up as in" type="radio" name="signup[<?php echo $raid->id; ?>]" value="100" onclick="document.getElementById('mini_<?php echo $raid->id; ?>').style.background = '<?php echo $statuscolor[100]; ?>';" /> in
			<input title="Sign up as 50/50" type="radio" name="signup[<?php echo $raid->id; ?>]" value="50" onclick="document.getElementById('mini_<?php echo $raid->id; ?>').style.background = '<?php echo $statuscolor[50]; ?>';" /> 50/50
			<input title="Sign up as out" type="radio" name="signup[<?php echo $raid->id; ?>]" value="0" onclick="document.getElementById('mini_<?php echo $raid->id; ?>').style.background = '<?php echo $statuscolor[0]; ?>';" /> out

<?php	
	if(!$raid->attendance)
	{
?>
			<img src="raid_includes/images/none.png" title="Attendance will not be recorded for this raid" />
<?php
	}
?>
		</td>
	</tr>
</table>