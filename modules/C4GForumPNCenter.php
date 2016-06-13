<?php

    namespace c4g\Forum;
    use Contao\User;


    /**
     * Class PnCenter
     *
     * @package c4g
     */
    class C4GForumPNCenter extends \Module
    {

        protected $strTemplate = "mod_c4g_forum_pncenter";

        /**
         * Display a wildcard in the back end
         *
         * @return string
         */
        public function generate()
        {

            if (TL_MODE == 'BE') {
                $objTemplate = new \BackendTemplate('be_wildcard');

                $objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['c4g_forum_pncenter'][0] . ' ###';
                $objTemplate->title    = $this->headline;
                $objTemplate->id       = $this->id;
                $objTemplate->link     = $this->title;
                $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

                return $objTemplate->parse();
            }

            return parent::generate();
        }

        /**
         * @return string
         */
        public static function getClientLangVars() {
            \System::loadLanguageFile("tl_c4g_forum_pn");

            return '<script>
                var C4GLANG = {
                    send_error: "'.$GLOBALS['TL_LANG']['tl_c4g_forum_pn']['send_error'].'",
                    send: "'.$GLOBALS['TL_LANG']['tl_c4g_forum_pn']['send'].'",
                    delete: "'.$GLOBALS['TL_LANG']['tl_c4g_forum_pn']['delete'].'",
                    close: "'.$GLOBALS['TL_LANG']['tl_c4g_forum_pn']['close'].'",
                    reply: "'.$GLOBALS['TL_LANG']['tl_c4g_forum_pn']['reply'].'",
                    delete_confirm: "'.$GLOBALS['TL_LANG']['tl_c4g_forum_pn']['delete_confirm'].'"
                 };
                
                var sCurrentLang = "'.$GLOBALS['TL_LANGUAGE'].'";
                
            </script>';

        }


        /**
         *
         */
        protected function compile()
        {

            \System::loadLanguageFile("tl_c4g_forum_pn");

            $aUser = \FrontendUser::getInstance()->getData();
            $iCountAll = C4gForumPn::countBy($aUser['id'],"status" , true);
            $iCountUnread = C4gForumPn::countBy($aUser['id'],"status" , 0);

            $this->Template->count_all = $iCountAll;
            $this->Template->count_unread = $iCountUnread;
            $sJsLang = $this->getClientLangVars();

            $this->Template->c4g_pn_js = $sJsLang;
        }


    }