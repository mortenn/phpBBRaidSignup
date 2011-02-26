<?php
	class RaidAbsences implements RaidControllerModule
	{
		public $name = 'absences';
		public $label = 'Absences';
		public $required_level = 2;
		public $required_post = 2;

		public function ProcessPost($data)
		{
			if(RaidController::$Access > 2)
			{
				if(isset($data['manage']['new']) && (int)$data['manage']['new']['userid'] > 0)
					Absence::Create($_POST['manage']['new']);

				if(isset($data['manage']['remove']) && isset($data['manage']['delete_confirm']))
					foreach($data['manage']['remove'] as $id => $d)
					{
						unset($data['manage']['edit'][$id]);
						Absence::Delete($id);
					}

				if(isset($data['manage']['edit']))
					foreach($data['manage']['edit'] as $id => $absence)
						Absence::Edit($id, $absence);

			}

			if(isset($data['create_confirm']))
			{
				$data['new']['userid'] = RaidController::$UserID;
				Absence::Create($data['new']);
			}

			if(isset($data['remove']) && isset($data['delete_confirm']))
				foreach($data['remove'] as $id => $d)
				{
					unset($data['edit'][$id]);
					Absence::Delete($id);
				}

			if(isset($data['edit']))
				foreach($data['edit'] as $id => $absence)
					Absence::Edit($id, $absence);
		}

		public function __tostring()
		{
			ob_start();
			if(RaidController::$Access > 2)
			{
				$absences = Absence::GetListForEveryone();
				require(RaidController::$templatedir.'absencelist.php');
			}
			$absences = Absence::GetListForUser(RaidController::$UserID);
			require(RaidController::$templatedir.'absences.php');

			return ob_get_clean();
		}
	}
	RaidController::RegisterModule(new RaidAbsences());
?>