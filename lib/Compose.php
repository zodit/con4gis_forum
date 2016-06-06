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

    class Compose
    {

        protected static $sTemplate = "modal_compose";


        public static function parse($data = array()){

            $oUser = FrontendUser::getInstance();
            $aPns = PN::getByRecipient($oUser->id);

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