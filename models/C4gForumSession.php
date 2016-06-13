<?php

namespace c4g\Forum;

class C4gForumSession extends \Model
{

    /**
     * Table name.
     *
     * @var string
     */
    protected static $sTable = 'tl_session';


    /**
     * @param $iMemberId
     * @param int $iThreshold
     * @return bool
     */
    public static function getOnlineStatusByMemberId($iMemberId, $iThreshold = 500)
    {
        $t = static::$sTable;
        $iTimeThreshold = time() - $iThreshold;

        $oDatabase = \Database::getInstance();
        $oTimeStamp = $oDatabase->prepare("SELECT tstamp FROM $t WHERE pid = ? AND tstamp > ?")->execute($iMemberId, $iTimeThreshold);

        // If member present in the session table and last activity (timestamp) is within now and the given time-threshold, the user is online.
        if ($oTimeStamp->numRows > 0) {
            return true;
        }

        return false;
    }

}