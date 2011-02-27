<?php
	// Standard phpBB3 Boilerplate
	define('IN_PHPBB', true);
	$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
	$phpEx = 'php';
	include($phpbb_root_path . 'common.' . $phpEx);

	$user->session_begin();
	$auth->acl($user->data);
	$user->setup();

	// Do not allow anonymous access to this page
	if($user->data['user_id'] == ANONYMOUS || $user->data['is_bot'])
		login_box('', $user->lang['LOGIN_EXPLAIN_MCP']);

	// Our own includes
	require('raid_includes/controller.php');
	require('raid_includes/player.php');
	require('raid_includes/classdata.php');
	require('raid_includes/raid.php');
	require('raid_includes/statistics.php');
	require('raid_includes/absence.php');

	RaidController::$config = array(

		// Names of the profile fields set up to hold the raiders' data
		'character_field' => 'main_char_name',
		'role_field'      => 'role',
		'class_field'     => 'class',

		// Group IDs that map onto access in the system
	
		// Raid administration (new,manage)
		'admin_group' => 'Guild Councillor/Captain',

		// Sign up for raids
		'raider_group' => 'Guild Members',
	
		// View raid status (false to disable)
		'guest_group' => false,

		// Set to true if WowWebStats are hosted on a premium account (they never expire)
		'wws_premium' => false,

		// This one is a little cryptic, sorry about that..
		// this array says how many days until the next raid for a given day
		// The first number is Sunday, then Monday etc. This is used for raid autogeneration
		// 2,1,2,1,3,2,1 would map to raids every tuesday, thursday and sunday.
		'raid_days' => array(2,1,2,1,3,2,1),

		// How many minutes before raid start, by default, are signups closed?
		'default_deadline' => 30,

		'armory_url' => 'http://eu.battle.net/wow/en/character/aszune/%1$s/simple',

		// These two arrays map the profile data onto the internal format of the system
		// The first two need to be blank, the remainder must be in the order specified in the profile management
		'rolemap'  => array('','','dps','tank','healer'),
		'classmap' => array('','','dknight','druid','hunter','mage','paladin','priest','rogue','shaman','warlock','warrior'),
	);

	// Local time offset, for use in date boxes
	RaidController::$TimeOffset = $user->timezone + $user->dst - date('Z');

	RaidController::Attach($template, $user->data['user_id']);

	// And display the page..
	page_header('Raid signup');
	make_jumpbox(append_sid($phpbb_root_path.'viewforum.'.$phpEx));
	page_footer();
?>