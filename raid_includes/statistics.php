<?php
	class PlayerStatistics
	{
		private $year;
		private $month;
		public $sliding;
		public $total;
		public $raider;

		private function GetLimiter($prefix = '')
		{
			if($prefix != '')
				$prefix .= '.';
			if($this->sliding)
			{
				$start = strtotime('-2 months');
				return $prefix.'raidstart > \''.date('Y-m-d H:i:s',$start).'\' AND '.$prefix.'raidstart < NOW()';
			}
			else
				return 'YEAR('.$prefix.'raidstart)='.$this->year.' AND MONTH('.$prefix.'raidstart)='.$this->month.' AND '.$prefix.'raidstart < NOW()';
		}

		public function __construct($userid, $year, $month, $current = false)
		{
			$this->year = (int)$year;
			$this->month = (int)$month;
			$this->sliding = $current;
			$this->raider = new Player($userid);

			global $db;
			$resource = $db->sql_query('
				SELECT COUNT(id) AS c
				FROM raid_list
				WHERE attendance=1 AND '.$this->GetLimiter()
			);
			$cnt = $db->sql_fetchrow($resource);
			if($cnt)
				$this->total = $cnt['c'];
			else
				$this->total = 0;
		}

		public function GetAttendanceData()
		{
			global $db;
			$stats = array();
			$resource = $db->sql_query('
				SELECT id, raidstart, percent, raider_attendance.comment, attended, sitout
				FROM raid_list
				LEFT JOIN raider_attendance ON (raid_list.id = raider_attendance.raidid AND userid='.$this->raider->userid.')
				WHERE attendance=1 AND '.$this->GetLimiter().'
				ORDER BY raidstart'
			);
			if($resource)
			{
				while($row = $db->sql_fetchrow($resource))
					$stats[] = $row;
			}
			return $stats;
		}
	
		public function GetAttendanceHistory()
		{
			global $db;
			$stats = array();
			$resource = $db->sql_query('
				SELECT r.id, r.raidstart, 
					(SELECT percent FROM raider_attendance WHERE raidid=r.id AND userid='.$this->raider->userid.') AS signup, 
					SUM(IF(attended IS NULL,0,attended)) / COUNT(b.id) AS attendance
				FROM raid_list AS r
				INNER JOIN raid_list AS b ON (b.attendance=1 AND b.raidstart <= r.raidstart AND DATEDIFF(r.raidstart,b.raidstart) < 60)
				LEFT JOIN raider_attendance ON (b.id = raider_attendance.raidid AND raider_attendance.userid='.$this->raider->userid.')
				WHERE r.attendance=1 AND '.$this->GetLimiter('r').'
				GROUP BY r.id, r.raidstart
				ORDER BY r.raidstart'
			);
			if($resource)
				while($row = $db->sql_fetchrow($resource))
					$stats[] = array(
						'attendance' => (float)$row['attendance'],
						'signup' => (int)$row['signup']
					);
			return $stats;
		}
	}

	class Statistics
	{
		private $year;
		private $month;
		public $sliding;
		public $total;
		public $hMon = false;
		public $hTue = false;
		public $hWed = false;
		public $hThu = false;
		public $hFri = false;
		public $hSat = false;
		public $hSun = false;

		private function GetLimiter()
		{
			if($this->sliding)
			{
				$end = strtotime(date('Y-m-d', mktime(0,0,0)));
				$start = strtotime('-2 months', $end);
				return 'raidstart > \''.date('Y-m-d H:i:s',$start).'\' AND raidstart < \''.date('Y-m-d H:i:s',$end).'\'';
			}
			else
				return 'YEAR(raidstart)='.$this->year.' AND MONTH(raidstart)='.$this->month.' AND raidstart < NOW()';
		}

		public function __construct($year, $month, $current = false)
		{
			$this->year = (int)$year;
			$this->month = (int)$month;
			$this->sliding = $current;
			global $db;
			$resource = $db->sql_query('
				SELECT COUNT(id) AS c
				FROM raid_list
				WHERE attendance=1 AND '.$this->GetLimiter()
			);
			$cnt = $db->sql_fetchrow($resource);
			if($cnt)
				$this->total = $cnt['c'];
			else
				$this->total = 0;
		}

		public function GetStats()
		{
			global $db;
			$stats = array();
			$resource = $db->sql_query('
				SELECT p.user_id, 
					AVG(percent/100) as confidence, 
					COUNT(raid_list.id) AS c, 
					COUNT(IF(percent=0,NULL,raidid)) / COUNT(raid_list.id) AS stp,
					SUM(attended) / COUNT(raid_list.id) AS atp,
					SUM(sitout) / COUNT(raid_list.id) AS sotp,
					SUM(IF(percent=0,0,sitout)) AS sitouts,
					SUM(IF(percent=0,0,attended)) AS attendance,
					COUNT(IF(percent=0,NULL,raidid)) AS signups,
					SUM(IF(percent=0,1,0)) AS s0, 
					SUM(IF(percent=50,1,0)) AS s50, 
					SUM(IF(percent=100,1,0)) AS s100, 
					SUM(IF(percent=0,attended,0)) AS a0, 
					SUM(IF(percent=50,attended,0)) AS a50, 
					SUM(IF(percent=100,attended,0)) AS a100, 
					SUM(IF(percent=0,sitout,0)) AS sb0, 
					SUM(IF(percent=0,sitout,0)) AS sb50, 
					SUM(IF(percent=0,sitout,0)) AS sb100,
					SUM(IF(DAYOFWEEK(raidstart)=1,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=1 THEN raid_list.id END) AS aSun,
					SUM(IF(DAYOFWEEK(raidstart)=2,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=2 THEN raid_list.id END) AS aMon,
					SUM(IF(DAYOFWEEK(raidstart)=3,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=3 THEN raid_list.id END) AS aTue,
					SUM(IF(DAYOFWEEK(raidstart)=4,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=4 THEN raid_list.id END) AS aWed,
					SUM(IF(DAYOFWEEK(raidstart)=5,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=5 THEN raid_list.id END) AS aThu,
					SUM(IF(DAYOFWEEK(raidstart)=6,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=6 THEN raid_list.id END) AS aFri,
					SUM(IF(DAYOFWEEK(raidstart)=7,attended,0)) / COUNT(CASE WHEN DAYOFWEEK(raidstart)=7 THEN raid_list.id END) AS aSat,

					COUNT(CASE WHEN DAYOFWEEK(raidstart)=1 THEN raid_list.id END) AS hSun,
					COUNT(CASE WHEN DAYOFWEEK(raidstart)=2 THEN raid_list.id END) AS hMon,
					COUNT(CASE WHEN DAYOFWEEK(raidstart)=3 THEN raid_list.id END) AS hTue,
					COUNT(CASE WHEN DAYOFWEEK(raidstart)=4 THEN raid_list.id END) AS hWed,
					COUNT(CASE WHEN DAYOFWEEK(raidstart)=5 THEN raid_list.id END) AS hThu,
					COUNT(CASE WHEN DAYOFWEEK(raidstart)=6 THEN raid_list.id END) AS hFri,
					COUNT(CASE WHEN DAYOFWEEK(raidstart)=7 THEN raid_list.id END) AS hSat

				FROM '.PROFILE_FIELDS_DATA_TABLE.' AS p
				INNER JOIN raid_list
				LEFT JOIN raider_attendance AS a ON (raid_list.id = a.raidid AND p.user_id = a.userid) 
				LEFT JOIN '.USER_GROUP_TABLE.' AS g ON (p.user_id = g.user_id)
				LEFT JOIN '.GROUPS_TABLE.' AS h ON (g.group_id = h.group_id)
				WHERE attendance=1 AND '.$this->GetLimiter().' AND h.group_name=\''.RaidController::$config['raider_group'].'\'
				GROUP BY user_id
				HAVING confidence IS NOT NULL
				ORDER BY p.pf_'.RaidController::$config['class_field'].', p.pf_'.RaidController::$config['character_field']
			);
			if($resource)
				while($stat = $db->sql_fetchrow($resource))
				{
					$stats[] = new RaiderStats($stat);
					$this->hMon = $this->hMon || $stat['hMon'];
					$this->hTue = $this->hTue || $stat['hTue'];
					$this->hWed = $this->hWed || $stat['hWed'];
					$this->hThu = $this->hThu || $stat['hThu'];
					$this->hFri = $this->hFri || $stat['hFri'];
					$this->hSat = $this->hSat || $stat['hSat'];
					$this->hSun = $this->hSun || $stat['hSun'];
				}
			return $stats;
		}

		public function SitOuts()
		{
			global $db;
			$resource = $db->sql_query('
				SELECT a.userid, SUM(sitout) AS c
				FROM '.PROFILE_FIELDS_DATA_TABLE.' AS p
				LEFT JOIN raider_attendance AS a ON (a.userid = p.user_id)
				LEFT JOIN raid_list ON (raid_list.id = a.raidid)
				LEFT JOIN '.USER_GROUP_TABLE.' AS g ON (p.user_id = g.user_id)
				LEFT JOIN '.GROUPS_TABLE.' AS h ON (g.group_id = h.group_id)
				WHERE attendance=1 AND '.$this->GetLimiter().' AND h.group_name=\''.RaidController::$config['raider_group'].'\'
				GROUP BY userid
				ORDER BY p.pf_'.RaidController::$config['role_field'].', p.pf_'.RaidController::$config['class_field'].', c DESC, p.pf_'.RaidController::$config['character_field']
			);
			$statlist = array();
			if($resource)
			{
				while($stat = $db->sql_fetchrow($resource))
				{
					$statlist[] = new Sitout($stat);
				}
			}
			return $statlist;
		}
		
	}

	class Sitout
	{
		public $raider;
		public $sitouts;

		public function __construct($data)
		{
			$this->raider = new Player($data['userid']);
			$this->sitouts = $data['c'];
		}
	}

	class RaiderStats
	{
		private $data;
		public $raider;

		public function __construct($data)
		{
			$this->data = $data;
			$this->raider = new Player($data['user_id']);
		}

		public function __get($key)
		{
			return $this->data[$key];
		}
	}
?>