<?php

/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

    namespace c4g\Forum\PN;

    use c4g\Forum\C4gForumPn;
    use Contao\FrontendUser;
    use Contao\User;

    /**
     * Class Compose
     * @package c4g\Forum\PN
     */
    class Compose
    {

        protected static $sTemplate = "modal_compose";


        public static function parse($data = array()){

            $oUser = FrontendUser::getInstance();
            $aPns = \c4g\Forum\C4gForumPn::getByRecipient($oUser->id);

            $oTemplate = new \FrontendTemplate(self::$sTemplate);
            $oTemplate->recipient_id = "";
            if(isset($data['recipient_id'])){
                $oTemplate->recipient_id = $data['recipient_id'];
            }
            if(isset($data['subject'])){
                $oTemplate->subject = $data['subject'];
            }
            $oTemplate->pns = $aPns;

            return $oTemplate->parse();
        }


    }