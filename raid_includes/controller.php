<?php
	interface RaidControllerModule
	{
		public function ProcessPost($data);
		public function __tostring();
	}

	function TimeLeft($ts)
	{
		if(!is_numeric($ts))
			$ts = strtotime($ts);
		$time = $ts - time();
		$second = $time % 60;
		$minute = floor($time / 60) % 60;
		$hour = floor($time / 3600) % 24;
		$day = floor($time / 86400);

		if($day > 0)
			return sprintf('%dd:%dh', $day, $hour);
		if($hour > 0)
			return sprintf('%dh:%dm', $hour, $minute);
		
		return sprintf('%dm:%ds', $minute, $second);
	}

	function FormatDate($ts, $format = ' %H:%M %A %B %e')
	{
		if(!is_numeric($ts))
		{
			$date = date_parse($ts);
			$ts = strtotime($ts) - ($date['zone'] * 60);
		}
		return strftime($format, $ts + RaidController::$TimeOffset);
	}

	function Timestamp($date)
	{
		return strtotime($date) - RaidController::$TimeOffset;
	}

	class RaidController
	{
		public static $Module;
		public static $config = array();
		public static $Access = 0;
		public static $TimeOffset = 0;
		public static $UserID = 0;
		private static $Modules = array();

		public static function RegisterModule($module)
		{
			if($module instanceof RaidControllerModule)
				self::$Modules[$module->name] = $module;
		}

		public static function GetAccessLevel($userid)
		{
			global $db;
			$result = $db->sql_query(
				'SELECT group_name FROM '.USER_GROUP_TABLE.' AS u LEFT JOIN '.GROUPS_TABLE.' AS g ON (u.group_id = g.group_id) WHERE u.user_id='.(int)$userid
			);
			$level = 0;
			while($group = $db->sql_fetchrow($result))
			{
				if($group['group_name'] == self::$config['admin_group'] && $level < 3)
					$level = 3;

				if($group['group_name'] == self::$config['raider_group'] && $level < 2)
					$level = 2;

				if($group['group_name'] == self::$config['guest_group'] && $level < 1)
					$level = 1;
			}
			return $level;
		}

		public static function LoadModules()
		{
			$modules = glob(dirname(__FILE__) . '/modules/*.php');
			foreach($modules as $module)
				require_once($module);
		}

		public static $templatedir;
		public static $error_title = false;
		public static $error_msg = false;
		public static $year;
		public static $month;
		public static $timestamp;
		public static $selected;
		public static $CurrentUser;

		public static function SelectDate($year = false, $month = false, $day = false)
		{
			if($year)
				self::$year = $year;
			
			if($month)
				self::$month = $month;
			
			self::$timestamp = strtotime(self::$year.'-'.self::$month.'-01 12:00');

			if($day)
				self::$selected = array(
					'y' => self::$year,
					'm' => self::$month,
					'd' => $day
				);
			else
				self::$selected = false;
		}

		public static function Attach($template, $userid)
		{
			self::$templatedir = dirname(__FILE__) . '/templates/';
			self::$UserID = $userid;
			self::$Access = self::GetAccessLevel($userid);
			
			// Calendar interaction logic
			self::SelectDate(
				isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y'),
				isset($_GET['month']) ? (int)$_GET['month'] : (int)date('m'),
				isset($_GET['day']) ? (int)$_GET['day'] : false
			);

			// Check that the user has the proper access rights
			if(self::$Access < 1)
			{
				$groups = array();
				if(self::$config['admin_group'] !== false)
					$groups[] = self::$config['admin_group'];
				
				if(self::$config['raider_group'] !== false)
					$groups[] = self::$config['raider_group'];

				if(self::$config['guest_group'] !== false)
					$groups[] = self::$config['guest_group'];

				self::$error_title = 'Access denied';
				
				if(count($groups) == 0)
					self::$error_msg = 'You do not have the necessary privileges to see this page';
				
				else if(count($groups) == 1)
					self::$error_msg = 'To access this system, you must be a member of the "'.$groups[0].'" group.';
				
				else if(count($groups) == 2)
					self::$error_msg = 'To access this system, you must be a member of either the "'.$groups[0].'" or "'.$groups[1].'" group.';

				else if(count($groups) == 3)
					self::$error_msg = 'To access this system, you must be a member of one of the "'.$groups[0].'", "'.$groups[1].'" or "'.$groups[2].'" groups.';
			}

			// Check that the user has completed his profile
			if(!self::$error_msg)
			{
				self::$CurrentUser = new Player($userid);
			
				if(!self::$CurrentUser->Complete())
				{
					self::$error_title = 'Incomplete user profile';
					self::$error_msg = 'You cannot access the raid signups before you have filled in your characters name, class and role under your profile <a href="./ucp.php">in the User Control Panel</a>';
				}
			}

			$action = isset($_GET['action']) ? $_GET['action'] : 'raidlist';
			if(isset($_POST['action']))
				$action = $_POST['action'];

 			self::$Module = $action;
			self::LoadModules();

			// Verify selected module is valid
			if(!isset(self::$Modules[$action]))
			{
				self::$error_title = 'Unknown module "'.$action.'"';
				self::$error_msg = 'You have reach a page that does not exist, please notify the site administrator of how this happened.';
			}

			// Verify that the user is allowed to access the selected module
			if(self::$Modules[$action]->required_level > self::$Access)
			{
				self::$error_title = 'Access denied';
				self::$error_msg = 'You do not have the necessary privileges to see this page';
			}

			// Display any error
			if(self::$error_msg)
			{
				$template->assign_vars(array(
					'MESSAGE_TITLE' => self::$error_title,
					'MESSAGE_TEXT' => self::$error_msg
				));
				$template->set_filenames(array(
					'body' => 'message_body.html')
				);
			}

			// Display the module
			else
			{
				if($_SERVER['REQUEST_METHOD'] == 'POST')
				{
					$redirect = true;
					if(self::$Modules[$action]->required_post <= self::$Access)
						$redirect = !self::$Modules[$action]->ProcessPost($_POST);

					if($redirect)
						redirect($_SERVER['REQUEST_URI']);
				}
				$content = (string)self::$Modules[$action];

				ob_start();
				require(self::$templatedir.'raidhead.php');
				$content = ob_get_clean() . $content;

				$template->assign_vars(array(
					'CONTROLLER' => $content
				));
				$template->set_filenames(array(
					'body' => 'raid_body.html'
				));
			}
		}
	}
?>