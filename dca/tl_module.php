<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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



/***
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['c4g_forum'] =
		'{title_legend},name,headline,type;'.
		'{c4g_forum_comf_general_legend},c4g_forum_size,c4g_forum_scroll,c4g_forum_startforum,c4g_forum_comf_navigation,c4g_forum_threadclick,c4g_forum_show_realname,c4g_forum_postsort,c4g_forum_collapsible_posts,c4g_forum_breadcrumb,c4g_forum_hide_intropages,c4g_forum_jumpTo,c4g_forum_language,c4g_forum_tooltip;'.
		'{c4g_forum_comf_bbcodes_legend:hide},c4g_forum_bbcodes;'.
		'{c4g_forum_comf_boxes_legend:hide},c4g_forum_boxes_text,c4g_forum_boxes_subtext,c4g_forum_boxes_lastpost,c4g_forum_boxes_center;'.
		'{c4g_forum_comf_jqui_legend:hide},c4g_forum_jqui;'.
		'{c4g_forum_comf_lib_legend:hide},c4g_forum_jquery_lib,c4g_forum_jqtable_lib,c4g_forum_jqhistory_lib,c4g_forum_jqtooltip_lib,c4g_forum_jqscrollpane_lib;'.
		'{c4g_forum_comf_sitemap_legend:hide},c4g_forum_sitemap;'.

		'{protected_legend:hide},protected;'.
		'{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['c4g_forum_breadcrumb'] =
		'{title_legend},name,type;'.
		'{c4g_forum_breadcrumb_legend},c4g_forum_breadcrumb_jumpTo;'.
		'{protected_legend:hide},protected;'.
		'{expert_legend:hide},guests,cssID,space';

$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'c4g_forum_bbcodes';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'c4g_forum_jqui';
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'c4g_forum_sitemap';
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = array('tl_module_c4g_forum','updateDCA');

$GLOBALS['TL_DCA']['tl_module']['subpalettes']['c4g_forum_jqui'] =
	    'c4g_forum_jqui_lib,c4g_forum_uitheme_css_src,c4g_forum_dialogsize,c4g_forum_dialogs_embedded,c4g_forum_embdialogs_jqui,c4g_forum_breadcrumb_jqui_layout,c4g_forum_buttons_jqui_layout,c4g_forum_table_jqui_layout,c4g_forum_posts_jqui,c4g_forum_boxes_jqui_layout,c4g_forum_enable_scrollpane';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['c4g_forum_bbcodes'] =
		'c4g_forum_editor, c4g_forum_bbcodes_editor_imguploadpath, c4g_forum_bbcodes_editor_fileuploadpath, c4g_forum_bbcodes_editor_toolbaritems, c4g_forum_bbcodes_editor_uploadTypes,c4g_forum_bbcodes_editor_maxFileSize,c4g_forum_bbcodes_editor_imageWidth, c4g_forum_bbcodes_editor_imageHeight'; //, c4g_forum_bbcodes_smileys,c4g_forum_bbcodes_smileys_url,c4g_forum_bbcodes_autourl';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['c4g_forum_sitemap'] =
'c4g_forum_sitemap_filename,c4g_forum_sitemap_contents';

/***
 * Fields - General
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_size'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_size'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => array('px', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
	'eval'                    => array('rgxp'=>'digit' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_scroll'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_scroll'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => array('px', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
	'eval'                    => array('rgxp'=>'digit' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_startforum'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_startforum'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'foreignKey'              => 'tl_c4g_forum.name',
    'eval'                 	  => array( 'includeBlankOption' => true, 'blankOptionLabel' => '-')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_comf_navigation'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_comf_navigation'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('BOXES','TREE'),
	'default'                 => 'BOXES',
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_references'],

);


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_threadclick'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_threadclick'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'			      => array('THREAD','FPOST','LPOST'),
	'default'			      => 'THREAD',
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_references'],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_show_realname'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_show_realname'],
	'exclude'                 => true,
	'inputType'               => 'radio',
	'options'			      => array('UU','FF','LL','FL','LF'),
	'default'			      => 'UU',
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_references'],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_postsort'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_postsort'],
		'exclude'                 => true,
		'inputType'               => 'radio',
		'options'			      => array('UP','DN'),
		'default'			      => 'UP',
		'reference'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_references'],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_collapsible_posts'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_collapsible_posts'],
		'exclude'                 => true,
		'inputType'               => 'radio',
		'options'			      => array('NC','CO','CC','CF','CL'),
		'default'			      => 'NC',
		'reference'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_references'],
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_breadcrumb'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox'
);


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_hide_intropages'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_hide_intropages'],
	'exclude'                 => true,
	'default'                 => '',
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jumpTo'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jumpTo'],
		'exclude'                 => true,
		'inputType'               => 'pageTree',
		'eval'                    => array('fieldType'=>'radio')
);


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_language'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_language'],
	'exclude'                 => true,
	'default'                 => '',
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>5, "style" => 'width: 100px' )
);


/***
 * Fields - BBCodes
*/

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes'],
		'exclude'                 => true,
		'default'                 => true,
		'inputType'               => 'checkbox',
		'eval'                    => array('submitOnChange'=>true),

);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor'],
		'exclude'                 => true,
		'default'                 => false,
		'inputType'               => 'checkbox'
);



$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_ckeditor'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_ckeditor'],
		'exclude'                 => true,
		'default'                 => false,
		'inputType'               => 'checkbox'
);

	$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_editor'] = array(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_editor'],
		'exclude'                 => true,
		'default'                 => false,
		'inputType'               => 'radio',
		'options' => array(
			'ck' => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_editor_option']['ck'],
			'bb' => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_editor_option']['bb'],
			'no' => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_editor_option']['no'],
		)
	);



$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_imguploadpath'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_imguploadpath'],
		'exclude'                 => true,
		'default'                 => '',
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>128, "style" => 'width: 200px' )
);
$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_fileuploadpath'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_fileuploadpath'],
		'exclude'                 => true,
		'default'                 => '',
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>128, "style" => 'width: 200px' )
);
$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_toolbaritems'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_toolbaritems'],
		'exclude'                 => true,
		'default'                 => 'Cut,Copy,Paste,PasteText,PasteFromWord,-,Undo,Redo,TextColor,Bold,Italic,Underline,Strike,Subscript,Superscript,-,JustifyLeft,JustifyCenter,JustifyRight,JustifyBlock,-,RemoveFormat,NumberedList,BulletedList,Link,Unlink,Anchor,Image,FileUpload,Table,Smiley',
		'inputType'               => 'text',
		'eval'                    => array('class' => '', 'style' => 'width:662px' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_smileys'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_smileys'],
		'exclude'                 => true,
		'default'                 => true,
		'inputType'               => 'checkbox'
);
$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_tooltip'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip'],
		'exclude'                 => true,
		'default'                 => 'body_first_post',
		'inputType'               => 'select',
		'options' => array(
			'title_first_post' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['title_first_post'],
			'title_last_post' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['title_last_post'],
			'body_first_post' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['body_first_post'],
			'body_last_post' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['body_last_post'],
			'threadtitle' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['threadtitle'],
			'threadbody' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['threadbody'],
			'disabled' => $GLOBALS['TL_LANG']['tl_module']['c4g_forum_tooltip_value']['disabled']
		)
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_smileys_url'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_smileys_url'],
		'exclude'                 => true,
		'default'                 => 'system/modules/con4gis_forum/html/images/smileys',
		'inputType'               => 'text',
		'eval'                    => array('maxlength'=>128, "style" => 'width: 200px' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_autourl'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_autourl'],
		'exclude'                 => true,
		'default'                 => true,
		'inputType'               => 'checkbox',
);


/***
 * Fields - Boxes
 */


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_boxes_text'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_text'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'clr')
);


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_boxes_subtext'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_subtext'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_boxes_lastpost'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_lastpost'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_boxes_center'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_center'],
		'exclude'                 => true,
		'default'                 => false,
		'inputType'               => 'checkbox',
);

/***
 * Fields - jQuery UI
 */

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqui'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqui'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),

);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqui_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqui_lib'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_uitheme_css_src'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_uitheme_css_src'],
	'exclude'                 => true,
	'inputType'               => 'fileTree',
	'eval'                    => array('fieldType'=>'radio', 'files'=>true, 'extensions'=>'css')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_dialogsize'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_dialogsize'],
	'exclude'                 => true,
	'inputType'               => 'imageSize',
	'options'                 => array('px', 'em', 'pt', 'pc', 'in', 'cm', 'mm'),
	'eval'                    => array('rgxp'=>'digit' )
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_dialogs_embedded'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_dialogs_embedded'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_embdialogs_jqui'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_embdialogs_jqui'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_breadcrumb_jqui_layout'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb_jqui_layout'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_buttons_jqui_layout'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_buttons_jqui_layout'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_table_jqui_layout'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_table_jqui_layout'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_posts_jqui'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_posts_jqui'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_boxes_jqui_layout'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_boxes_jqui_layout'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
);


/***
 * Fields - Libraries
 */


$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jquery_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jquery_lib'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'w50')

);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqtable_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqtable_lib'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqtable_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqtable_lib'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqhistory_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqhistory_lib'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqtooltip_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqtooltip_lib'],
	'exclude'                 => true,
	'default'                 => true,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_jqscrollpane_lib'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_jqscrollpane_lib'],
	'exclude'                 => true,
	'default'                 => false,
	'inputType'               => 'checkbox',
	'eval'               	  => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_enable_maps'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_enable_maps'],
	'exclude'                 => true,
	'default'                 => '',
	'inputType'               => 'checkbox',
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_sitemap'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap'],
	'exclude'                 => true,
	'default'                 => '',
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'save_callback'			  => array(array('tl_module_c4g_forum','update_sitemap'))
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_sitemap_filename'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap_filename'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>30 ),
	'save_callback'			  => array(array('tl_module_c4g_forum','update_sitemap'))
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_sitemap_contents'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_sitemap_contents'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
    'options'        		  => array('THREADS','FORUMS','INTROS'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_module']['c4g_references'],
	'eval'                    => array('multiple'=>true),
	'save_callback'			  => array(array('tl_module_c4g_forum','update_sitemap'))
);




$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_uploadTypes'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_uploadTypes'],
    'inputType'               => 'text',
    'eval'                    => array('tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_maxFileSize'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_maxFileSize'],
    'inputType'               => 'text',
    'default' => '2048000',
    'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'nospace'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_imageWidth'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_imageWidth'],
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'nospace'=>true, 'tl_class'=>'w50')
);

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_bbcodes_editor_imageHeight'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_bbcodes_editor_imageHeight'],
    'inputType'               => 'text',
    'eval'                    => array('mandatory'=>true, 'rgxp'=>'digit', 'nospace'=>true, 'tl_class'=>'w50')
);






/***
 * Fields - Breadcrumb Module
 */

$GLOBALS['TL_DCA']['tl_module']['fields']['c4g_forum_breadcrumb_jumpTo'] = array
(
		'label'                   => &$GLOBALS['TL_LANG']['tl_module']['c4g_forum_breadcrumb_jumpTo'],
		'exclude'                 => true,
		'inputType'               => 'pageTree',
		'eval'                    => array('fieldType'=>'radio', 'mandatory'=>true)
);

/**
 * Class tl_module_c4g_forum
 *
 * @copyright  Küstenschmiede GmbH Software & Design 2012
 * @author     Jürgen Witte <http://www.kuestenschmiede.de>
 * @package    con4gis
 * @author     Jürgen Witte <http://www.kuestenschmiede.de>
 */
class tl_module_c4g_forum extends Backend {

	/**
	 * Update the palette information that depend on other values
	 */
	public function updateDCA(DataContainer $dc)
	{
		// add Maps section if c4gMaps is installed
		if ($GLOBALS['con4gis_maps_extension']['installed']) {
			$c4gMapsFields = '{c4g_forum_comf_maps_legend:hide},c4g_forum_enable_maps;';
			$GLOBALS['TL_DCA']['tl_module']['palettes']['c4g_forum'] =
				str_replace('{protected_legend',$c4gMapsFields.'{protected_legend',
					$GLOBALS['TL_DCA']['tl_module']['palettes']['c4g_forum']);

		}
	}

	public function update_sitemap($value,$dc)
	{
		if ($value != $dc->varValue){
			// force update of sitemap in the frontend by setting last sitemap timestamp to 0
			$this->Database->prepare(
					"UPDATE tl_module SET ".
					"c4g_forum_sitemap_updated=0 ".
					"WHERE id = ".$this->Input->get('id')
			)->executeUncached();

		}

		return $value;
	}
}

?>