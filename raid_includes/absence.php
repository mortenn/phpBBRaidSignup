<?php
	class Absence
	{
		public $id;
		public $raider;
		public $userid;
		public $starting;
		public $ending;
		public $comment;

		public static function GetListForUser($user)
		{
			global $db;
			$resource = $db->sql_query('SELECT id FROM raider_vacation WHERE v_ending > NOW() && user_id='.(int)$user);
			$vacation = array();
			if($resource)
				while($data = $db->sql_fetchrow($resource))
					$vacation[] = new self($data['id']);
			return $vacation;
		}

		public static function GetListForEveryone()
		{
			global $db;
			$resource = $db->sql_query(
				'SELECT id FROM raider_vacation '. //WHERE v_ending > NOW()');
				'LEFT JOIN '.USER_GROUP_TABLE.' AS g ON (raider_vacation.user_id = g.user_id) '.
				'LEFT JOIN '.GROUPS_TABLE.' AS h ON (g.group_id = h.group_id) '.
				'WHERE v_ending > NOW() AND h.group_name=\''.RaidController::$config['raider_group'].'\''
			);
			$vacation = array();
			if($resource)
				while($data = $db->sql_fetchrow($resource))
					$vacation[] = new self($data['id']);
			return $vacation;
		}

		public static function GetListForDay($date)
		{
			global $db;
			$resource = $db->sql_query(
				'SELECT id FROM raider_vacation '. //WHERE v_ending > NOW()');
				'LEFT JOIN '.USER_GROUP_TABLE.' AS g ON (raider_vacation.user_id = g.user_id) '.
				'LEFT JOIN '.GROUPS_TABLE.' AS h ON (g.group_id = h.group_id) '.
				'WHERE v_starting <= \''.$date.'\' AND v_ending > NOW() AND h.group_name=\''.RaidController::$config['raider_group'].'\''
			);
			$vacation = array();
			if($resource)
				while($data = $db->sql_fetchrow($resource))
					$vacation[] = new self($data['id']);
			return $vacation;
		}

		public static function Create($data)
		{
			if(strtotime($data['starting']) > time() && strtotime($data['ending']) > strtotime($data['starting']))
			{
				$vacation = new self();
				$vacation->userid = $data['userid'];
				$vacation->starting = $data['starting'];
				$vacation->ending = $data['ending'];
				$vacation->comment = $data['comment'];
				$vacation->Save();
			}
		}

		public static function Edit($id, $data)
		{
			$vacation = new self($id);
			$vacation->Update($data);
		}

		public static function Delete($id)
		{
			global $db;

			$vacation = new self($id);
			$vacation->ClearRaidSignups();
			$db->sql_query('DELETE FROM raider_vacation WHERE id='.(int)$id);
		}

		public function __construct($id = false)
		{
			global $db;
			if($id)
			{
				$resource = $db->sql_query('SELECT * FROM raider_vacation WHERE id='.(int)$id);
				if($resource)
				{
					$data = $db->sql_fetchrow($resource);
					$this->id       = $data['id'];
					$this->raider   = new Player($data['user_id']);
					$this->userid   = $data['user_id'];
					$this->starting = $data['v_starting'];
					$this->ending   = $data['v_ending'];
					$this->comment  = $data['comment'];
				}
			}
		}

		public function Update($data)
		{
			if(!$this->id)
				return;

			if($this->starting != $data['starting'] || $this->ending != $data['ending'])
				$this->ClearRaidSignups();

			$this->starting = $data['starting'];
			$this->ending = $data['ending'];
			$this->comment = trim($data['comment']);

			$this->Save();
		}

		public function Save()
		{
			global $db;
			$data = array(
				'user_id' => $this->userid,
				'v_starting' => $this->starting,
				'v_ending' => $this->ending,
				'comment' => trim($this->comment)
			);

			if(!$this->id)
				$db->sql_query('INSERT INTO raider_vacation '.$db->sql_build_array('INSERT',$data));
			else
				$db->sql_query('UPDATE raider_vacation SET '.$db->sql_build_array('UPDATE',$data).' WHERE id='.(int)$this->id);

			$this->SignOutOfRaids();
		}

		private function SignOutOfRaids()
		{
			$raids = Raid::GetByPeriod($this->starting, $this->ending);
			foreach($raids as &$raid)
				$raid->Signup($this->userid, 0, 'Absence: '.trim($this->comment), false);
		}

		private function ClearRaidSignups()
		{
			$raids = Raid::GetByPeriod($this->starting, $this->ending);
			foreach($raids as $raid)
				$raid->Signup($this->userid, null, false);
		}
	}
?>