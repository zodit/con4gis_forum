<?php

namespace c4g\Forum;

/**
 * Class C4gForumPost
 */
class C4gForumPost extends \Model
{

    /**
     * Table name.
     *
     * @var string
     */
    protected static $sTable = 'tl_c4g_forum_post';


    /**
     * Get posts count by member id.
     *
     * @param $iMemberId
     * @return mixed|null
     */
    public static function getMemberPostsCountById($iMemberId)
    {
        $t = static::$sTable;
        $oDatabase = \Database::getInstance();
        $oResult = $oDatabase->prepare("SELECT id FROM $t WHERE author=?")->execute($iMemberId);

        return $oResult->numRows;
    }

}