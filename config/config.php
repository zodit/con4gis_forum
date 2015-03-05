<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @version   php 5
 * @package   con4gis
 * @author     Jürgen Witte <http://www.kuestenschmiede.de>
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2014 - 2015
 * @link      https://www.kuestenschmiede.de
 * @filesource
 */



/**
 * Global settings
 */
$GLOBALS['con4gis_forum_extension']['installed']    = true;
$GLOBALS['con4gis_forum_extension']['version']      = '1.1.1';

/**
 * Frontend modules
 */
array_insert( $GLOBALS['FE_MOD']['con4gis'], 6, array
(
	'c4g_forum' 				=> 'Module_c4g_forum',
	'c4g_forum_breadcrumb' 		=> 'Module_c4g_forum_breadcrumb',
)
);

/**
 * Backend Modules
 */
array_insert( $GLOBALS['BE_MOD']['con4gis'], 5, array
(
    'c4g_forum' => array
    (
		'tables' 		=> array('tl_c4g_forum'),
		'build_index' 	=> array('C4GForumBackend', 'buildIndex'),
 		'icon'	 		=> 'system/modules/con4gis_forum/html/forumicon.png'
	)
));

/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['removeOldFeeds'][] = array('C4GForumHelper','removeOldFeedsHook');

/**
 * Rest-API
 */
$GLOBALS['TL_API']['c4g_forum_ajax'] 		= 'C4gForumAjaxApi';
