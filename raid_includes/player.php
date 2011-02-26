<?php
	class Player
	{
		public $userid;
		public $account;
		public $character;
		public $class;
		public $role;
		public $admincomment;
		public $accesslevel;

		public static function &GetAll()
		{
			global $db;
			$players = array();
			$resource = $db->sql_query('
				SELECT u.user_id
				FROM '.USERS_TABLE.' AS u,'.USER_GROUP_TABLE.' AS g,'.GROUPS_TABLE.' AS h,'.PROFILE_FIELDS_DATA_TABLE.' AS p
				WHERE u.user_id=p.user_id AND u.user_id=g.user_id AND user_pending=0 AND g.group_id=h.group_id AND h.group_name=\''.
				RaidController::$config['raider_group'].'\'
				ORDER BY p.pf_'.RaidController::$config['class_field'].', p.pf_'.RaidController::$config['character_field']
			);
			while($user = $db->sql_fetchrow($resource))
			{
				$player = new self($user['user_id']);
				if($player->Complete())
					$players[$player->userid] = $player;
			}
			return $players;
		}

		private function LoadProfile()
		{
			global $db;
			$result = $db->sql_query('SELECT * FROM '.PROFILE_FIELDS_DATA_TABLE.' WHERE user_id='.(int)$this->userid);
			$profile = $db->sql_fetchrow($result);
			if($profile)
				$this->character = $profile['pf_'.RaidController::$config['character_field']];
			else
				$this->character = false;

			if($profile)
				$this->class = RaidController::$config['classmap'][$profile['pf_'.RaidController::$config['class_field']]];
			else
				$this->class = false;

			if($profile)
				$this->role = RaidController::$config['rolemap'][$profile['pf_'.RaidController::$config['role_field']]];
			else
				$this->role = false;

			$resource = $db->sql_query('SELECT username FROM '.USERS_TABLE.' WHERE user_id='.(int)$this->userid);
			$account = $db->sql_fetchrow($resource);
			$this->account = $account['username'];
			if(!$this->character)
				$this->character = $this->account;
		}

		public function __construct($userid = null)
		{
			global $db;
			$result = $db->sql_query('SELECT * FROM raider_usermap WHERE userid='.(int)$userid);
			$player = $db->sql_fetchrow($result);

			if(!$player)
			{
				$data = array('userid'=>(int)$userid,'admincomment'=>'New player','accesslevel'=>0);
				$db->sql_query('INSERT INTO raider_usermap '.$db->sql_build_array('INSERT', $data));

				$this->userid = (int)$userid;
				$this->admincomment = 'New player';
			}
			else
			{
				if($player['userid'] != $userid)
					throw new Exception('Invalid UserID "'.$userid.'"');
				$this->userid = $player['userid'];
				$this->admincomment = $player['admincomment'];
			}
			$this->LoadProfile();
			$this->accesslevel = RaidController::GetAccessLevel($this->userid);
		}

		public function ColorizeName()
		{
			global $WoWclasses;
			return '<span style="color: '.$WoWclasses[$this->class]['color'].'">'.$this->character.'</span>';
		}

		public function Complete()
		{
			return $this->accesslevel > 1 && trim($this->character) && trim($this->class) && trim($this->role);
		}

		public function Update($data)
		{
			if(isset($data['admincomment']) && trim($data['admincomment']) != $this->admincomment)
			{
				$this->admincomment = trim($data['admincomment']);
				$this->Save();
			}
		}

		public function Save()
		{
			$data = array(
				'admincomment' => $this->admincomment,
				'accesslevel' => (int)$this->accesslevel
			);

			global $db;
			$sql = '
				UPDATE raider_usermap 
				SET '.$db->sql_build_array('UPDATE', $data).'
				WHERE userid='.(int)$this->userid;

			$db->sql_query($sql);
		}
	}

	class Attendee extends Player
	{
		public $confidence;
		public $comment;
		public $attended;
		public $raid;
		public $satout;
		public $added;
		public $added_by;
		public $modified;
		public $modified_by;

		public function __construct($data)
		{
			parent::__construct($data['userid']);
			$this->confidence = (int)$data['percent'];
			$this->comment = $data['comment'];
			$this->attended = $data['attended'] == 1;
			$this->satout = $data['sitout'] == 1;
			$this->raid = (int)$data['raidid'];
			$this->added = $data['added'];
			$this->added_by = $data['added_by'] ? new Player($data['added_by']) : null;
			$this->modified = $data['modified'];
			$this->modified_by = $data['modified_by'] ? new Player($data['modified_by']) : null;
		}

		public function SetAttendance($bool)
		{
			global $db;
			$this->attended = $bool;
			$sql = 'UPDATE raider_attendance SET '.$db->sql_build_array('UPDATE',array('attended'=>$bool?1:0)).' WHERE raidid='.(int)$this->raid.' AND userid='.(int)$this->userid;
			$db->sql_query($sql);
		}

		public function SetSitout($bool)
		{
			global $db;
			$this->satout = $bool;
			$this->attended = $bool ? true : $this->attended;
			$sql = 'UPDATE raider_attendance SET '.$db->sql_build_array('UPDATE',$bool?array('attended'=>1,'sitout'=>1):array('sitout'=>0)).' WHERE raidid='.(int)$this->raid.' AND userid='.(int)$this->userid;
			$db->sql_query($sql);
		}
	}
?>