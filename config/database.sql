-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************


-- 
-- Table `tl_c4g_forum`
-- 

CREATE TABLE `tl_c4g_forum` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `sorting` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `headline` varchar(255) NOT NULL default '',
  `description` blob NULL,
  `box_imagesrc` binary(16) NULL,  
  `published` char(1) NOT NULL default '',
  `use_intropage` char(1) NOT NULL default '',
  `intropage` text NULL,
  `intropage_forumbtn` varchar(100) NOT NULL default '',  
  `intropage_forumbtn_jqui` char(1) NOT NULL default '1',  
  `pretext` text NULL,
  `posttext` text NULL,
  `threads` int(10) unsigned NOT NULL default '0',
  `posts` int(10) unsigned NOT NULL default '0',
  `last_thread_id` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `define_groups` char(1) NOT NULL default '',
  `member_groups` blob NULL,
  `admin_groups` blob NULL,
  `define_rights` char(1) NOT NULL default '',
  `guest_rights` blob NULL,
  `member_rights` blob NULL,
  `admin_rights` blob NULL,  
  `enable_maps` char(1) NOT NULL default '',
  `enable_maps_inherited` char(1) NOT NULL default '',
  `map_type` char(5) NOT NULL default 'PICK',
  `map_override_locationstyle` char(1) NOT NULL default '',
  `map_override_locstyles` blob NULL,
  `map_id` int(10) unsigned NOT NULL default '0',
  `map_location_label` char(20) NOT NULL default '',
  `map_label` char(5) NOT NULL default 'NONE',
  `map_tooltip` char(5) NOT NULL default 'NONE',
  `map_popup` char(5) NOT NULL default 'NONE',
  `map_link` char(5) NOT NULL default 'NONE',  
  `linkurl` varchar(255) NOT NULL default '',
  `link_newwindow` char(1) NOT NULL default '',
  `sitemap_exclude` char(1) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_c4g_forum_thread`
-- 

CREATE TABLE `tl_c4g_forum_thread` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `sort` int(10) unsigned NOT NULL default '999',  
  `threaddesc` text NULL,  
--  `threaddesc` blob NULL,  
  `author` int(10) NOT NULL default '0',
  `creation` int(10) NOT NULL default '0',
  `posts` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `edit_count` int(10) unsigned NOT NULL default '0',
  `edit_last_author` int(10) unsigned NOT NULL default '0',
  `edit_last_time` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
--  FULLTEXT KEY `threaddesc` (`threaddesc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_c4g_forum_thread_subscription`
-- 

CREATE TABLE `tl_c4g_forum_thread_subscription` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `member` int(10) NOT NULL default '0',  
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `member` (`member`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_c4g_forum_subforum_subscription`
-- 

CREATE TABLE `tl_c4g_forum_subforum_subscription` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `member` int(10) NOT NULL default '0', 
  `thread_only` char(1) NOT NULL default '',  
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
  KEY `member` (`member`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_c4g_forum_post`
-- 

CREATE TABLE `tl_c4g_forum_post` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `text` text NULL,
--  `text` blob NULL,
--  `subject` text NOT NULL default ' ',
  `subject` varchar(100) NOT NULL default '',
  `author` int(10) NOT NULL default '0',
  `creation` int(10) NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `post_number` int(10) unsigned NOT NULL default '0',
  `edit_count` int(10) unsigned NOT NULL default '0',
  `edit_last_author` int(10) unsigned NOT NULL default '0',
  `edit_last_time` int(10) unsigned NOT NULL default '0',  
  `linkname` varchar(100) NOT NULL default '',
  `linkurl` varchar(255) NOT NULL default '',
  `loc_osm_id` varchar(255) NOT NULL default '',  
  `loc_geox` varchar(20) NOT NULL default '',
  `loc_geoy` varchar(20) NOT NULL default '',
  `loc_data_type` char(10) NOT NULL default '',
  `loc_data_content` text NULL,
  `locstyle` int(10) unsigned NOT NULL default '0',  
  `loc_label` varchar(100) NOT NULL default '',
  `loc_tooltip` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`),
--  FULLTEXT KEY `text` (`text`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table `tl_c4g_forum_search_word`
--

CREATE TABLE `tl_c4g_forum_search_word` (
  `sw_id` int(10) unsigned NOT NULL auto_increment,
  `sw_word` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`sw_id`),
  UNIQUE KEY `un_sw_word` (`sw_word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table `tl_c4g_forum_search_index`
--

CREATE TABLE `tl_c4g_forum_search_index` (
  `si_id` int(10) unsigned NOT NULL auto_increment,
  `si_sw_id` int(10) unsigned NOT NULL default '0',
  `si_type` varchar(10) NOT NULL default 'threadhl',
  `si_dest_id` int(10) NOT NULL default '0',
  `si_count` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`si_id`),
  KEY `fk_si_sw_id` (`si_sw_id`),
  UNIQUE KEY `un_si_sw_id` (`si_sw_id`, `si_type`, `si_dest_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


--
-- Table `tl_c4g_forum_search_last_index`
--

CREATE TABLE `tl_c4g_forum_search_last_index` (
  `id` int(5) unsigned NOT NULL default '1',
  `first` int(10) NOT NULL default '0',
  `last_total_renew` int(10) NOT NULL default '0',
  `last_index` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`),
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- 
-- Table `tl_module`
-- 

CREATE TABLE `tl_module` (
  `c4g_forum_size` varchar(255) NOT NULL default '',
  `c4g_forum_scroll` varchar(255) NOT NULL default '',
  `c4g_forum_startforum` int(10) unsigned NOT NULL default '0',
  `c4g_forum_comf_navigation` varchar(10) NOT NULL default 'BOXES',  
  `c4g_forum_threadclick` char(6) NOT NULL default 'THREAD',  
  `c4g_forum_show_realname` char(2) NOT NULL default 'UU',
  `c4g_forum_postsort` char(2) NOT NULL default 'UP',  
  `c4g_forum_collapsible_posts` char(2) NOT NULL default 'NC',
  `c4g_forum_breadcrumb` char(1) NOT NULL default '1',  
  `c4g_forum_hide_intropages` char(1) NOT NULL default '',
  `c4g_forum_jumpTo` int(10) unsigned NOT NULL default '0',
  `c4g_forum_language` char(5) NOT NULL default '',  
  `c4g_forum_bbcodes` char(1) NOT NULL default '1',  
  `c4g_forum_bbcodes_editor_imguploadpath` char(128) NOT NULL default '',  
  `c4g_forum_bbcodes_smileys` char(1) NOT NULL default '1', 
  `c4g_forum_bbcodes_smileys_url` char(128) NOT NULL default 'system/modules/con4gis_core/lib/wswgEditor/images/smilies',  
  `c4g_forum_bbcodes_autourl` char(1) NOT NULL default '1', 
  `c4g_forum_bbcodes_editor` char(1) NOT NULL default '0',
  `c4g_forum_boxes_text` char(1) NOT NULL default '1',  
  `c4g_forum_boxes_subtext` char(1) NOT NULL default '1',  
  `c4g_forum_boxes_lastpost` char(1) NOT NULL default '1',  
  `c4g_forum_boxes_center` char(1) NOT NULL default '',  
  `c4g_forum_jqui` char(1) NOT NULL default '1',  
  `c4g_forum_jqui_lib` char(1) NOT NULL default '1',  
  `c4g_forum_uitheme_css_src` binary(16) NULL,  
  `c4g_forum_dialogsize` varchar(255) NOT NULL default '',
  `c4g_forum_dialogs_embedded` char(1) NOT NULL default '1',  
  `c4g_forum_embdialogs_jqui` char(1) NOT NULL default '1',  
  `c4g_forum_breadcrumb_jqui_layout` char(1) NOT NULL default '1',  
  `c4g_forum_buttons_jqui_layout` char(1) NOT NULL default '1',  
  `c4g_forum_table_jqui_layout` char(1) NOT NULL default '1',  
  `c4g_forum_posts_jqui` char(1) NOT NULL default '1',  
  `c4g_forum_boxes_jqui_layout` char(1) NOT NULL default '1',  
  `c4g_forum_jquery_lib` char(1) NOT NULL default '1',  
  `c4g_forum_jqtable_lib` char(1) NOT NULL default '1',  
  `c4g_forum_jqhistory_lib` char(1) NOT NULL default '1',  
  `c4g_forum_jqtooltip_lib` char(1) NOT NULL default '1',
  `c4g_forum_jqscrollpane_lib` char(1) NOT NULL default '',
  `c4g_forum_enable_maps` char(1) NOT NULL default '',  
  `c4g_forum_sitemap` char(1) NOT NULL default '',  
  `c4g_forum_sitemap_filename` varchar(30) NOT NULL default '',  
  `c4g_forum_sitemap_contents` blob NULL,  
  `c4g_forum_sitemap_updated` int(10) NOT NULL default '0',
  `c4g_forum_breadcrumb_jumpTo` int(10) unsigned NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
