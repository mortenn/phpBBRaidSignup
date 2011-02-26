<?php
	class RaidCreation implements RaidControllerModule
	{
		public $name = 'create';
		public $label = 'Create raid';
		public $required_level = 3;
		public $required_post = 3;

		public function ProcessPost($data)
		{
			$raid = new Raid();
			if(isset($_POST['auto']))
			{
				$month = date('m',strtotime($raid->raidstart));
				$raid->raidstart = date('Y-m-d H:i:s', Timestamp($data['raidstart']));
				$raid->deadline = date('Y-m-d H:i:s', Timestamp($data['deadline']));
				while(date('m',strtotime($raid->raidstart)) == $month)
				{
					$raid->thumbnail = $data['thumbnail'];
					$raid->comment = trim($data['comment']);
					$raid->attendance = isset($data['attendance']);
					$raid->Save();
					$raid = new Raid();
				}
			}
			else
			{
				$raid->raidstart = date('Y-m-d H:i:s', Timestamp($data['raidstart']));
				$raid->deadline = date('Y-m-d H:i:s', Timestamp($data['deadline']));
				$raid->thumbnail = $data['thumbnail'];
				$raid->comment = trim($data['comment']);
				$raid->attendance = isset($data['attendance']);
				$raid->Save();
			}
		}

		public function __tostring()
		{
			ob_start();
			$raid = new Raid();
				require(RaidController::$templatedir.'newraid.php');
			return ob_get_clean();
		}
	}
	RaidController::RegisterModule(new RaidCreation());
?>