<?php

/**
 * Usethe "memberLink" key in the eval array to indicate this field as a member link field, e. g. homepage, facebook, twitter.
 * This key is used in the member data generation for the forum to get all member links as output them.
 */

$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] = str_replace(
    '{groups_legend}',
    '{forum_member_legend},memberImage,memberSignature,memberPosts,memberHomepageLink,memberFacebookLink,memberTwitterLink,memberGooglePlusLink;{groups_legend}',
    $GLOBALS['TL_DCA']['tl_member']['palettes']['default']
);

//ToDo überarbeiten. So im Backend nicht pflegbar. Zumindest muss der Avatar gelöscht werden können, wenn man beispielsweise ein Mitglied im Backend kopiert.
$GLOBALS['TL_DCA']['tl_member']['fields']['memberImage'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberImage'],
    'exclude'                 => true,
    'inputType'               => 'avatar',
    'save_callback'           => array(array('tl_member_dca', 'handleMemberImage')),
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

$GLOBALS['TL_DCA']['tl_member']['fields']['memberHomepageLink'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberHomepageLink'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'fieldType'=>'radio', 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'forum', 'memberLink' => true, 'tl_class'=>'clr w50'),
    'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['memberFacebookLink'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberFacebookLink'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'fieldType'=>'radio', 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'forum', 'memberLink' => true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['memberTwitterLink'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberTwitterLink'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'fieldType'=>'radio', 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'forum', 'memberLink' => true, 'tl_class'=>'clr w50'),
    'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_member']['fields']['memberGooglePlusLink'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_member']['memberGooglePlusLink'],
    'exclude'                 => true,
    'search'                  => true,
    'inputType'               => 'text',
    'eval'                    => array('rgxp'=>'url', 'decodeEntities'=>true, 'maxlength'=>255, 'fieldType'=>'radio', 'feEditable' => true, 'feViewable' => true, 'feGroup' => 'forum', 'memberLink' => true, 'tl_class'=>'w50'),
    'sql'                     => "varchar(255) NOT NULL default ''"
);


class tl_member_dca extends \Contao\Backend
{

    /**
     * Check if submitted memberImage is empty.
     * If a memberImage is already stored in the database, return this memberImage value.
     * Otherwise store an empty value.
     *
     * This save_callback prevents deleting memberImages when submitting the personal data form without specifying a memberImage when there is already a memberImage stored in the database.
     * It also takes into account, when the admin saves the member profile in the backend and checks again for already present memberImage data in the database.
     *
     * @param $varValue
     * @param $dc
     * @return mixed
     */
    public function handleMemberImage($varValue, $dc)
    {
        // Get the member's ID based upon the usage-location of the Widget: BE -> current viewed member, FE -> current logged in frontenduser.
        if (TL_MODE === 'FE') {
            $this->import('frontenduser');
            $iMemberId = $this->frontenduser->id;
        } else {
            $iMemberId = $dc->id;
        }

        if (empty($varValue)) {
            $sImagePathFromDatabase = C4gForumMember::getAvatarByMemberId($iMemberId);
            return $sImagePathFromDatabase;
        }

        return $varValue;
    }

}