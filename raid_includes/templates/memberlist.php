<?php
	//var_dump($members);
?>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#memberlist').tablesorter({widgets:['zebra']});
	});
</script>
<style type="text/css">
	.odd td { background: #333; }
</style>
<table style="clear:both;" class="tablebg" cellspacing="0" id="memberlist">
	<colgroup>
		<col style="width: 12em;" />
		<col style="width: 12em;" />
		<col style="width: 8em;" />
		<col style="width: 8em;" />
<?php
	if(RaidController::$Access >= 3)
	{
?>
		<col style="width: 25em;" />
<?php
	}
?>
	</colgroup>
	<thead>
		<tr>
			<th style="cursor:pointer">Forum account</th>
			<th style="cursor:pointer">Character</th>
			<th style="cursor:pointer">Class</th>
			<th style="cursor:pointer">Role</th>
<?php
	if(RaidController::$Access >= 3)
	{
?>
			<th style="cursor:pointer">Admin comment</th>
<?php
	}
?>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($members as $member)
	{
?>
		<tr>
			<td><?php echo $member->account; ?></td>
			<td><?php echo $member->ColorizeName(); ?></td>
			<td><?php echo $member->class; ?></td>
			<td><?php echo $member->role; ?></td>
<?php
		if(RaidController::$Access >= 3)
		{
?>
			<td><?php echo stripslashes($member->admincomment); ?></td>
<?php
		}
?>
		</tr>
<?php
	}
?>
	</tbody>
</table>