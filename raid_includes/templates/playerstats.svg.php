<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>

<svg
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns="http://www.w3.org/2000/svg"
   width="200"
   height="100"
>
  <defs
     id="defs4">
    <linearGradient
       id="linearGradient3235">
      <stop
         style="stop-color:#0092ff;stop-opacity:1;"
         offset="0"
         id="stop3237" />
      <stop
         style="stop-color:#0092ff;stop-opacity:0.2;"
         offset="1"
         id="stop3239" />
    </linearGradient>
    <linearGradient
       xlink:href="#linearGradient3235"
       id="linearGradient3241"
       x1="100"
       y1="0"
       x2="100"
       y2="100"
       gradientUnits="userSpaceOnUse" />

	<linearGradient
       id="signup">
      <stop
         style="stop-color:#00ff92;stop-opacity:1;"
         offset="0"
         id="stop0" />
      <stop
         style="stop-color:#00ff92;stop-opacity:0.2;"
         offset="1"
         id="stop1" />
    </linearGradient>
    <linearGradient
       xlink:href="#signup"
       id="signupfill"
       x1="100"
       y1="0"
       x2="100"
       y2="100"
       gradientUnits="userSpaceOnUse" />
	</defs>
<?php
	$graph = $stats->GetAttendanceHistory();
	$step = 200 / (count($graph) - 1);
	$last = false;
?>
	<path
		style="fill:url(#signupfill);fill-opacity:0.6;stroke:none"
		d="M 0,100<?php

	$x = 0;
	foreach($graph as $data)
	{
		if($last === false)
		{
			$last = $data['signup'];
			if($last != 0)
				printf(' L %f,%f', 0, 100 - $last);
		}
		if($last != $data['signup'])
		{
			printf(' L %f,%f L %f,%f', $x - ($step/2), 100 - $last, $x - ($step/2), 100 - $data['signup']);
			$last = $data['signup'];
		}
		$x += $step;
	}
	if($last != 0)
		printf(' L %f,%f', 200, 100 - $last);

?> L 200,100 z"
		/>
	<path
		style="fill:url(#linearGradient3241);fill-opacity:1;stroke:none"
		d="M 0,100<?php

	$x = 0;
	foreach($graph as $data)
	{
		printf(' L %f,%f', $x, 100 * (1 - $data['attendance']));
		$x += $step;
	}

?> L 200,100 z"
		/>
</svg>