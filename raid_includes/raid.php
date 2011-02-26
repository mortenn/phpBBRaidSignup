<?php
	class Raid
	{
		public $raiders;

		public $id;
		public $deadline;
		public $raidstart;
		public $comment;

		public $wws_url;
		public $wws_expiry;

		public $thumbnail;

		public $attendance;

		public static function GetByMonth($year, $month, $expired = false)
		{
			global $db;
			$resource = $db->sql_query(
				'SELECT id FROM raid_list '.
				'WHERE YEAR(raidstart)='.(int)$year.
				' AND MONTH(raidstart)='.(int)$month.' '.
				' AND raidstart '.($expired?'<':'>').' \''.$db->sql_escape(date('Y-m-d H:i',time() - 3600 + RaidController::$TimeOffset)).'\' '.
				'ORDER BY raidstart ASC'
			);
			$raids = array();
			while($rid = $db->sql_fetchrow($resource))
				$raids[] = new self($rid['id']);
			return $raids;
		}

		public static function GetByPeriod($start, $end)
		{
			global $db;
			$resource = $db->sql_query(
				'SELECT id FROM raid_list '.
				'WHERE raidstart BETWEEN \''.$start.'\' AND \''.$end.'\' AND raidstart > NOW()'
			);
			$raids = array();
			while($rid = $db->sql_fetchrow($resource))
				$raids[] = new self($rid['id']);
			return $raids;
		}

		public static function GetByDay($year, $month, $day)
		{
			global $db;
			$resource = $db->sql_query(
				'SELECT id FROM raid_list '.
				'WHERE YEAR(raidstart)='.(int)$year.
				' AND MONTH(raidstart)='.(int)$month.
				' AND DAYOFMONTH(raidstart)='.(int)$day.' '.
				'ORDER BY raidstart ASC'
			);
			$raids = array();
			while($rid = $db->sql_fetchrow($resource))
				$raids[] = new self($rid['id']);
			return $raids;
		}

		public static function GetAll($expired = false)
		{
			global $db;
			$sql = 'SELECT id FROM raid_list';

			if(!$expired)
				$sql .= ' WHERE raidstart > \''.$db->sql_escape(date('Y-m-d H:i',time()-3600)).'\'';

			$sql .= ' ORDER BY raidstart ASC';
			$resource = $db->sql_query($sql);
			$raids = array();
			while($rid = $db->sql_fetchrow($resource))
				$raids[] = new self($rid['id']);
			return $raids;
		}

		public static function GetUnsigned()
		{
			global $db;
			$sql =
				'SELECT id '.
				'FROM raid_list '.
				'WHERE raidstart > NOW() AND (SELECT userid FROM raider_attendance WHERE raidid=raid_list.id AND userid='.RaidController::$UserID.') IS NULL';

			$resource = $db->sql_query($sql);
			$raids = array();
			while($rid = $db->sql_fetchrow($resource))
				$raids[] = new self($rid['id']);
			return $raids;
		}

		public static function GetStatus($year, $month, $day)
		{
			global $db;
			$sql = 'SELECT COUNT(id) AS raids, MAX((SELECT percent FROM raider_attendance WHERE userid='.RaidController::$UserID.' AND raidid=raid_list.id)) AS status '.
				'FROM raid_list '.
				'WHERE YEAR(raidstart)='.$year.' AND MONTH(raidstart)='.$month.' AND DAYOFMONTH(raidstart)='.$day;
			$resource = $db->sql_query($sql);
			$status = $db->sql_fetchrow($resource);
			if(!$status || $status['raids'] == 0)
				return null;

			if($status['status'] === null)
				return false;

			return (int)$status['status'];
		}

		public function __construct($id = null)
		{
			$this->raiders = array();
			if($id !== null)
			{
				global $db;
				$resource = $db->sql_query('SELECT * FROM raid_list WHERE id='.(int)$id);
				$raid = $db->sql_fetchrow($resource);
				if(!$raid || $raid['id'] != $id)
					throw new Exception('Invalid Raid ID "'.$id.'"');

				$this->id = $raid['id'];
				$this->deadline = $raid['deadline'];
				$this->raidstart = $raid['raidstart'];
				$this->comment = $raid['comment'];
				$this->wws_url = $raid['wws_url'];
				$this->wws_expiry = $raid['wws_expiry'];

				$this->thumbnail = $raid['icon_file'];
				if(!file_exists('raid_includes/icons/'.$this->thumbnail) || empty($raid['icon_file']))
					$this->thumbnail = 'default.png';

				$this->attendance = $raid['attendance'] == 1;

				$sql = '
					SELECT a.*
					FROM raider_attendance AS a,'.PROFILE_FIELDS_DATA_TABLE.' AS p
					LEFT JOIN '.USER_GROUP_TABLE.' AS g ON (p.user_id = g.user_id)
					LEFT JOIN '.GROUPS_TABLE.' AS h ON (g.group_id = h.group_id)
					WHERE a.userid = p.user_id AND raidid='.(int)$this->id.' AND h.group_name=\''.RaidController::$config['raider_group'].'\'
					ORDER BY percent DESC,
						p.pf_'.RaidController::$config['class_field'].',
						p.pf_'.RaidController::$config['role_field'].',
						p.pf_'.RaidController::$config['character_field'];

				$resource = $db->sql_query($sql);
				while($data = $db->sql_fetchrow($resource))
				{
					$att = new Attendee($data);
					if($att->Complete())
						$this->raiders[$data['userid']] = $att;
				}
			}
			else
			{
				global $db;
				$resource = $db->sql_query('SELECT MAX(raidstart) as d FROM raid_list');
				$last = $db->sql_fetchrow($resource);
				if(!$last || !$last['d'])
					return;

				$ts = strtotime($last['d']);
				$ts += RaidController::$config['raid_days'][date('w',$ts)] * 86400;

				if($ts <= time())
					return;

				$this->deadline = date('Y-m-d H:i', $ts - RaidController::$config['default_deadline'] * 60);
				$this->raidstart = date('Y-m-d H:i', $ts);
			}
		}

		public function WowWebStats()
		{
			if(!$this->wws_url || (!RaidController::$config['wws_premium'] && strtotime($this->wws_expiry) < time()))
				return false;

			return $this->wws_url;
		}

		public function Signup($userid, $confidence, $comment = null, $overridedeadline = true)
		{
			if(!$overridedeadline && time() > strtotime($this->deadline))
				return;

			global $db;
			$data = array(
				'userid' => $userid,
				'raidid' => $this->id,
				'percent' => $confidence,
				'comment' => $comment,
			);
			if($confidence === null)
			{
				$db->sql_query('DELETE FROM raider_attendance WHERE raidid='.(int)$this->id.' AND userid='.(int)$userid);
			}
			else if(!isset($this->raiders[$userid]))
			{
				if($data['comment'] === null)
					$data['comment'] = '';

				$data['added'] = date('Y-m-d H:i:s');
				$data['added_by'] = RaidController::$UserID;

				$db->sql_query('INSERT INTO raider_attendance '.$db->sql_build_array('INSERT',$data));
			}
			else
			{
				if($data['comment'] === null)
					$data['comment'] = trim($this->raiders[$userid]->comment);
				else
					$data['comment'] = trim($data['comment']);

				if($this->raiders[$userid]->confidence != $data['percent'])
				{
					$data['modified'] = date('Y-m-d H:i:s');
					$data['modified_by'] = RaidController::$UserID;
				}
				unset($data['raidid']);
				unset($data['userid']);
				$db->sql_query('UPDATE raider_attendance SET '.$db->sql_build_array('UPDATE',$data).' WHERE raidid='.(int)$this->id.' AND userid='.(int)$userid);
			}
		}

		public function GetPlayers()
		{
			$players = array();
			foreach($this->raiders as $raider)
				$players[] = new Player($raider['userid']);
			return $players;
		}

		public function Delete()
		{
			global $db;
			$db->sql_query('DELETE FROM raider_attendance WHERE raidid='.(int)$this->id);
			$db->sql_query('DELETE FROM raid_list WHERE id='.(int)$this->id);
		}

		public function Save()
		{
			global $db;
			$data = array(
				'comment' => $this->comment,
				'deadline' => $this->deadline,
				'raidstart' => $this->raidstart,
				'wws_url' => $this->wws_url,
				'wws_expiry' => $this->wws_expiry,
				'icon_file' => $this->thumbnail,
				'attendance' => $this->attendance ? 1 : 0
			);
			if($this->id === null)
			{
				$db->sql_query(
					'INSERT INTO raid_list '.$db->sql_build_array('INSERT',$data)
				);
				$this->id = $db->sql_nextid();
			}
			else
			{
				$db->sql_query(
					'UPDATE raid_list SET '.$db->sql_build_array('UPDATE',$data).' WHERE id='.(int)$this->id
				);
			}
			$absences = Absence::GetListForDay($this->raidstart);
			foreach($absences as $player)
				if($player->raider->Complete())
					$this->Signup($player->userid, 0, 'Absence: '.$player->comment);
		}
	}
?>