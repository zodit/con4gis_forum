<?php

/**
 * Contao Open Source CMS
 *
 * @version   php 5
 * @package   con4gis
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2014
 * @link      https://www.kuestenschmiede.de
 * @filesource 
 */

			
			// Run the version 3.2 update for all fields 
			if ($blnDone == false)
			{
				Database\Updater::convertSingleField('tl_c4g_forum', 'box_imagesrc');
				Database\Updater::convertSingleField('tl_module', 'c4g_forum_uitheme_css_src');
			}

		}

	}
}


$objC4GForumRunonceJob = new C4GForumRunonceJob();
$objC4GForumRunonceJob->run();

?>