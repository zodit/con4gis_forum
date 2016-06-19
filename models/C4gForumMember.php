<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2016.
 * @link      https://www.kuestenschmiede.de
 */

namespace c4g\Forum;

/**
 * Class C4gForumMember
 * @package c4g\Forum
 */
class C4gForumMember extends \Model
{

    /**
     * Table name.
     *
     * @var string
     */
    protected static $sTable = 'tl_member';


    /**
     * Return an avatar by member id.
     *
     * @param $iMemberId
     * @return mixed
     */
    public static function getAvatarByMemberId($iMemberId)
    {
        $t = static::$sTable;
        $oDatabase = \Database::getInstance();
        $aMemberImage = $oDatabase->prepare("SELECT memberImage FROM $t WHERE id=?")->execute($iMemberId)->fetchAssoc();
        $sMemberImagePath = $aMemberImage['memberImage'];

        return $sMemberImagePath;
    }

}