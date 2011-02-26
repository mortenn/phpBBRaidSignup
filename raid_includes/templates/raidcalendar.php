<?php
	$statuscolor = array(
		100 => '#6CB56C',
		50  => '#D8D83A',
		0   => '#F44'
	);
	$prevy = RaidController::$year;
	$nexty = RaidController::$year;
	$prevm = RaidController::$month - 1;
	$nextm = RaidController::$month + 1;
	if($prevm == 0)
	{
		$prevy--;
		$prevm = 12;
	}
	if($nextm == 13)
	{
		$nexty++;
		$nextm = 1;
	}
	$actionref = RaidController::$Module;
	if(isset($_GET['raid']))
		$actionref .= '&raid='.$_GET['raid'];
	if(isset($_GET['player']))
		$actionref .= '&player='.$_GET['player'];

	$timestamp = RaidController::$timestamp;
	$year = RaidController::$year;
	$month = RaidController::$month;
	$selected = RaidController::$selected;
?>
<table style="float: right;" cellspacing="1" cellpadding="2">
	<colgroup>
		<col style="width: 25px;" />
		<col style="width: 25px;" />
		<col style="width: 25px;" />
		<col style="width: 25px;" />
		<col style="width: 25px;" />
		<col style="width: 25px;" />
		<col style="width: 25px;" />
	</colgroup>
	<thead>
		<tr>
			<th><a href="raid.php?action=<?php echo $actionref; ?>&<?php printf('year=%04d&month=%02d', $prevy, $prevm); ?>">&lt;-</a></th>
			<th colspan="5"><?php echo date('F, Y', $timestamp); ?></th>
			<th><a href="raid.php?action=<?php echo $actionref; ?>&<?php printf('year=%04d&month=%02d', $nexty, $nextm); ?>">-&gt;</a></th>
		</tr>
		<tr>
			<th>M</th>
			<th>T</th>
			<th>W</th>
			<th>T</th>
			<th>F</th>
			<th>S</th>
			<th>S</th>
		</tr>
	</thead>
	<tbody>
<?php
	$m = array();
	if((date('w',$timestamp) + 6) % 7 > 0)
		$w = array_fill(0,(date('w',$timestamp) + 6) % 7,'');
	else
		$w = array();
	while(date('n',$timestamp) == RaidController::$month)
	{
		if(count($w) == 7)
		{
			$m[] = $w;
			$w = array();
		}
		$w[] = date('d',$timestamp);
		$timestamp += 86400;
	}
	if(count($w) > 0 && count($w) <= 7)
	{
		for($i = count($w); $i < 7; ++$i)
			$w[] = '';
		$m[] = $w;
	}

	$now = array(
		'y' => date('Y'),
		'm' => date('m'),
		'd' => date('d')
	);
	
	foreach($m as $w)
	{
?>
		<tr>
<?php
		foreach($w as $d)
		{
			$today = ($now['y'] == $year && $now['m'] == $month && $now['d'] == $d);
			$current = ($selected && $selected['y'] == $year && $selected['m'] == $month && $selected['d'] == $d);
			if(!$d)
			{
				echo '<td>&nbsp;</td>';
				continue;
			}
			$status = Raid::GetStatus($year, $month, (int)$d);
			$style = array('text-align:right');
			if($status === false)
			{
				if($today)
				{
					$style[] = 'background:#006998';
					$style[] = 'color:white';
					$style[] = 'font-weight:bolder';
				}
				else if($current)
				{
					$style[] = 'background:#7f8400';
					$style[] = 'color:white';
				}
				else
				{
					$style[] = 'background:white';
					$style[] = 'color:black';
				}
			}
			else if($status !== null)
			{
				if($today)
				{
					$style[] = 'background:#006998';
					$style[] = 'color:'.$statuscolor[$status];
					$style[] = 'font-weight:bolder';
				}
				else if($current)
				{
					$style[] = 'background:#7f8400';
					$style[] = 'color:'.$statuscolor[$status];
				}
				else
				{
					$style[] = 'background:'.$statuscolor[$status];
					$style[] = 'color:black';
				}
			}
			else if($today)
			{
				$style[] = 'background:#006998';
				$style[] = 'color:white';
				$style[] = 'font-weight:bolder';
			}
			else if($current)
			{
				$style[] = 'background:#7f8400';
				$style[] = 'color:white';
			}

?>
			<td style="<?php echo join(';',$style); ?>">
<?php
			if($status !== null)
			{
?>
				<a style="<?php echo join(';',$style); ?>;" href="raid.php?action=<?php echo RaidController::$Module; ?>&<?php printf('year=%04d&month=%02d&day=%02d', $year, $month, $d); ?>"><?php echo $d; ?></a>
<?php
			}
			else
				echo $d;
?>
			</td>
<?php
		}
?>
		</tr>
<?php
	}
?>
	</tbody>
	<tfoot>
		<tr><td colspan="7">&nbsp;</td></tr>
	</tfoot>
</table>