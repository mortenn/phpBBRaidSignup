<?php
	class RaidManagement implements RaidControllerModule
	{
		public $name = 'manage';
		public $label = 'Management';
		public $required_level = 3;
		public $required_post = 3;

		public $raidcheck = array('notsigned' => array(),'signedout' => array(),'noshowup' => array());

		public function ProcessPost($data)
		{
			if(isset($data['raidid']) && $data['raidid'])
				$raid = new Raid((int)$data['raidid']);

			if($raid)
			{
				if(isset($data['__import']))
				{
					$raiders = explode(',',$data['raidgroup']);
					$raidmem = array();
					foreach($raiders as $raider)
					{
						$tmp = explode(':', $raider);
						if(count($tmp) == 2)
							$raidmem[strtolower($tmp[1])] = (int)$tmp[0] > 5;
					}

					if(isset($data['__check_signup']))
					{
						foreach($raidmem as $char => $sitout)
						{
							$found = false;
							foreach($raid->raiders as $att)
							{
								if(strtolower($att->character) == $char)
								{
									$found = true;
									if($att->confidence === 0)
										$this->raidcheck['signedout'][] = $char;
									break;
								}
							}
							if(!$found)
								$this->raidcheck['notsigned'][] = $char;
						}
						foreach($raid->raiders as $att)
							if($att->confidence == 100 && !isset($raidmem[strtolower($att->character)]))
								$this->raidcheck['noshowup'][] = $att->character;
						return true;
					}
					else if(isset($data['__record_attendance']))
					{
						foreach($raidmem as $char => $sitout)
						{
							$found = false;
							foreach($raid->raiders as $att)
							{
								if(strtolower($att->character) == strtolower($char))
								{
									$found = true;
									$att->SetAttendance(true);
									$att->SetSitout($sitout);
								}
							}
						}
					}
					return false;
				}

				if(isset($data['delete_raid']))
				{
					$raid->Delete();
					redirect('./raid.php');
				}
				$raid->raidstart = date('Y-m-d H:i:s', Timestamp($data['raidstart']));
				$raid->deadline = date('Y-m-d H:i:s', Timestamp($data['deadline']));
				$raid->comment = trim($data['comment']);
				$raid->thumbnail = $data['thumbnail'];
				$wws = trim($data['wws_url']);
				if((empty($raid->wws_url) && !empty($wws)) || $raid->wws_url != $wws)
					$raid->wws_expiry = date('Y-m-d H:i:s', strtotime('+15 days'));
				$raid->wws_url = trim($wws);
				$raid->attendance = isset($data['attendance']);
				$raid->Save();
				if(isset($data['confidence']))
					foreach($data['confidence'] as $userid => $confidence)
					{
						if($confidence < 0)
							$confidence = null;
						$raid->Signup($userid, $confidence, trim($data['usercomment'][$userid]), true);
						$raid->raiders[$userid]->Update(array('admincomment' => $data['admincomment'][$userid]));
					}

				foreach($raid->raiders as $raider)
				{
					$raider->SetAttendance(isset($data['attended']) && isset($data['attended'][$raider->userid]));
					$raider->SetSitout(isset($data['sitout']) && isset($data['sitout'][$raider->userid]));
				}

				if(isset($data['add_player']) && (int)$data['add_player'])
					$raid->Signup($data['add_player'], 100, 'Added by manager', true);
			}
		}

		public function __tostring()
		{
			ob_start();
			try
			{
			$raid = false;
			if(!isset($_GET['raid']))
			{
				$raids = Raid::GetByDay(
					RaidController::$selected['y'], 
					RaidController::$selected['m'], 
					RaidController::$selected['d']
				);
				if(count($raids) == 1)
					$raid = $raids[0];
			}
			else
			{
				$raid = new Raid((int)$_GET['raid']);
				if($raid->id != $_GET['raid'])
					$raid = false;
			}
			if($raid)
			{
				$ts = strtotime($raid->raidstart);
				RaidController::SelectDate(date('Y', $ts),date('m', $ts),date('d', $ts));
			}		
			require(RaidController::$templatedir.'raidmanage.php');
			}
			catch(Exception $e)
			{
				var_dump($e);
			}
			return ob_get_clean();
		}
	}
	RaidController::RegisterModule(new RaidManagement());
?>