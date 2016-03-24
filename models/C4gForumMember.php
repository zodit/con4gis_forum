<?php

/**
 * Class C4gForumMember
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