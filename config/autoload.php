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
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'c4g\Forum',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'c4g\Forum\C4GUtils'                    => 'system/modules/con4gis_forum/classes/C4GUtils.php',
	'c4g\Forum\C4GForumBackend'             => 'system/modules/con4gis_forum/classes/C4GForumBackend.php',
	'c4g\Forum\C4GForumHelper'              => 'system/modules/con4gis_forum/classes/C4GForumHelper.php',
	'c4g\Forum\C4gForumSingleFileUpload'    => 'system/modules/con4gis_forum/classes/C4gForumSingleFileUpload.php',
	'c4g\Forum\C4GForumSubscription'        => 'system/modules/con4gis_forum/classes/C4GForumSubscription.php',
	'c4g\Forum\PN\Inbox' 		  			=> 'system/modules/con4gis_forum/classes/Inbox.php',
	'c4g\Forum\PN\Compose' 		  			=> 'system/modules/con4gis_forum/classes/Compose.php',
	'c4g\Forum\PN\View' 		  			=> 'system/modules/con4gis_forum/classes/View.php',

	// Models
	'c4g\Forum\C4gForumMember'              => 'system/modules/con4gis_forum/models/C4gForumMember.php',
	'c4g\Forum\C4gForumModel'               => 'system/modules/con4gis_forum/models/C4gForumModel.php',
	'c4g\Forum\C4gForumPost'                => 'system/modules/con4gis_forum/models/C4gForumPost.php',
	'c4g\Forum\C4gForumSession'             => 'system/modules/con4gis_forum/models/C4gForumSession.php',
	'c4g\Forum\C4gForumPn'                  => 'system/modules/con4gis_forum/models/C4gForumPn.php',

	// Modules
	'c4g\Forum\C4GForum'		            => 'system/modules/con4gis_forum/modules/C4GForum.php',
	'c4g\Forum\C4GForumBreadcrumb' 		  	=> 'system/modules/con4gis_forum/modules/C4GForumBreadcrumb.php',
	'c4g\Forum\C4GForumPNCenter'            => 'system/modules/con4gis_forum/modules/C4GForumPNCenter.php',

	// api
	'C4gForumAjaxApi'             => 'system/modules/con4gis_forum/modules/api/C4gForumAjaxApi.php',
	'C4gForumPnApi' 	          => 'system/modules/con4gis_forum/modules/api/C4gForumPnApi.php',

	// Widgets
	'Avatar'                      => 'system/modules/con4gis_forum/widgets/Avatar.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_tag_widget'            => 'system/modules/con4gis_forum/templates/backend/widget',
	'c4g_subscription'         => 'system/modules/con4gis_forum/templates/mail',
	'member_grouped'           => 'system/modules/con4gis_forum/templates/member',
	'mod_c4g_forum'            => 'system/modules/con4gis_forum/templates',
	'mod_c4g_forum_breadcrumb' => 'system/modules/con4gis_forum/templates',
	'mod_c4g_forum_plainhtml'  => 'system/modules/con4gis_forum/templates',
	'mod_c4g_forum_pncenter'   => 'system/modules/con4gis_forum/templates',
	'forum_user_data'          => 'system/modules/con4gis_forum/templates/partials',
	'modal_inbox'          	   => 'system/modules/con4gis_forum/templates/',
	'modal_compose'            => 'system/modules/con4gis_forum/templates/',
	'modal_view_message'       => 'system/modules/con4gis_forum/templates/',
));
