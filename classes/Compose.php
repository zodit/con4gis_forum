<?php

    namespace c4g\Forum\PN;

    use c4g\Forum\C4gForumPn;
    use Contao\FrontendUser;
    use Contao\User;

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