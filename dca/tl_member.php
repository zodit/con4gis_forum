<?php

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(
    '{groups_legend}',
    '{forum_member_legend},memberImage,memberSignature,memberPosts;{groups_legend}',
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default']
);

$GLOBALS['TL_DCA']['tl_member']['fields']['memberImage'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberImage'],
    'exclude'                 => true,
    'inputType'               => 'avatar',
    'eval'                    => array('filesOnly'=>true, 'multiple' => false, 'fieldType'=>'radio', 'feEditable'=>true, 'feViewable'=>true, 'feGroup'=>'forum', 'storeFile' => true, 'uploadFolder' => 'files/userimages', 'tl_class'=>'clr'),
    'sql'                     => "mediumtext NULL"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['memberSignature'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberSignature'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'textarea',
    'eval'                    => array('feEditable' => true, 'feViewable' => true, 'feGroup' => 'forum'),
    'sql'                     => "mediumtext NULL"
);