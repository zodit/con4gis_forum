<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2014 Leo Feyer
 *
 * @package Con4gis_forum
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'C4GForumBackend'             => 'system/modules/con4gis_forum/C4GForumBackend.php',
	'C4GForumHelper'              => 'system/modules/con4gis_forum/C4GForumHelper.php',
	'C4GForumSubscription'        => 'system/modules/con4gis_forum/C4GForumSubscription.php',
	// Lib
	'C4GUtils'                    => 'system/modules/con4gis_forum/lib/C4GUtils.php',

	// Modules
	'C4gForumAjaxApi'             => 'system/modules/con4gis_forum/modules/api/C4gForumAjaxApi.php',
	'Module_c4g_forum'            => 'system/modules/con4gis_forum/Module_c4g_forum.php',
	'Module_c4g_forum_breadcrumb' => 'system/modules/con4gis_forum/Module_c4g_forum_breadcrumb.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'mod_c4g_forum'            => 'system/modules/con4gis_forum/templates',
	'mod_c4g_forum_breadcrumb' => 'system/modules/con4gis_forum/templates',
	'mod_c4g_forum_plainhtml'  => 'system/modules/con4gis_forum/templates',
));
