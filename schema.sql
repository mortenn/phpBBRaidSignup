SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- --------------------------------------------------------

--
-- Table structure for table `raider_attendance`
--

CREATE TABLE IF NOT EXISTS `raider_attendance` (
  `userid` bigint(20) NOT NULL,
  `raidid` bigint(20) NOT NULL,
  `percent` smallint(6) NOT NULL,
  `comment` varchar(500) NOT NULL,
  `attended` tinyint(4) NOT NULL default '0',
  `sitout` tinyint(4) NOT NULL default '0',
  `added` datetime default NULL,
  `modified` datetime default NULL,
  `added_by` bigint(20) default NULL,
  `modified_by` bigint(20) default NULL,
  PRIMARY KEY  (`userid`,`raidid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `raider_usermap`
--

CREATE TABLE IF NOT EXISTS `raider_usermap` (
  `userid` bigint(20) NOT NULL,
  `admincomment` varchar(500) NOT NULL,
  `accesslevel` smallint(6) NOT NULL,
  `banned` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `raider_vacation`
--

CREATE TABLE IF NOT EXISTS `raider_vacation` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` bigint(20) NOT NULL,
  `v_starting` date NOT NULL,
  `v_ending` date NOT NULL,
  `comment` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `raid_list`
--

CREATE TABLE IF NOT EXISTS `raid_list` (
  `id` bigint(8) unsigned NOT NULL auto_increment,
  `comment` varchar(500) NOT NULL,
  `deadline` datetime NOT NULL,
  `raidstart` datetime NOT NULL,
  `wws_url` varchar(100) default NULL,
  `wws_expiry` date default NULL,
  `icon_file` varchar(20) default NULL,
  `attendance` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=355 ;
