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
<table class="post" style="margin: 0 1em 2em 0;">
	<colgroup>
		<col style="width: 112px;" />
		<col style="width: 14em;" />
		<col style="width: 14em;" />
		<col style="width: 14em;" />
	</colgroup>
	<tr>
		<td rowspan="5" style="vertical-align:top;text-align:center;">
<?php
	if(RaidController::$Access > 2)
	{
?>
			<a href="raid.php?action=manage&raid=<?php echo $raid->id; ?>"><img src="raid_includes/icons/<?php echo $raid->thumbnail; ?>" width="100" /></a>
<?php
	}
	else
	{
?>
			<img src="raid_includes/icons/<?php echo $raid->thumbnail; ?>" width="100" />
<?php
	}

	$wws = $raid->WowWebStats();
	if($wws)
	{
?>
		<a href="<?php echo $wws; ?>"><img style="border: none;" src="images/wws.png" /></a>
<?php
	}
?>

		</td>
		<td colspan="3" style="height:30px;text-align:center;margin-bottom:2px;padding:4px;background:<?php echo $conf === false ? 'gray' : $statuscolor[(int)$conf]; ?>;">
<?php
	if(RaidController::$Access < 2)
		echo 'You cannot sign up to raids.';
	else if(strtotime($raid->deadline) < time())
	{
		if(!$raid->attendance)
		{
?>
			<img style="float: right;" src="raid_includes/images/none.png" title="Attendance was not recorded for this raid" />
<?php
		}
		else if($signup)
		{
?>
			<img style="float: right;" src="raid_includes/images/<?php echo $signup->attended ? 'check.png' : 'cross.png'; ?>" title="<?php echo $signup->attended ? 'You attended this raid' : 'You did not attend this raid'; ?>" />
<?php
			if($signup->satout)
			{
?>
			<img style="float: right;" src="raid_includes/images/sitout.png" title="You sat out of this raid" />
<?php
			}
		}
		echo '<div style="padding-top: 4px;">Signups have closed.</div>';
	}
	else
	{
?>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<span style="font-weight: bold;">Sign up:</span>
				<input type="hidden" name="action" value="raidlist" />
				<input type="hidden" name="raidid" value="<?php echo $raid->id; ?>" />
				<input type="submit" class="btnlite" name="join[100]" value="In" <?php if($conf === 100) echo 'disabled="disabled"'; ?> />
				<input type="submit" class="btnlite" name="join[50]" value="50/50" <?php if($conf === 50) echo 'disabled="disabled"'; ?> />
				<input type="submit" class="btnlite" name="join[0]" value="Out" <?php if($conf === 0) echo 'disabled="disabled"'; ?> />
			</form>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" />
				<input type="hidden" name="action" value="raidlist" />
				<input type="hidden" name="raidid" value="<?php echo $raid->id; ?>" />
				<input type="text" name="comment" style="width: 20em;" value="<?php 

		$comment = trim($raid->raiders[RaidController::$CurrentUser->userid]->comment);
		if(empty($comment))
			echo '[Add a comment here]';
		else
			echo $comment;

?>" onfocus="if(this.value=='[Add a comment here]')this.value='';" />
				<input type="submit" class="btnlite" value="Save" />
			</form>
<?php
	}
?>
		</td>
	</tr>
	<tr>
		<td  colspan="3">
			<h2><span style="color:#ff9c00;">Raidstart</span>: <?php

	echo '<span style="color:#2bb200">'.FormatDate($raid->raidstart).'</span>';

	if(time() < strtotime($raid->raidstart))
		printf(' (in %s)', TimeLeft($raid->raidstart));
	echo '<br />';
	if($raid->deadline)
	{
		$left = strtotime($raid->deadline) - time();
		if($left > 0)
			printf('<span style="color:#ff9c00;">Signups close %s from now</span>',TimeLeft($raid->deadline));
	}
?></h2>
		</td>
	</tr>
	<tr>
		<td colspan="3"><?php echo nl2br($raid->comment); ?></td>
	</tr>
	<tr>
<?php
	foreach(array('tank'=>'Tanks','dps'=>'DPS','healer'=>'Healers') as $k => $role)
	{
?>
		<td style="vertical-align:top;">
			<table style="cursor:pointer;" width="100%" cellspacing="0" onclick="var l=document.getElementById('raiders_<?php echo $raid->id; ?>_<?php echo $k; ?>'); l.style.display=l.style.display==''?'none':'';">
				<tr><th colspan="3" class="cat"><?php echo $role; ?></th></tr>
				<tr style="font-weight: bold;">
					<td style="color: #9F9;">+ <?php echo $status[$k][100]; ?></td>
					<td style="color: #FF4;">? <?php echo $status[$k][50]; ?></td>
					<td style="color: #F44;">- <?php echo $status[$k][0]; ?></td>
				</tr>
			</table>
			<table cellspacing="0" id="raiders_<?php echo $raid->id; ?>_<?php echo $k; ?>" style="display:none;">
				<colgroup>
					<col style="width: 3em;" />
					<col style="width: 10em;" />
				</colgroup>
<?php
			foreach($raid->raiders as $raider)
			{
				if($raider->role != $k)
					continue;
?>
				<tr style="background: black;color:white;">
					<td style="color:<?php echo $statuscolor[$raider->confidence]; ?>;"><?php echo $raider->confidence; ?>%</td>
					<td><?php echo $raider->ColorizeName(); ?></td>
				</tr>
<?php
			}
?>
			</table>
		</td>
<?php
	}
?>
	</tr>
</table>