<?php
	class RaidMembers implements RaidControllerModule
	{
		public $name = 'members';
		public $label = 'Raiders';
		public $required_level = 1;
		public $required_post = 3;

		public function ProcessPost($data)
		{
		}

		public function __tostring()
		{
			ob_start();
			$members = Player::GetAll();
			require(RaidController::$templatedir.'memberlist.php');
			return ob_get_clean();
		}
	}
	RaidController::RegisterModule(new RaidMembers());
?>