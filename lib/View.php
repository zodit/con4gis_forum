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
     * @since       03.06.16 15:23
     * @package     Core
     *
     */

    namespace Con4Gis\PN;


    use Con4Gis\PN;
    use Contao\FrontendUser;
    use Contao\User;

    class View
    {

        protected static $sTemplate = "modal_view_message";


        public static function parse(){

            $aData = \Input::get("data");
            $oUser = FrontendUser::getInstance();
            $oPn = PN::getById($aData['id']);

            $oTemplate = new \FrontendTemplate(self::$sTemplate);
            $oTemplate->pn = $oPn;

            return $oTemplate->parse();
        }


    }