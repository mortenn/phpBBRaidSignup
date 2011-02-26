<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
	<input type="hidden" name="action" value="absences" />
	<table>
		<tr><th colspan="2">Add a planned absence</th></tr>
		<tr>
			<td>First day I cannot attend</td>
			<td><input type="text" name="new[starting]" /> (yyyy-mm-dd)</td>
		</tr>
		<tr>
			<td>First day I can attend again</td>
			<td><input type="text" name="new[ending]" /> (yyyy-mm-dd)</td>
		</tr>
		<tr>
			<td>Comment</td>
			<td><input type="text" name="new[comment]" maxlength="100" size="40" /></td>
		</tr>
		<tr><td colspan="2"><input class="btnlite" type="submit" value="Add absence period" name="create_confirm" /></td></tr>
	</table>
<?php
	if(count($absences))
	{
?>
	<table>
		<tr><th colspan="4">Absence list</th></tr>
		<tr><th>&nbsp;</th><th>Starting</th><th>Ending</th><th>Comment</th></tr>
<?php
		foreach($absences as $period)
		{
?>
		<tr>
			<td><input type="checkbox" name="remove[<?php echo $period->id; ?>]" /></td>
			<td><input size="10" type="text" name="edit[<?php echo $period->id; ?>][starting]" value="<?php echo $period->starting; ?>" /></td>
			<td><input size="10" type="text" name="edit[<?php echo $period->id; ?>][ending]" value="<?php echo $period->ending; ?>" /></td>
			<td><input size="40" maxlength="100" type="text" name="edit[<?php echo $period->id; ?>][comment]" value="<?php echo $period->comment; ?>" /></td>
		</tr>
<?php
		}
?>
		<tr><td colspan="4"><input class="btnlite" type="submit" value="Delete selected periods" name="delete_confirm" /> <input class="btnlite" type="submit" value="Save" /></td></tr>
	</table>
<?php
	}
?>
</form>