<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

/**
 * Global settings
 */
$GLOBALS['con4gis_forum_extension']['installed']    = true;
$GLOBALS['con4gis_forum_extension']['version']      = '2.0.4-snapshot';

/**
 * Frontend modules
 */
$GLOBALS['FE_MOD']['con4gis']['c4g_forum'] = 'C4GForum';
$GLOBALS['FE_MOD']['con4gis']['c4g_forum_breadcrumb'] = 'C4GForumBreadcrumb';
$GLOBALS['FE_MOD']['con4gis']['c4g_forum_pncenter'] = 'C4GForumPNCenter';

/**
 * Backend Modules
 */
array_insert( $GLOBALS['BE_MOD']['con4gis'], 5, array
(
    'c4g_forum' => array
    (
		'tables' 		=> array('tl_c4g_forum'),
		'build_index' 	=> array('C4GForumBackend', 'buildIndex'),
 		'icon'	 		=> 'system/modules/con4gis_forum/assets/forumicon.png'
	)
));

/**
 * Add frontend form field for memberImage (Avatar)
 */
$GLOBALS['TL_FFL']['avatar'] = 'Avatar';

/**
 * Add backend form field for memberImage (Avatar)
 */
$GLOBALS['BE_FFL']['avatar'] = 'Avatar';

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('C4GForumHelper','removeOldFeedsHook');

/**
 * Rest-API
 */
$GLOBALS['TL_API']['c4g_forum_ajax'] 		= 'C4gForumAjaxApi';
$GLOBALS['TL_API']['c4g_forum_pn_api'] 		= 'C4gForumPnApi';