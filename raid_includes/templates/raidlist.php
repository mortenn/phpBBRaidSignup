<?php
	require(RaidController::$templatedir.'raidcalendar.php');
	if(count($expired) || count($unsigned))
	{
?>
<div style="float:left; margin-right:5px;">
<?php
		if(count($unsigned))
		{
?>
	<div style="text-align:center; padding: 10px;">New raids</div>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<input type="submit" value="Sign up" class="btnlite" />
<?php
		foreach($unsigned as $raid)
			require(RaidController::$templatedir.'unsignedraid.php');
?>
		<input type="submit" value="Sign up" class="btnlite" />
	</form>
<?php
		}
		if(count($expired))
		{
?>
	<div style="text-align:center; padding: 10px;">Expired raids</div>
<?php
		foreach($expired as $raid)
			require(RaidController::$templatedir.'expiredraid.php');
		}	
?>
</div>
<div style="float:left; margin-right:5px;">
<?php
	}
	foreach($raids as $raid)
		require(RaidController::$templatedir.'raid.php');
?>
</div>
<div style="clear: both;"></div>