<?php
	$statuscolor = array(
		100 => '#6CB56C',
		50  => '#D8D83A',
		0   => '#F44'
	);
	$status = array(
		'dps'    => array(0=>0,50=>0,100=>0),
		'tank'   => array(0=>0,50=>0,100=>0),
		'healer' => array(0=>0,50=>0,100=>0)
	);
	foreach($raid->raiders as $raider)
		$status[$raider->role][$raider->confidence]++;

	if(isset($raid->raiders[RaidController::$CurrentUser->userid]))
	{
		$signup = $raid->raiders[RaidController::$CurrentUser->userid];
		$conf = $raid->raiders[RaidController::$CurrentUser->userid]->confidence;
	}
	else
	{
		$signup = false;
		$conf = false;
	}
?>
<table style="margin-bottom:5px;">
	<colgroup>
		<col style="width:50px;" />
		<col style="width:70px;" />
		<col style="width:60px;" />
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
		<td colspan="2" style="color:black;padding:2px;background:<?php echo $conf === false ? 'gray' : $statuscolor[(int)$conf]; ?>;"><?php echo FormatDate($raid->raidstart, 'Raid of the %e. %H:%M'); ?></td>
	</tr>
	<tr>
		<td style="height:32px;" valign="top">
<?php
	$wws = $raid->WowWebStats();
	if($wws)
	{
?>
			<a href="<?php echo $wws; ?>"><img style="height:30px;border: none;" src="raid_includes/images/stats.png" alt="raid stats" title="Raid statistics" /></a>
<?php
	}
?>
		</td>
		<td style="height:32px;text-align:right; vertical-align:top;">
<?php	
	if(!$raid->attendance)
	{
?>
			<img src="raid_includes/images/none.png" title="Attendance was not recorded for this raid" />
<?php
	}
	else if($signup)
	{
?>
			<img src="raid_includes/images/<?php echo $signup->attended ? 'check.png' : 'cross.png'; ?>" title="<?php echo $signup->attended ? 'You attended this raid' : 'You did not attend this raid'; ?>" />
<?php
		if($signup->satout)
		{
?>
			<img src="raid_includes/images/sitout.png" title="You sat out of this raid" />
<?php
		}
	}
?>
		</td>
	</tr>
</table>