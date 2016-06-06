<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2016 Leo Feyer
 *
 * @package Con4gis_forum
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the namespaces
 */
ClassLoader::addNamespaces(array
(
	'Con4Gis',
));


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'C4GForumBackend'             => 'system/modules/con4gis_forum/C4GForumBackend.php',
	'C4GForumHelper'              => 'system/modules/con4gis_forum/C4GForumHelper.php',
	'C4gForumSingleFileUpload'    => 'system/modules/con4gis_forum/C4gForumSingleFileUpload.php',
	'C4GForumSubscription'        => 'system/modules/con4gis_forum/C4GForumSubscription.php',
	// Lib
	'C4GUtils'                    => 'system/modules/con4gis_forum/lib/C4GUtils.php',

	// Models
	'C4gForumMember'              => 'system/modules/con4gis_forum/models/C4gForumMember.php',
	'C4gForumModel'               => 'system/modules/con4gis_forum/models/C4gForumModel.php',
	'C4gForumPost'                => 'system/modules/con4gis_forum/models/C4gForumPost.php',
	'C4gForumSession'             => 'system/modules/con4gis_forum/models/C4gForumSession.php',
	'Con4Gis\PN'                  => 'system/modules/con4gis_forum/models/PN.php',
	'Module_c4g_forum'            => 'system/modules/con4gis_forum/Module_c4g_forum.php',
	'Module_c4g_forum_breadcrumb' => 'system/modules/con4gis_forum/Module_c4g_forum_breadcrumb.php',

	// Modules
	'C4gForumAjaxApi'             => 'system/modules/con4gis_forum/modules/api/C4gForumAjaxApi.php',
	'Con4Gis\PnCenter'            => 'system/modules/con4gis_forum/modules/frontend/PnCenter.php',

	// Widgets
	'Avatar'                      => 'system/modules/con4gis_forum/widgets/Avatar.php',
	'Con4Gis\PN\Inbox' 			=> 'system/modules/con4gis_forum/lib/Inbox.php',
	'Con4Gis\PN\Compose' 			=> 'system/modules/con4gis_forum/lib/Compose.php',
	'Con4Gis\PN\View' 			=> 'system/modules/con4gis_forum/lib/View.php'
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
	'mod_c4g_pncenter'         => 'system/modules/con4gis_forum/templates',
	'forum_user_data'          => 'system/modules/con4gis_forum/templates/partials',
	'modal_inbox'          => 'system/modules/con4gis_forum/templates/',
	'modal_compose'          => 'system/modules/con4gis_forum/templates/',
	'modal_view_message'          => 'system/modules/con4gis_forum/templates/',
));
