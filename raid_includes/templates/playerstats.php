<?php
	require($templatedir.'raidcalendar.php');
?>
<table style="font-size:1.4em;" class="post">
	<colgroup>
		<col />
		<col style="width: 4em;" />
		<col style="width: 10em;" />
	</colgroup>
	<tr>
		<td rowspan="4"><a href="<?php printf(RaidController::$config['armory_url'], $stats->raider->character); ?>"><img src="raid_includes/images/armory.png" style="border:0;" /></a></td>
		<td style="height:1em;">Name:</td>
		<td><?php echo $stats->raider->ColorizeName(); ?></td>
	</tr>
	<tr>
		<td style="height:1em;">Class:</td>
		<td><?php echo $stats->raider->class; ?></td>
	</tr>
	<tr>
		<td style="height:1em;">Role:</td>
		<td><?php echo $stats->raider->role; ?></td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
</table>
<table style="float:left;">
	<tr><th colspan="4">Raider attendance history</th></tr>
	<tr>
		<th>Raid start</th>
		<th>Signup</th>
		<th>Attended</th>
		<th>Comment</th>
	</tr>
<?php
	$statuscolor = array(
		100 => '#6CB56C',
		50  => '#D8D83A',
		0   => '#F44'
	);
	
	$n = 0;
	foreach($stats->GetAttendanceData() as $raid)
	{
?>
	<tr style="background: <?php echo $n++%2 ? 'transparent' : '#333' ?>;">
<?php
		if(RaidController::$Access > 2)
		{
?>
		<tD><a href="raid.php?action=manage&raid=<?php echo $raid['id']; ?>"><?php echo FormatDate($raid['raidstart'], '%A, %B %e %Y'); ?></a></td>
<?php
		}
		else
		{
?>
		<td><?php echo FormatDate($raid['raidstart'], '%A, %B %e %Y'); ?></td>
<?php
		}
?>
		<td style="color:<?php echo $statuscolor[$raid['percent']]; ?>;"><?php 

		switch($raid['percent'])
		{
			case null:
				echo 'DNS';
				break;
			case 0:
				echo 'Out';
				break;
			case 50:
				echo '50/50';
				break;
			case 100:
				echo 'In';
				break;
		}

?></td>
		<td style="color:<?php echo $statuscolor[$raid['attended']?100:0]; ?>;"><?php if($raid['sitout']) echo 'Yes, but sat out'; else if($raid['attended']) echo 'Yes'; else echo 'No'; ?></td>
		<td><?php echo stripslashes($raid['comment']); ?></td>
	</tr>
<?php
	}
?>
</table>
<object style="float:left;" data="<?php echo $_SERVER['REQUEST_URI']; ?>&show=graph" type="image/svg+xml"
        width="200" height="100">
    <embed src="<?php echo $_SERVER['REQUEST_URI']; ?>&show=graph" type="image/svg+xml"
            width="200" height="100" />
</object>
<div style="clear:both;"></div>