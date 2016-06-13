<?php
  
    namespace c4g\Forum\PN;


    use c4g\Forum\C4gForumPn;
    use Contao\FrontendUser;
    use Contao\User;

    class Inbox
    {

        protected static $sTemplate = "modal_inbox";


        public static function parse(){

            $oUser = FrontendUser::getInstance();
            $aPns = \c4g\Forum\C4gForumPn::getByRecipient($oUser->id);

            $oTemplate = new \FrontendTemplate(self::$sTemplate);
            $oTemplate->pns = $aPns;

            return $oTemplate->parse();
        }


    }