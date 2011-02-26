<?php
	class RaidStatistics implements RaidControllerModule
	{
		public $name = 'statistics';
		public $label = 'Attendance';
		public $required_level = 2;
		public $required_post = 3;

		public function ProcessPost($data)
		{
		}

		public function __tostring()
		{
			ob_start();
			if(isset($_GET['player']))
			{
				$stats = new PlayerStatistics(
					(int)$_GET['player'], 
					RaidController::$year, 
					RaidController::$month, 
					RaidController::$year == (int)date('Y') && RaidController::$month == (int)date('m')
				);
				if(isset($_GET['show']) && $_GET['show'] == 'graph')
				{
					ob_end_clean();
					header('Content-Type: image/svg+xml');
					require(RaidController::$templatedir.'playerstats.svg.php');
					die();
				}	
				else
					require(RaidController::$templatedir.'playerstats.php');
			}
			else
			{
				$stats = new Statistics(
					RaidController::$year,
					RaidController::$month,
					RaidController::$year == (int)date('Y') && RaidController::$month == (int)date('m')
				);
				require(RaidController::$templatedir.'raidstats.php');
			}
			return ob_get_clean();
		}
	}
	RaidController::RegisterModule(new RaidStatistics());
?>