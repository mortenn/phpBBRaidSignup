<script type="text/javascript" src="raid_includes/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="raid_includes/jquery.tablesorter.min.js"></script>
<p class="cat" style="padding-top: 8px;height:21px;">
<?php
	echo 'Welcome, '.RaidController::$CurrentUser->ColorizeName();
?>
&nbsp;
<?php
	foreach(RaidController::$Modules as $mod)
	{
		if(RaidController::$Access >= $mod->required_level)
			printf('[<a href="raid.php?action=%s">%s</a>]&nbsp;&nbsp;', $mod->name, $mod->label);
	}
?>
</p>
<br />