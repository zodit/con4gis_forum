<?php

    namespace c4g\Forum\PN;


    use c4g\Forum\C4gForumPn;
    use Contao\FrontendUser;
    use Contao\User;

    class View
    {

        protected static $sTemplate = "modal_view_message";


        public static function parse(){

            $aData = \Input::get("data");
            $oUser = FrontendUser::getInstance();
            $oPn = \c4g\Forum\C4gForumPn::getById($aData['id']);

            $oTemplate = new \FrontendTemplate(self::$sTemplate);
            $oTemplate->pn = $oPn;

            return $oTemplate->parse();
        }


    }