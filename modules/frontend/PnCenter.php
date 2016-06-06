<?php
    /**
     *
     *          _           _                       _
     *         | |         | |                     | |
     *      ___| | __ _ ___| |____      _____  _ __| | _____
     *     / __| |/ _` / __| '_ \ \ /\ / / _ \| '__| |/ / __|
     *     \__ \ | (_| \__ \ | | \ V  V / (_) | |  |   <\__ \
     *     |___/_|\__,_|___/_| |_|\_/\_/ \___/|_|  |_|\_\___/
     *                                        web development
     *
     *     http://www.slash-works.de </> hallo@slash-works.de
     *
     *
     * @author      rwollenburg
     * @copyright   rwollenburg@slashworks
     * @since       03.06.16 10:48
     * @package     Core
     *
     */

    namespace Con4Gis;
    use Contao\User;


    /**
     * Class PnCenter
     *
     * @package Con4Gis
     */
    class PnCenter extends \Module
    {

        protected $strTemplate = "mod_c4g_pncenter";


        /**
         *
         */
        protected function compile()
        {

            \System::loadLanguageFile("tl_c4g_pn");

            $aUser = \FrontendUser::getInstance()->getData();
            $iCountAll = PN::countBy($aUser['id'],"status" , true);
            $iCountUnread = PN::countBy($aUser['id'],"status" , 0);


            

            $this->Template->count_all = $iCountAll;
            $this->Template->count_unread = $iCountUnread;
            $sJsLang = '
            <script>
                var C4GLANG = {
                    send: "'.$GLOBALS['TL_LANG']['tl_c4g_pn']['send'].'",
                    delete: "'.$GLOBALS['TL_LANG']['tl_c4g_pn']['delete'].'",
                    close: "'.$GLOBALS['TL_LANG']['tl_c4g_pn']['close'].'",
                    reply: "'.$GLOBALS['TL_LANG']['tl_c4g_pn']['reply'].'",
                    delete_confirm: "'.$GLOBALS['TL_LANG']['tl_c4g_pn']['delete_confirm'].'"
                };
                
                var sCurrentLang = "'.$GLOBALS['TL_LANGUAGE'].'";
            </script>';

            $this->Template->c4g_pn_js = $sJsLang;
            
            
        }


    }