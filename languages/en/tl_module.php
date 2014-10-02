<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @version   php 5
 * @package   con4gis
 * @author     Jürgen Witte <http://www.kuestenschmiede.de>
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2014
 * @link      https://www.kuestenschmiede.de
 * @filesource 
 */




/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_size'] 				= array('Size (width, height)', 
																		'Size of division (DIV) wherein the forum is displayed. The size is calculated automatically when you don\'t enter values here.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_scroll'] 			= array('Size of the scrollable area of the threadlist (width, height)', 
																		'Leave empty if you don\'t want scrollbars.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_startforum'] 		= array('Origin',
																		'Choose the parent forum to start from. Leave empty to see all defined forums.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_navigation'] 	= array('Navigation', 
																		'Choose the navigation for the forum.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_threadclick'] 		= array('Thread click action',
																		'Choose the action to be performed when a thread is clicked.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_show_realname'] 	= array('Use real-names instead of usernames',
																		'Choose if and how you want to display the real-name of the users, instead of their usernames');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_postsort'] 			= array('Post order',
																		'Choose the order of the posts in the post list of a thread.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_collapsible_posts'] = array('Make posts collapsible',
																		'Choose if and how you want the posts to be collapsible.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb'] 		= array('Show breadcrumb',
																		'Check this if you want the breadcrumb to be displayed.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_hide_intropages'] 	= array('Hide intropages',
																		'Check this to hide intropages despite they have been defined. This can make sense if you want to realise different views on your forum with several frontend modules.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jumpTo'] 			= array('Redirect page on denied permission', 
																		'Please choose the page to which visitors will be redirected when the permission for a requested action is not granted.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_language'] 			= array('Frontend-Language',
																		'Empty=determine automatically, de=German, en=English.');

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes'] 			= array('Use BBCodes (BETA)',
																		'Deactivate this checkbox, if you do not want to use BBCodes in your forum. Please take note, that deactivating BBCodes after they have already been used, may cause ugly formating-errors.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor'] 	= array('Use WYSIWYG-Editor',
																		'CAUTION: This Feature only works for embedded forums!');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_imguploadpath'] 	= array('Image Upload-Folder',
																					'Decide where uploaded images should be stored');
// $GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_smileys'] = array('Use Smileys','This Feature converts ASCII-smilies into pictures.');
// $GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_smileys_url'] = array('Path to Smiley-Icons','eg = system/modules/con4gis_forum/html/images/smileys');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_autourl'] 	= array('Automaticaly recognize URLs',
																		'This Feature automatically recognites typed URLs and converts them into functional links.');

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_text'] 		= array('Box navigation: display forum name',
																		'Check this to show the forum name in the box navigation.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_subtext'] 	= array('Box navigation: display details',
																		'Check this to show the number of child forums, number of threads and number of posts in the box navigation.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_lastpost'] 	= array('Box navigation: display last post information',
																		'Check this to display information regarding the last post in the forum.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_center'] 		= array('Center box navigation',
																		'Check this to center the block containing the boxes.');

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqui'] 				= array('Use jQuery UI', 
																		'Uncheck this to deactivate jQuery UI completely. The library is not loaded and all jQuery UI dependent functionality is deactivated!');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqui_lib'] 			= array('Load jQuery UI library',
																		'Uncheck this if you are already loading the jQuery UI library by yourself: please check that you use a compatible version of the library!');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_uitheme_css_src'] 	= array('jQuery UI ThemeRoller CSS file',
																		'Optionally: select the CSS file you created with the jQuery UI ThemeRoller.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_dialogsize'] 		= array('Size of dialogs (width, height)', 
																		'Leave empty to use default values. Has no meaning if you use embedded dialogs.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_buttons_jqui_layout'] 	= array('Use jQuery UI Layout for the toolbar buttons',
																			'Check this to use jQuery-UI Buttons, otherwise links are created.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb_jqui_layout'] 	= array('Use jQuery UI Layout for the breadcrumb buttons',
																				'Check this to use jQuery-UI Buttons, otherwise links are created.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_table_jqui_layout'] = array('Use jQuery UI Layout for threadlist',
																		'Check this to use jQuery-UI layout for the threadlist.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_dialogs_embedded'] 	= array('Embedded dialogs',
																		'Check this if you want dialogs to be embedded into the page rather than flowing around.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_embdialogs_jqui'] 	= array('Use jQuery UI Layout for embedded dialogs',
																		'Check this to use jQuery-UI layout for the embedded dialogs. ');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_posts_jqui'] 		= array('Use jQuery UI Layout for posts',
																		'Check this to use jQuery-UI layout for displaying the posts.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_jqui_layout'] = array('Use jQuery UI Layout for box navigation',
																		'Check this to use jQuery-UI CSS-classes to style the navigation boxes.');

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jquery_lib'] 		= array('Load jQuery library',
																		'Check this if you are already loading jQuery by yourself. Attention: Make sure a compatible version is loaded!');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqtable_lib'] 		= array('Load jQuery DataTables library',
																		'Uncheck this if you don\'t want jQuery DataTables to be loaded! Attention: you can\'t use the threadlist if jQuery DataTables is not available!');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqhistory_lib'] 	= array('Load jQuery History library',
																		'Uncheck this, if you don\'t want to use jQuery History.js functionality. Attention: unchecking this means that the backbutton doesn\'t work inside the forum. Also the browser URL field is not updated while using forum functionality, so there is no easy link functionality to forums, threads and posts.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqtooltip_lib'] 	= array('Load jQuery Tooltip library',
																		'Uncheck this to deactivate jQuery Tooltip functionality.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqscrollpane_lib'] 	= array('Load jScrollPane library',
																		'Check this if you want to use styleable scrollbars in jQuery UI dialogs.');

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_enable_maps'] 		= array('Enable maps (requires con4gis-Maps)',
																		'Check this to activate map functionality in general. Note that you also have to configure map functionality in the forum maintenance. Requires the Contao extension \'con4gis-Maps\' to be installed! ');

if (version_compare(VERSION,'3','<')) {
	$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap'] 		= array('Create an XML sitemap',
																		'Create a Google XML sitemap in the root directory.');
} else {
	$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap'] 		= array('Create an XML sitemap',
																		'Create a Google XML sitemap in the directory "share/".');	
}	
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap_filename'] 	= array('Sitemap file name',
																		'Enter the name of the sitemap file without extension .xml.');
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap_contents'] 	= array('Sitemap content',
																		'Check the contents you want to have written to the sitemap file.');

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb_jumpTo'] = array('Redirect to',
																		'Please select the page which contains the frontend module of the forum.');

/**
 * Legend
 */
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_general_legend'] 		= 'Forum - General';
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_bbcodes_legend'] 		= 'Forum - BBCodes';
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_boxes_legend'] 		= 'Forum - Box navigation settings';
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_jqui_legend'] 			= 'Forum - jQuery UI';
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_maps_legend'] 			= 'Forum - Maps (con4gis)';
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_lib_legend'] 			= 'Forum - jQuery libraries';
$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_sitemap_legend'] 		= 'Forum - XML sitemap';

$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb_legend'] 		= 'Breadcrumb';

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['TREE'] 				= 'Tree';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['BOXES'] 			= 'Boxes';

$GLOBALS['TL_LANG']['tl_module']['c4g_references']['THREAD'] 			= 'Display all posts of thread';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['FPOST'] 			= 'Display first post';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['LPOST'] 			= 'Display last post';

$GLOBALS['TL_LANG']['tl_module']['c4g_references']['UU'] 				= 'Do not use real-names (use usernames instead)';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['FF'] 				= 'Use only the first-name';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['LL'] 				= 'Use only the last-name';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['FL'] 				= 'Use first- and last-name';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['LF'] 				= 'Use last- and first-name';

$GLOBALS['TL_LANG']['tl_module']['c4g_references']['UP'] 				= 'Oldest post first';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['DN'] 				= 'Latest post first';

$GLOBALS['TL_LANG']['tl_module']['c4g_references']['NC'] 				= 'Do not use collapsible posts';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['CO'] 				= 'All posts uncollapsed';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['CC'] 				= 'All posts collapsed';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['CF'] 				= 'First post uncollapsed';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['CL'] 				= 'Last post uncollapsed';

$GLOBALS['TL_LANG']['tl_module']['c4g_references']['THREADS'] 			= 'Public threads';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['FORUMS'] 			= 'Public forums';
$GLOBALS['TL_LANG']['tl_module']['c4g_references']['INTROS'] 			= 'Public forums - Intropages';

?>