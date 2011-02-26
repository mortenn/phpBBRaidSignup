<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="action" value="absencelist" />
	<table>
		<tr><th colspan="2">Add a planned absence</th></tr>
		<tr>
			<td>Raider</td>
			<td>
				<select name="manage[new][userid]">
					<option value="0"></option>
<?php
	global $WoWclasses;
	$players = Player::GetAll();

	if(count($players))
		foreach($players as $player)
		{
?>
					<option value="<?php echo $player->userid; ?>" style="background:black;color: <?php echo $WoWclasses[$player->class]['color']; ?>;"><?php echo $player->character; ?></option>
<?php
		}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>First day raider cannot attend</td>
			<td><input type="text" name="manage[new][starting]" /> (yyyy-mm-dd)</td>
		</tr>
		<tr>
			<td>First day raider can attend again</td>
			<td><input type="text" name="manage[new][ending]" /> (yyyy-mm-dd)</td>
		</tr>
		<tr>
			<td>Comment</td>
			<td><input type="text" name="manage[new][comment]" maxlength="100" size="40" /></td>
		</tr>
		<tr><td colspan="2"><input class="btnlite" type="submit" value="Add absence period" name="manage[create_confirm]" /></td></tr>
	</table>
</form>

<?php
	if(count($absences))
	{
?>
	<table>
		<tr><th colspan="4">Upcoming absences</th></tr>
		<tr><th>Who</th><th>Starting</th><th>Ending</th><th>Comment</th></tr>
<?php
		foreach($absences as $period)
		{
			$player = new Player($period->userid);
?>
		<tr>
			<td><?php echo $player->ColorizeName(); ?></td>
			<td><?php echo $period->starting; ?></td>
			<td><?php echo $period->ending; ?></td>
			<td><?php echo $period->comment; ?></td>
		</tr>
<?php
		}
?>
	</table>
<?php
	}
	else
		echo 'No absences';
?>