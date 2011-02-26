<?php
	class RaidList implements RaidControllerModule
	{
		public $name = 'raidlist';
		public $label = 'Raid list';
		public $required_level = 1;
		public $required_post = 2;

		public function ProcessPost($data)
		{
			if(isset($data['raidid']))
				$raid = new Raid($data['raidid']);

			$comment = null;
			if(isset($data['comment']))
			{
				$comment = trim($data['comment']);
				if($comment[0] == '[')
					$comment = null;
			}

			if(isset($data['signup']) && is_array($data['signup']))
			{
				foreach($data['signup'] as $id => $pct)
				{
					$raid = new Raid($id);
					$raid->Signup(RaidController::$UserID, $pct, $comment, false);
				}
				return;
			}

			if($raid)
			{
				if(!isset($data['join']))
					$raid->signup(RaidController::$UserID, $raid->raiders[RaidController::$UserID]->confidence, $comment, false);
				else
					foreach($data['join'] as $pct => $dummy)
						$raid->Signup(RaidController::$UserID, $pct, $comment, false);
			}
		}

		public function __tostring()
		{
			ob_start();
			if(RaidController::$selected)
				$raids = Raid::GetByDay(
					RaidController::$selected['y'],
					RaidController::$selected['m'],
					RaidController::$selected['d']
				);
			else
				$raids = Raid::GetByMonth(
					RaidController::$year, 
					RaidController::$month, 
					false
				);

			$expired = array_reverse(
				Raid::GetByMonth(
					RaidController::$year, 
					RaidController::$month, 
					true
				)
			);

			if(RaidController::$Access >= 2)
				$unsigned = Raid::GetUnsigned();
			else
				$unsigned = array();

			if(count($raids) == 0)
			{
				$raids = $expired;
				$expired = array();
			}
				
			require(RaidController::$templatedir.'raidlist.php');
			return ob_get_clean();
		}
	}
	RaidController::RegisterModule(new RaidList());
?>