<?php
	$mn = date('F',$timestamp);
	require($templatedir.'raidcalendar.php');

	$details = $stats->GetStats();
?>
<script type="text/javascript">
	$(document).ready(function()
	{
		$('#attendancelist').tablesorter({widgets: ['zebra']});
	});

	function showGraph(raider)
	{
		var url = '<?php echo $_SERVER['REQUEST_URI']; ?>&player=' + raider + '&show=graph';
		var emb = document.createElement('embed');
		var obj = document.createElement('object');
		var anchor = document.getElementById('graph');
		while(anchor.childNodes.length)
			anchor.removeChild(anchor.childNodes[0]);

		emb.width = 200;
		emb.height = 100;
		emb.type = 'image/svg+xml';
		emb.src = url;

		obj.data = url;
		obj.width = 200;
		obj.height = 100;
		obj.type = 'image/svg+xml';
		obj.appendChild(emb);

		anchor.appendChild(obj);
	}
</script>
<div><?php echo $stats->total; ?> raids included in these statistics</div>
<div style="float: left;">
	<style type="text/css">
		.odd td { background: #333; }
	</style>
	<table cellspacing="0" cellpadding="0"><tr><td>
	<table cellspacing="0" cellpadding="0" width="100%">
		<colgroup>
			<col style="width: 150px;" />
			<col style="width: 60px;" />
			<col style="width: 60px;" />
			<col style="width: 60px;" />
			<col style="width: 30px;" />
			<col />
			<!--col style="width: 210px;" /-->
		</colgroup>
		<thead>
			<tr><th colspan="6"><?php echo $stats->sliding ? '8 weeks sliding attendance' : 'Attendance for '.$mn; ?></th></tr>
			<tr><th>&nbsp;</th><th>Out</th><th>50/50</th><th>In</th><th>&nbsp;</th><th>Overall</th></tr>
		</thead>
	</table>
	</td></tr><tr><td>
	<table id="attendancelist" cellspacing="0" cellpadding="0">
		<colgroup>
			<col style="width: 150px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 30px;" />
			<col style="width: 50px;" />
			<col style="width: 50px;" />
			<col style="width: 20px;" />
		</colgroup>
		<thead>
			<tr>
				<th style="cursor:pointer;">Character</th>
				<th style="cursor:pointer;" title="Number of times signed up as out">S</th>
				<th style="cursor:pointer;" title="Number of times attended anyway">A</th>
				<th style="cursor:pointer;" title="Number of times signed up as 50/50">S</th>
				<th style="cursor:pointer;" title="Number of times attended">A</th>
				<th style="cursor:pointer;" title="Number of times signed up as in">S</th>
				<th style="cursor:pointer;" title="Number of times attended">A</th>
				<th>&nbsp;</th>
				<th style="cursor:pointer;" title="Number of times signed up as in or 50/50">S</th>
				<th style="cursor:pointer;" title="Number of times attended">A</th>
				<th style="cursor:pointer;" title="Number of times player sat out">O</th>
				<th style="cursor:pointer;" title="Percentage of times signed up">S%</th>
				<th style="cursor:pointer;" title="Percentage of times attended">A%</th>
<?php
	if($stats->hMon)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a monday">Mon%</th>
<?php
	}
	if($stats->hTue)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a tuesday">Tue%</th>
<?php
	}
	if($stats->hWed)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a wednesday">Wed%</th>
<?php
	}
	if($stats->hThu)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a thursday">Thu%</th>
<?php
	}
	if($stats->hFri)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a friday">Fri%</th>
<?php
	}
	if($stats->hSat)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a saturday">Sat%</th>
<?php
	}
	if($stats->hSun)
	{
?>
				<th style="cursor:pointer;" title="Percentage of times attended on a sunday">Sun%</th>
<?php
	}
?>
				<th style="cursor:pointer;" title="Display graph for raider">G</th>
			</tr>
		</thead>
		<tbody>
<?php
	$n = 0;
	foreach($details as $stat)
	{
		// style="background: < ?php echo $n++%2 ? 'transparent' : '#333' ? >;"
?>
			<tr>
				<th style="text-align: left;"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;player=<?php echo $stat->raider->userid; ?>"><?php echo $stat->raider->ColorizeName(); ?></a></th>
				<td style="text-align: right;" title="Signups as out"><?php echo $stat->s0; ?></td>
				<td style="text-align: right;" title="Attended anyway"><?php echo $stat->a0; ?></td>
				<td style="text-align: right;" title="Signups as 50/50"><?php echo $stat->s50; ?></td>
				<td style="text-align: right;" title="Attended"><?php echo $stat->a50; ?></td>
				<td style="text-align: right;" title="Signups as int"><?php echo $stat->s100; ?></td>
				<td style="text-align: right;<?php if($stat->a100 < $stat->s100) echo ' color: red;'; ?>" title="Attended"><?php echo $stat->a100; ?></td>
				<th>&Sigma;</th>
				<td style="text-align: right;" title="Signups as 50/50 or in"><?php echo $stat->signups; ?></td>
				<td style="text-align: right;" title="Attended"><?php echo $stat->attendance; ?></td>
				<td style="text-align: right;" title="Sitouts"><?php echo $stat->sitouts; ?></td>
				<td style="text-align: right;<?php if($stat->stp < 0.5) echo ' color: red;'; else if($stat->stp >= 0.66) echo ' color: lightgreen;'; ?>" title="Signup %"><?php printf('%d%%', $stat->stp * 100); ?></td>
				<td style="text-align: right;<?php if($stat->atp < 0.5) echo ' color: red;'; else if($stat->atp >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->atp * 100); ?></td>
<?php
	if($stats->hMon)
	{
?>
				<td style="text-align: right;<?php if($stat->aMon < 0.5) echo ' color: red;'; else if($stat->aMon >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aMon * 100); ?></td>
<?php
	}
	if($stats->hTue)
	{
?>
				<td style="text-align: right;<?php if($stat->aTue < 0.5) echo ' color: red;'; else if($stat->aTue >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aTue * 100); ?></td>
<?php
	}
	if($stats->hWed)
	{
?>
				<td style="text-align: right;<?php if($stat->aWed < 0.5) echo ' color: red;'; else if($stat->aWed >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aWed * 100); ?></td>
<?php
	}
	if($stats->hThu)
	{
?>
				<td style="text-align: right;<?php if($stat->aThu < 0.5) echo ' color: red;'; else if($stat->aThu >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aThu * 100); ?></td>
<?php
	}
	if($stats->hFri)
	{
?>
				<td style="text-align: right;<?php if($stat->aFri < 0.5) echo ' color: red;'; else if($stat->aFri >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aFri * 100); ?></td>
<?php
	}
	if($stats->hSat)
	{
?>
				<td style="text-align: right;<?php if($stat->aSat < 0.5) echo ' color: red;'; else if($stat->aSat >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aSat * 100); ?></td>
<?php
	}
	if($stats->hSun)
	{
?>
				<td style="text-align: right;<?php if($stat->aSun < 0.5) echo ' color: red;'; else if($stat->aSun >= 0.66) echo ' color: lightgreen;'; ?>" title="Attendance %"><?php printf('%d%%', $stat->aSun * 100); ?></td>
<?php
	}
?>
				<td><input type="radio" name="showgraph" onclick="setTimeout('showGraph(<?php echo $stat->raider->userid; ?>);',10);" /></td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
	</td></tr></table>
</div>
<div style="float: left; margin-left: 1em;">
	<table>
		<colgroup>
			<col />
			<col style="width: 3em;" />
		</colgroup>
		<thead>
			<tr><th colspan="2"><?php echo $stats->sliding ? '8 weeks sliding sitouts' : 'Sitouts for '.$mn; ?></th></tr>
			<tr><th>Character</th><th>Sitouts</th></tr>
		</thead>
<?php
	$n = 0;
	$role = false;
	foreach($stats->SitOuts() as $out)
	{
		if($role != $out->raider->role)
		{
			if($role !== false)
			{
?>
		</tbody>
<?php
			}
			$role = $out->raider->role;
?>
		<tr><th colspan="2" style="cursor:pointer;" onclick="var l=document.getElementById('sitout_<?php echo $role; ?>'); l.style.display=l.style.display==''?'none':'';"><?php echo $role; ?></th></tr>
		<tbody id="sitout_<?php echo $role; ?>" style="display:none;">
<?php
		}
?>
			<tr style="background: <?php echo $n++%2 ? 'transparent' : '#333' ?>;">
				<th style="text-align: left;"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&amp;player=<?php echo $out->raider->userid; ?>"><?php echo $out->raider->ColorizeName(); ?></a></th>
				<td style="text-align:right;"><?php echo $out->sitouts; ?></td>
			</tr>
<?php
	}
	if($role !== false)
	{
?>
		</tbody>
<?php
	}
?>
	</table>
</div>
<div id="graph" style="width: 200px; height: 100px; float:left;">
</div>
<div style="clear: both;"></div>