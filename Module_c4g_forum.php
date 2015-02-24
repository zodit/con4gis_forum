<?php if (!defined('TL_ROOT')) {
    die('You can not access this file directly!');
}

    /**
     * Contao Open Source CMS
     *
     * @version   php 5
     * @package   con4gis
     * @author    Jürgen Witte & Tobias Dobbrunz <http://www.kuestenschmiede.de>
     * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
     * @copyright Küstenschmiede GmbH Software & Design 2014 - 2015
     * @link      https://www.kuestenschmiede.de
     * @filesource
     */


    $GLOBALS['c4gForumErrors']           = array();
    $GLOBALS['c4gForumSearchParamCache'] = array();

    /**
     * to catch warnings etc. and put them into the ajax response separately
     */
    function c4gForumErrorHandler($code, $text, $file, $line)
    {

        if ($code != E_NOTICE) {
            if ($code & error_reporting()) {
                $error['code']               = $code;
                $error['text']               = $text;
                $error['file']               = $file;
                $error['line']               = $line;
                $GLOBALS['c4gForumErrors'][] = $error;
            }
        }
    }

    /**
     * Class Module_c4g_forum
     */
    class Module_c4g_forum extends Module
    {

        /**
         * Template
         *
         * @var string
         */
        protected $strTemplate = 'mod_c4g_forum';

        /**
         * @var bool
         */
        protected $plainhtml = false;

        /**
         * @var string
         */
        protected $action = "";

        /**
         * @var null
         */
        protected $putVars = null;

        /**
         * @var C4GForumHelper
         */
        protected $helper = null;

        /**
         * @var bool
         */
        protected $dialogs_jqui = true;


        /**
         * Display a wildcard in the back end
         *
         * @return string
         */
        public function generate()
        {

            if (TL_MODE == 'BE') {
                $objTemplate = new BackendTemplate('be_wildcard');

                $objTemplate->wildcard = '### ' . $GLOBALS['TL_LANG']['FMD']['c4g_forum'][0] . ' ###';
                $objTemplate->title    = $this->headline;
                $objTemplate->id       = $this->id;
                $objTemplate->link     = $this->title;
                $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

                return $objTemplate->parse();
            }
            if (array_key_exists('_escaped_fragment_', $_GET)) {
                $this->strTemplate = 'mod_c4g_forum_plainhtml';
                $this->plainhtml   = true;
            }

            return parent::generate();
        }


        /**
         * Generate module
         */
        protected function compile()
        {

            global $objPage;
            $this->initMembers();

            $useGoogleMaps = false;
            if ($this->c4g_forum_enable_maps) {
                $useGoogleMaps = C4GForumHelper::isGoogleMapsUsed($this->Database);
            }
            // initialize used Javascript Libraries and CSS files
            C4GJQueryGUI::initializeLibraries(
                true,                                            // add c4gJQuery GUI Core LIB
                ($this->c4g_forum_jquery_lib == true),            // add JQuery
                ($this->c4g_forum_jqui_lib == true),            // add JQuery UI
                ($this->c4g_forum_comf_navigation == 'TREE'),    // add Tree Control
                ($this->c4g_forum_jqtable_lib == true),            // add Table Control
                ($this->c4g_forum_jqhistory_lib == true),        // add history.js
                ($this->c4g_forum_jqtooltip_lib == true),        // add simple tooltip
                ($this->c4g_forum_enable_maps == true),         // add C4GMaps
                $useGoogleMaps,                                    // add C4GMaps - include Google Maps Javascript?
                ($this->c4g_forum_enable_maps == true),         // add C4GMaps Feature Editor
                ($this->c4g_forum_bbcodes == true),
                ($this->c4g_forum_jqscrollpane_lib == true));   // add jScrollPane

            //Override JQuery UI Default Theme CSS if defined
            if ($this->c4g_forum_uitheme_css_src) {
                if (version_compare(VERSION, '3.2', '>=')) {
                    // Contao 3.2.x Format
                    $objFile                            = FilesModel::findByUuid($this->c4g_forum_uitheme_css_src);
                    $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
                } else {
                    if (is_numeric($this->c4g_forum_uitheme_css_src)) {
                        // Contao 3.x Format
                        $objFile                            = FilesModel::findByPk($this->c4g_forum_uitheme_css_src);
                        $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
                    } else {
                        // Contao 2 Format
                        $GLOBALS['TL_CSS']['c4g_jquery_ui'] = $this->c4g_forum_uitheme_css_src;
                    }
                }
            }

            $GLOBALS ['TL_CSS'] [] = 'system/modules/con4gis_forum/html/css/c4gForum.css';
            //$GLOBALS ['TL_CSS'] [] = 'system/modules/con4gis_forum/html/css/bbcodes.css';
            $data['id']      = $this->id;
            $data['ajaxUrl'] = "system/modules/con4gis_core/api/c4g_forum_ajax";
            // $data['ajaxData'] = "action=fmd&id=".$this->id."&language=".$GLOBALS['TL_LANGUAGE']."&page=".$objPage->id;
            $data['ajaxData'] = $this->id;

            $size = deserialize($this->c4g_forum_size, true);
            if ($size[0] != 0) {
                $data['width'] = $size[0] . $size[2];
            }
            if ($size[1] != 0) {
                $data['height'] = $size[1] . $size[2];
            }

            if ($_GET['state']) {
                $request = $_GET['state'];
            } else {
                $request = 'initnav';
            }
            $data['initData'] = $this->generateAjax($request);

            // save forum url for linkbuilding in ajaxrequests
            $aTmpData = $this->Session->getData();
            if(stristr($aTmpData['referer']['current'],"/con4gis_core/api/") === false) {
                $aTmpData['current_forum_url'] = $aTmpData['referer']['current'];
                $this->Session->setData($aTmpData);
            }else{
                $aTmpData['referer']['last'] = $aTmpData['current_forum_url'];
                $aTmpData['referer']['current'] = $aTmpData['current_forum_url'];
                $this->Session->setData($aTmpData);
            }


            $data['div'] = 'c4g_forum';
            switch ($this->c4g_forum_comf_navigation) {
                case 'TREE':
                    $data['navPanel'] = true;
                    break;

                case 'BOXES':
                    $data['navPanel'] = false;
                    break;

                default:
                    break;
            }
            $data['jquiBreadcrumb']      = $this->c4g_forum_breadcrumb_jqui_layout;
            $data['jquiButtons']         = $this->c4g_forum_buttons_jqui_layout;
            $data['embedDialogs']        = $this->c4g_forum_dialogs_embedded;
            $data['jquiEmbeddedDialogs'] = $this->dialogs_jqui;

            \Contao\Session::getInstance()->set("con4gisImageUploadPath", $this->c4g_forum_bbcodes_editor_imguploadpath);
            \Contao\Session::getInstance()->set("con4gisFileUploadPath", $this->c4g_forum_bbcodes_editor_fileuploadpath);
            \Contao\Session::getInstance()->set("c4g_forum_bbcodes_editor_uploadTypes", $this->c4g_forum_bbcodes_editor_uploadTypes);
            \Contao\Session::getInstance()->set("c4g_forum_bbcodes_editor_maxFileSize", $this->c4g_forum_bbcodes_editor_maxFileSize);
            \Contao\Session::getInstance()->set("c4g_forum_bbcodes_editor_imageWidth", $this->c4g_forum_bbcodes_editor_imageWidth);
            \Contao\Session::getInstance()->set("c4g_forum_bbcodes_editor_imageHeight", $this->c4g_forum_bbcodes_editor_imageHeight);

            $aToolbarButtons = explode(",", $this->c4g_forum_bbcodes_editor_toolbaritems);


            $GLOBALS['TL_CSS'][] = 'system/modules/con4gis_core/lib/jQuery/plugins/chosen/chosen.css';
            $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/con4gis_core/lib/jQuery/plugins/chosen/chosen.jquery.min.js';

            if ($this->c4g_forum_editor === "ck") {
                $GLOBALS['TL_HEAD'][]       = "<script>var ckEditorItems = ['" . implode("','", $aToolbarButtons) . "'];</script>";
                $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/con4gis_core/lib/ckeditor/ckeditor.js';
            }


            if (!$this->c4g_forum_breadcrumb_jqui_layout) {
                $data['breadcrumbDelim'] = '>';
            }
            if (($this->action == 'readthread') ||
                ($this->action == 'forum') ||
                ($this->action == 'forumbox')
            ) {
                // add this for search engines
                // when search engines find this they are supposed to send a second request
                // with a "_escaped_fragment_" GET parameter
                if (!$this->plainhtml) {
                    $GLOBALS['TL_HEAD'][] = '<meta name="fragment" content="!">';
                }
            }

            $this->Template->c4gdata = $data;
        }


        /**
         *
         * Check Permissions for the current action
         */
        public function checkPermission($forumId)
        {

            return array(
                $this->helper->checkPermissionForAction($forumId, $this->action),
                $this->helper->permissionError
            );
        }


        /**
         *
         * Check Permissions for a given action
         */
        public function checkPermissionForAction($forumId, $action)
        {

            return array(
                $this->helper->checkPermissionForAction($forumId, $action),
                $this->helper->permissionError
            );
        }


        /**
         * @param array $options
         *
         * @return mixed
         */
        public function addDefaultDialogOptions($options)
        {

            $options['show'] = 'fold';
            $options['hide'] = 'fold';
            $size            = deserialize($this->c4g_forum_dialogsize, true);
            if ($size[0] != 0) {
                if (!isset($options['width'])) {
                    $options['width'] = $size[0];
                }
            }
            if ($size[1] != 0) {
                if (!isset($options['height'])) {
                    $options['height'] = $size[1];
                }
            }

            return $options;
        }


        /**
         *
         * @param int $forumId
         */
        public function addForumButtons($buttons, $forumId)
        {

            if ($this->map_enabled() && $this->helper->checkPermission($forumId, 'mapview')) {
                $forum = $this->helper->getForumFromDB($forumId);
                if ($forum['enable_maps'] || $forum['enable_maps_inherited']) {
                    array_insert($buttons, 0, array(
                        array(
                            "id"   => 'viewmapforforum:' . $forumId,
                            "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['VIEW_MAP_FOR_FORUM']
                        )
                    ));

                }
            }

            if ($this->helper->checkPermission($forumId, 'subscribeforum')) {
                $subscriptionId = $this->helper->subscription->getSubforumSubscriptionFromDB($forumId, $this->User->id);
                if ($subscriptionId) {
                    $text = $GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_SUBFORUM'];
                } else {
                    $text = $GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIBE_SUBFORUM'];
                }
                array_insert($buttons, 0, array(
                    array(
                        "id"   => 'subscribesubforumdialog:' . $forumId,
                        "text" => $text
                    )
                ));
            }

            if ($this->helper->checkPermission($forumId, 'addmember')) {
                array_insert($buttons, 0, array(
                    array(
                        "id"   => 'addmemberdialog:' . $forumId,
                        "text" => $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['ADD_MEMBER']
                    )
                ));
            }
            if ($this->helper->checkPermission($forumId, 'newthread')) {
                array_insert($buttons, 0, array(
                    array(
                        "id"   => 'newthread:' . $forumId,
                        "text" => $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['NEW_THREAD']
                    )
                ));
            }

            return $buttons;
        }


        /**
         * @param $id
         * @param $forumTree
         *
         * @return array
         */
        public function getForumInTable($id, $forumTree)
        {

            list($access, $message) = $this->checkPermissionForAction($id, 'forum');
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $data                 = array();
            $data['aoColumnDefs'] = array(
                array(
                    'sTitle'      => 'key',
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(0)
                ),
                array(
                    'sTitle'                => $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'],
                    "sClass"                => 'c4g_forum_tlist_threadname',
                    "sWidth"                => '50%',
                    "aDataSort"             => array(
                        9,
                        1
                    ),
                    "aTargets"              => array(1),
                    "c4gMinTableSizeWidths" => array(
                        array(
                            "tsize" => 500,
                            "width" => '50%'
                        ),
                        array(
                            "tsize" => 0,
                            "width" => ''
                        )
                    )
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_AUTHOR_SHORT'],
                    "sClass"          => 'c4g_forum_tlist_last_author',
                    "aDataSort"       => array(
                        9,
                        2,
                        4
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(2),
                    "c4gMinTableSize" => 700
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_POST_SHORT'],
                    "sClass"          => 'c4g_forum_tlist_last_post',
                    "aDataSort"       => array(
                        10,
                        4
                    ),
                    "bSearchable"     => false,
                    "asSorting"       => array(
                        'desc',
                        'asc'
                    ),
                    "aTargets"        => array(3),
                    "c4gMinTableSize" => 700
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(4)
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['AUTHOR'],
                    "sClass"          => 'c4g_forum_tlist_author',
                    "aDataSort"       => array(
                        9,
                        5,
                        7
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(5),
                    "c4gMinTableSize" => 500
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['CREATED_ON'],
                    "sClass"          => 'c4g_forum_tlist_created',
                    "aDataSort"       => array(
                        10,
                        7
                    ),
                    "asSorting"       => array(
                        'desc',
                        'asc'
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(6),
                    "c4gMinTableSize" => 500
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(7)
                ),
                array(
                    'sTitle'      => '#',
                    "sClass"      => 'c4g_forum_tlist_postcount',
                    "asSorting"   => array(
                        'desc',
                        'asc'
                    ),
                    "bSearchable" => false,
                    "aTargets"    => array(8)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(9)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(10)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(11)
                ),
            );
            if ($this->c4g_forum_table_jqui_layout) {
                $data['bJQueryUI'] = true;
            }

            $scroll = deserialize($this->c4g_forum_scroll, true);
            if ($scroll[0] != 0) {
                $data['sScrollX'] = $scroll[0] . $scroll[2];
            }
            if ($scroll[1] != 0) {
                $data['sScrollY'] = $scroll[1] . $scroll[2];
            } else {
                $size = deserialize($this->c4g_forum_size, true);
                if (($size[1] >= 200) && ($size[2] == 'px')) {
                    // if height is set, but not scrollY, then try to set scrollY to a useful value
                    // note: the perfect value depends on the used jQuery UI theme
                    $data['sScrollY'] = ($size[1] - 120) . $scroll[2];
                }

            }

            $data['aaSorting']       = array(
                array(
                    3,
                    'desc'
                )
            );
            $data['bScrollCollapse'] = true;
            $data['bStateSave']      = true;
            $data['sPaginationType'] = 'full_numbers';
            $data['oLanguage']       = array(
                "oPaginate"      => array(
                    "sFirst"    => '<<',
                    "sLast"     => '>>',
                    "sPrevious" => '<',
                    "sNext"     => '>'
                ),
                "sEmptyTable"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_EMPTY'],
                "sInfo"          => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_INFO'],
                "sInfoEmpty"     => "-",
                "sInfoFiltered"  => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_FILTERED'],
                "sInfoThousands" => '.',
                "sLengthMenu"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_LENGTHMENU'],
                "sProcessing"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_PROCESSING'],
                "sSearch"        => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_SEARCH'],
                "sZeroRecords"   => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_NOTFOUND']
            );

            $threads = $this->helper->getThreadsFromDB($id);
            $forum   = $this->helper->getForumFromDB($id);
            foreach ($threads AS $thread) {
                switch ($this->c4g_forum_threadclick) {
                    case 'LPOST':
                        $threadAction = 'readlastpost:' . $thread['id'];
                        break;

                    case 'FPOST':
                        $threadAction = 'readpostnumber:' . $thread['id'] . ':1';
                        break;

                    default:
                        $threadAction = 'readthread:' . $thread['id'];
                        break;
                }
                if ($thread['lastPost']) {
                    $lastPost     = $thread['lastPost'];
                    $lastUsername = $thread['lastUsername'];
                } else {
                    $lastPost     = $thread['creation'];
                    $lastUsername = $thread['username'];
                }



                switch($this->c4g_forum_tooltip){
                    case "title_first_post":
                        $tooltip = $this->helper->getFirstPostLimitedTextOfThreadFromDB($thread['id'], 250,true);
                        $tooltip = preg_replace('/\[[^\[\]]*\]/i', '', $tooltip);
                        break;
                    case "title_last_post":
                        $tooltip = $this->helper->getLastPostLimitedTextOfThreadFromDB($thread['id'], 250,true);
                        $tooltip = preg_replace('/\[[^\[\]]*\]/i', '', $tooltip);
                        break;
                    case "body_first_post":
                        $tooltip = $this->helper->getFirstPostLimitedTextOfThreadFromDB($thread['id'], 250);
                        $tooltip = preg_replace('/\[[^\[\]]*\]/i', '', $tooltip);
                        break;
                    case "body_last_post":
                        $tooltip = $this->helper->getLastPostLimitedTextOfThreadFromDB($thread['id'], 250);
                        $tooltip = preg_replace('/\[[^\[\]]*\]/i', '', $tooltip);
                        break;
                    case "threadtitle":
                        $tooltip = $thread['name'];
                        break;
                    case "threadbody":
                        $tooltip = $thread['threaddesc'];
                        break;
                    case "disabled":
                        $tooltip = false;
                        break;
                    default:
                        $tooltip = $thread['threaddesc'];
                        break;
                }

                if (strlen($tooltip) >= 245) {
                    $tooltip = substr($tooltip, 0, strrpos($tooltip, ' '));
                    $tooltip .= ' [...]';
                }


                $plainHtmlData = false;
                if ($this->plainhtml) {
                    // for search engines: only show threadnames
                    $plainHtmlData .= $this->helper->checkThreadname($thread['name']) . '<br/>';
                } else {
                    $data['aaData'][] = array(
                        $threadAction,
                        $this->helper->checkThreadname($thread['name']),
                        $lastUsername,
                        $this->helper->getDateTimeString($lastPost),
                        $lastPost,
                        // hidden column for sorting
                        $thread['username'],
                        $this->helper->getDateTimeString($thread['creation']),
                        $thread['creation'],
                        // hidden column for sorting
                        $thread['posts'],
                        $thread['sort'],
                        // hidden column for sorting
                        999 - $thread['sort'],
                        // hidden column for sorting
                        $tooltip
                    );    // hidden column for tooltip
                }
            }

            $buttons = $this->addDefaultButtons(array(), $id);
            $buttons = $this->addForumButtons($buttons, $id);

            $return = array(
                "contenttype"    => "datatable",
                "contentdata"    => $data,
                "contentoptions" => array(
                    "actioncol"     => 0,
                    "tooltipcol"    => 11,
                    "selectOnHover" => true,
                    "clickAction"   => true
                ),
                "state"          => "forum:" . $id,
                "breadcrumb"     => $this->getBreadcrumb($id),
                "headline"       => $this->getHeadline($forum['headline']),
                "buttons"        => $buttons
            );
            if ($plainHtmlData) {
                $return['plainhtml'] = $plainHtmlData;
            }
            if ($forum['pretext']) {
                $return['precontent'] = $this->replaceInsertTags($forum['pretext']);
                if ($this->plainhtml) {
                    $return['metaDescription'] = $this->prepareMetaDescription($return['precontent']);
                }
            }
            if ($forum['posttext']) {
                $return['postcontent'] = $this->replaceInsertTags($forum['posttext']);
            }

            if ($forumTree) {
                if ($this->c4g_forum_comf_navigation == 'TREE') {
                    $return['treedata'] = $this->getForumTree($id, 0);
                }
            }

            return $return;
        }


        /**
         * Generate tree data of forums for items in a jQuery-dynatree
         *
         * @param int     $pid      - ID of parent forum
         * @param boolean $actForum - ID of active forum (is automatically activated)
         *
         * @return array
         */
        public function getForums($pid, $actForum)
        {

            $return = array();
            $forums = $this->helper->getForumsFromDB($pid);
            if (count($forums) == 0) {

                return array(
                    "breadcrumb"     => $this->getBreadcrumb($pid),
                    "contenttype"    => "html",
                    "contentoptions" => array("scrollable" => false),
                    "contentdata"    => sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['NO_ACTIVE_FORUMS'], $pid)
                );

            }

            foreach ($forums AS $forum) {
                if ($forum['subforums'] > 0) {
                    $children = $this->getForums($forum['id'], $actForum);
                } else {
                    $children = array();
                }

                $expand = (sizeOf($children) > 0);

                if ($forum['use_intropage'] && (!$this->c4g_forum_hide_intropages)) {
                    $action = 'forumintro';
                } else {
                    if ($forum['subforums'] == 0) {
                        $action = 'forum';
                    } else {
                        $action = 'forumbox';
                    }
                }
                $row = array(
                    "title"    => $forum['name'] . ' (' . $forum['threads'] . ')',
                    "key"      => $action . ':' . $forum['id'],
                    "isFolder" => true,
                    "children" => $children,
                    "expand"   => $expand,
                    "tooltip"  => nl2br(str_replace("'", '', C4GUtils::secure_ugc($forum['description'])))
                );
                if ($forum['id'] == $actForum) {
                    $row['activate'] = true;
                }
                if ($forum['linkurl'] != '') {
                    $row['href'] = $this->getForumLink($forum);
                    if ($forum['link_newwindow']) {
                        $row['href_newwindow'] = true;
                    }
                }
                $return[] = $row;
            }

            return $return;

        }


        /**
         * Generate tree data of forums in a jQuery-dynatree
         *
         * @param int $actForum - ID of active forum (is automatically activated)
         *
         * @return array
         */
        public function getForumTree($actForum)
        {

            $children = $this->getForums($this->c4g_forum_startforum, $actForum);

            $treedata = array(
                "children"        => $children,
                "clickFolderMode" => 1,
                "autoCollapse"    => false,
                "classNames"      => array("title" => "dynatree-title c4gGuiTooltip"),
                "fx"              => array(
                    "height"   => "toggle",
                    "duration" => 200
                )
            );

            return $treedata;
        }


        /**
         * Get initial jQuery Dynatree including buttons
         */
        public function generateForumTree()
        {

            $return = array(
                "treedata" => $this->getForumTree(0),
                "buttons"  => $this->addDefaultButtons(array(), 0)
            );

            return $return;
        }


        /**
         * @param $thread
         *
         * @return string
         */
        public function generateThreadHeaderAsHtml($thread)
        {

            if ($thread['threaddesc'] != '') {
                if ($this->c4g_forum_posts_jqui) {
                    $data = '<div class="c4gForumThreadHeader c4gGuiAccordion ui-widget ui-widget-header ui-corner-all">';
                    $data .= '<h3><a href="#">' . $GLOBALS['TL_LANG']['C4G_FORUM']['THREADDESC'] . '</a></h3>';
                    $data .= '<div class="c4gForumThreadHeaderDesc">' .
                             $thread['threaddesc'] .
                             '</div>';
                    $data .= '</div>';

                    return $data;
                } else {
                    $data = '<div class="c4gForumThreadHeader c4gForumThreadHeaderNoJqui">';
                    $data .= '<h2>' . $GLOBALS['TL_LANG']['C4G_FORUM']['THREADDESC'] . '</h2>';
                    $data .= '<div class="c4gForumThreadHeaderDesc">' .
                             $thread['threaddesc'] .
                             '</div>';
                    $data .= '</div><hr>';

                    return $data;

                }

            } else {
                return '';
            }

        }


        /**
         *
         * Generate a given post as HTML
         *
         * @param      $post
         * @param      $singlePost
         * @param bool $preview
         *
         * @return string
         */
        public function generatePostAsHtml($post, $singlePost, $preview = false)
        {

            if (!empty($post['tags'])) {
                $post['tags'] = explode(", ",$post['tags']);
            }

            //$collapse = $this->c4g_forum_collapsible_posts;
            $last  = false;
            $first = false;
            $targetClass        = '';
            $triggerClass       = '';
            $triggerTargetClass = '';
            $hideClass          = '';
            switch ($this->c4g_forum_collapsible_posts) {
                case 'CL':
                    $last = true;
                    break;
                case 'CF':
                    if (!$last && $post['post_number'] == 1) {
                        $first = true;
                    } elseif ($last && !($post['post_number'] == $post['posts'])) {
                        $last = false;
                    }
                    break;
                case 'CC':
                    $hideClass = ' c4gGuiCollapsible_hide';
                case 'CO':
                    $targetClass        = ' c4gGuiCollapsible_target';
                    $triggerClass       = ' c4gGuiCollapsible_trigger';
                    $triggerTargetClass = ' c4gGuiCollapsible_trigger_target';
                    break;
                default:
                    break;
            }
            if (!$last && !$first) {
                $targetClass .= $hideClass;
                $triggerTargetClass .= $hideClass;
            }

            if ($this->c4g_forum_posts_jqui) {
                $divClass     = " ui-widget ui-widget-header ui-corner-top";
                $linkClass    = " c4gGuiButton";
                $mainDivClass = "c4gForumPost";
            } else {
                $divClass     = " c4gForumPostHeaderNoJqui";
                $linkClass    = "";
                $mainDivClass = "c4gForumPost c4gForumPostNoJqui";
            }
            $data = '<div class="' . $mainDivClass . '"><div class="c4gForumPostHeader' . $divClass . $triggerClass . '">';
            if ($singlePost) {
                if ($post['post_number'] > 1) {
                    $actionFirst = 'readpostnumber:' . $post['threadid'] . ':1;usedialog:post' . $post['id'];
                    $actionPrev  = 'readpostnumber:' . $post['threadid'] . ':' . ($post['post_number'] - 1) . ';usedialog:post' . $post['id'];
                    $addClass    = "";
                    $span        = false;
                } else {
                    $actionFirst = "";
                    $actionPrev  = "";
                    $addClass    = " c4gGuiButtonDisabled";
                    $span        = ($this->c4g_forum_posts_jqui == false);
                }
                if ($span) {
                    $data .=
                        '<span>&lt;&lt;</span>' .
                        '<span>&lt;</span>';
                } else {
                    $data .=
                        '<a href="#" data-action="' . $actionFirst . '" class="c4gGuiAction' . $linkClass . $addClass . '">&lt;&lt;</a>' .
                        '<a href="#" data-action="' . $actionPrev . '" class="c4gGuiAction' . $linkClass . $addClass . '">&lt;</a>';
                }
            }

            if (!$preview) {
                $data .= '<span class="c4g_forum_post_head_postcount_row">' . sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['POST_HEADER_COUNT'], 'class=c4g_forum_post_head_postcount_number', $post['post_number'], 'class=c4g_forum_post_head_postcount_count', $post['posts']) . '</span>';
            }

            if ((!$preview) && (!$singlePost)) {
                // change buttons for post
                $act = $this->getChangeActionsForPost($post);
                foreach ($act as $key => $value) {
                    $data .= '<a href="#" data-action="' . $key . '" class="c4gForumPostHeaderChangeButton c4gGuiAction' . $linkClass . $triggerTargetClass . '">' . $value . '</a>';
                }
            }
            $act = $this->getViewActionsForPost($post);
            foreach ($act as $key => $value) {
                $data .= '<a href="#" data-action="' . $key . '" class="c4gForumPostHeaderViewButton c4gGuiAction' . $linkClass . $triggerTargetClass . '">' . $value . '</a>';
            }

            if ($singlePost) {
                if ($post['post_number'] < $post['posts']) {
                    $actionLast = 'readpostnumber:' . $post['threadid'] . ':' . $post['posts'] . ';usedialog:post' . $post['id'];
                    $actionNext = 'readpostnumber:' . $post['threadid'] . ':' . ($post['post_number'] + 1) . ';usedialog:post' . $post['id'];
                    $addClass   = "";
                    $span       = false;
                } else {
                    $actionLast = "";
                    $actionNext = "";
                    $addClass   = " c4gGuiButtonDisabled";
                    $span       = ($this->c4g_forum_posts_jqui == false);
                }

                if ($span) {
                    $data .=
                        '<span>&gt;</span>' .
                        '<span>&gt;&gt;</span>';
                } else {
                    $data .=
                        '<a href="#" data-action="' . $actionNext . '" class="c4gGuiAction' . $linkClass . $addClass . '">&gt;</a>' .
                        '<a href="#" data-action="' . $actionLast . '" class="c4gGuiAction' . $linkClass . $addClass . '">&gt;&gt;</a>';
                }
                $data .=
                    '<a href="#" data-action="readthread:' . $post['threadid'] . ';usedialog:post' . $post['id'] .
                    '" class="c4gForumPostHeaderAll c4gGuiAction' . $linkClass . '">' . $GLOBALS['TL_LANG']['C4G_FORUM']['ALL_POSTS'] . '</a>';
            }

            if (!$this->plainhtml) {
                // show author only when not in plainhtml-mode (=pages that will be indexed by search engines)
                $data .= '<br><span class="c4g_forum_post_head_origin_row">' .
                         sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['POST_HEADER_CREATED'], 'class=c4g_forum_post_head_origin_author',
                                 $post['username'], 'class=c4g_forum_post_head_origin_datetime', $this->helper->getDateTimeString($post['creation'])) . '</span>';
            }
            $data .= '<br>' .
                     sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['POST_HEADER_SUBJECT'], 'class="c4g_forum_post_head_subject_pre"',
                             'class="c4g_forum_post_head_subject"', $post['subject']) . '<br>';

            if (($post['linkname'] != '') || ($post['linkurl'] != '')) {
                $linkname = $post['linkname'];
                $linkurl  = $post['linkurl'];

                if ($linkname == '') {
                    $linkurl = $linkname;
                }
                if ($linkurl == '') {
                    $linkname = $linkurl;
                }
                if ($post['link_newwindow']) {
                    $linkcode = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_HEADER_LINK_NEWWINDOW'];
                } else {
                    $linkcode = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_HEADER_LINK'];
                }
                //$data .= '<span class="c4g_forum_post_head_link' .$triggerTargetClass. '">'.sprintf($linkcode,$linkurl, $linkname).'</span><br>';
                $data .= '<span class="c4g_forum_post_head_link">' . sprintf($linkcode, $linkurl, $linkname) . '</span><br>';
            }
            if (!empty($post['tags'])) {
                $data .= '<span class="c4g_forum_post_head_tags">' . sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['POST_HEADER_TAGS'], implode(", ", $post['tags'])) . '</span><br>';
            }

            if ($this->c4g_forum_posts_jqui) {
                $divClass = " ui-widget ui-widget-content ui-corner-bottom";
            } else {
                $divClass = " c4gForumPostTextNoJqui";
            }

            $text = $post['text'];
            // Handle BBCodes, if activated
            if ($this->c4g_forum_bbcodes) {
                $divClass .= ' BBCode-Area';
                //$text = preg_replace('#<br? ?/>#', '', $text);
                //$text = $bbcode->Parse($text);
            }

            $data .=
                '</div>' .
                '<div class="c4gForumPostText' . $divClass . $targetClass . '">' .
                $text .
                '</div>';

            if ($post['edit_count']) {
                $data .=
                    '<div class="c4gForumPostText c4g_forum_post_head_edit_row' . $targetClass . '">' .
                    sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['POST_EDIT_INFO'], 'class="c4g_forum_post_head_edit_count"',
                            $post['edit_count'], 'class="c4g_forum_post_head_edit_datetime"', $this->helper->getDateTimeString($post['edit_last_time']),
                            'class="c4g_forum_post_head_edit_author"', $post['edit_username']) .
                    '</div>';
            }
            if (!$this->c4g_forum_posts_jqui) {
                $data .= '<hr>';
            }
            $data .=
                '</div>';

            return $data;
        }


        /**
         * @param $post
         *
         * @return array
         */
        public function getChangeActionsForPost($post)
        {

            $return = array();
            if ($post['authorid'] == $this->User->id) {
                $delAction  = 'delownpostdialog';
                $editAction = 'editownpostdialog';
            } else {
                $delAction  = 'delpostdialog';
                $editAction = 'editpostdialog';
            }
            if ($this->helper->checkPermissionForAction($post['forumid'], $delAction)) {
                $return[$delAction . ':' . $post['id']] = $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_POST'];
            }
            if ($this->helper->checkPermissionForAction($post['forumid'], $editAction)) {
                $return[$editAction . ':' . $post['id']] = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST'];
            }

            return $return;
        }


        /**
         * @param $post
         *
         * @return array
         */
        public function getViewActionsForPost($post)
        {

            $return = array();
            if (($post['loc_geox'] && $post['loc_geoy']) || $post['loc_data_content']) {
                if ($this->map_enabled() && $this->helper->checkPermissionForAction($post['forumid'], 'viewmapforpost')) {
                    $return['viewmapforpost:' . $post['id']] = $GLOBALS['TL_LANG']['C4G_FORUM']['VIEW_MAP_FOR_POST'];
                }
            }

            return $return;
        }


        /**
         * @param $id
         *
         * @return array
         */
        public function getPostAsHtml($id)
        {

            $posts  = $this->helper->getPostFromDB($id);
            $thread = $this->helper->getThreadFromDB($posts[0]['threadid']);
            $data   = $this->generateThreadHeaderAsHtml($thread);
            foreach ($posts as $post) {
                $data .= $this->generatePostAsHtml($post, true);
            }

            list($access, $message) = $this->checkPermission($post['forumid']);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }


            $dialogbuttons = array(
                array(
                    "action" => 'closedialog:post' . $id,
                    "type"   => 'get',
                    "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CLOSE']
                )
            );

            if ($this->helper->checkPermission($posts[0]['forumid'], 'newpost')) {
                array_insert($dialogbuttons, 0,
                             array(
                                 array(
                                     "action" => 'newpost:' . $posts[0]['threadid'] . ':post' . $id,
                                     "type"   => 'get',
                                     "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['NEW_POST']
                                 )
                             )
                );
            }

            // get edit and delete buttons
            $act = $this->getChangeActionsForPost($posts[0]);
            foreach ($act as $key => $value) {
                array_insert($dialogbuttons, 0,
                             array(
                                 array(
                                     "action" => $key,
                                     "type"   => 'get',
                                     "text"   => $value
                                 )
                             )
                );
            }

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array("title" => $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'] . ': ' . $posts[0]['threadname'])),
                "dialogid"      => 'post' . $id,
                "dialogstate"   => "forum:" . $posts[0]['forumid'] . ";readpost:" . $id,
                "dialogbuttons" => $dialogbuttons,

            );

            return $return;
        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function getLastPostOfThreadAsHtml($threadId)
        {

            $return = $this->getPostAsHtml($this->helper->getIdOfLastPostFromDB($threadId));

            return $return;
        }


        /**
         * @param $threadId
         * @param $postNumber
         *
         * @return array
         */
        public function getPostNumberOfThreadAsHtml($threadId, $postNumber)
        {

            $return = $this->getPostAsHtml($this->helper->getIdOfPostNumberFromDB($threadId, $postNumber));

            return $return;
        }


        /**
         * @param $id
         *
         * @return array
         */
        public function getThreadAsHtml($id)
        {

            $posts  = $this->helper->getPostsOfThreadFromDB($id, ($this->c4g_forum_postsort != 'UP'));
            $thread = $this->helper->getThreadFromDB($id);
            $data   = $this->generateThreadHeaderAsHtml($thread);
            foreach ($posts as $post) {
                $data .= $this->generatePostAsHtml($post, false);
            }

            list($access, $message) = $this->checkPermission($thread['forumid']);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $dialogbuttons = array(
                array(
                    "action" => 'closedialog:thread' . $id,
                    "type"   => 'get',
                    "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CLOSE']
                )
            );


            if (FE_USER_LOGGED_IN) {
                if ($this->helper->checkPermission($thread['forumid'], 'subscribethread')) {
                    $showButton = true;
                    if ($this->helper->checkPermission($thread['forumid'], 'subscribeforum')) {
                        if ($this->helper->subscription->getCompleteSubforumSubscriptionFromDB($thread['forumid'], $this->User->id)) {
                            // no thread subscription button when forum is already subscribed completely
                            $showButton = false;
                        }
                    }
                    if ($showButton) {
                        $subscriptionId = $this->helper->subscription->getThreadSubscriptionFromDB($id, $this->User->id);
                        if ($subscriptionId) {
                            $text = $GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_THREAD'];
                        } else {
                            $text = $GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIBE_THREAD'];
                        }
                        array_insert($dialogbuttons, 0,
                                     array(
                                         array(
                                             "action" => 'subscribethreaddialog:' . $id,
                                             "type"   => 'get',
                                             "text"   => $text
                                         )
                                     )
                        );
                    }

                }
            }

            if ($this->helper->checkPermission($thread['forumid'], 'newpost')) {
                array_insert($dialogbuttons, 0,
                             array(
                                 array(
                                     "action" => 'newpost:' . $id . ':thread' . $id,
                                     "type"   => 'get',
                                     "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['NEW_POST']
                                 )
                             )
                );
            }

            if ($this->helper->checkPermission($thread['forumid'], 'movethread')) {
                array_insert($dialogbuttons, 0,
                             array(
                                 array(
                                     "action" => 'movethreaddialog:' . $id,
                                     "type"   => 'get',
                                     "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD']
                                 )
                             )
                );
            }

            if ($this->helper->checkPermission($thread['forumid'], 'delthread')) {
                array_insert($dialogbuttons, 0,
                             array(
                                 array(
                                     "action" => 'delthreaddialog:' . $id,
                                     "type"   => 'get',
                                     "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_THREAD']
                                 )
                             )
                );
            }

            if ($post['threadauthor'] == $this->User->id) {
                $editAction = 'editownthreaddialog';
            } else {
                $editAction = 'editthreaddialog';
            }

            if ($this->helper->checkPermissionForAction($thread['forumid'], $editAction)) {
                array_insert($dialogbuttons, 0,
                             array(
                                 array(
                                     "action" => $editAction . ':' . $id,
                                     "type"   => 'get',
                                     "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_THREAD']
                                 )
                             )
                );
            }

            $return = array(
                "dialogstate"   => "forum:" . $thread['forumid'] . ";readthread:" . $id,
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogid"      => 'thread' . $id,
                "dialogbuttons" => $dialogbuttons,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" => $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'] . ': ' . $thread['name']
                                                                  ))
            );

            if ($this->plainhtml) {
                if ($thread['threaddesc']) {
                    $return['metaDescription'] = $this->prepareMetaDescription($thread['threaddesc']);
                } else {
                    if ($posts[0]) {
                        $return['metaDescription'] = $this->prepareMetaDescription($posts[0]['text']);
                    }
                }
            }

            return $return;
        }


        /**
         * @param int $forumId
         *
         * @return array
         */
        public function generateNewThreadForm($forumId)
        {

            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }
            $data = '<div class="c4gForumNewThread">' .
                    '<div class="c4gForumNewThreadName">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'] . ':<br/>' .
                    '<input name="thread" type="text" class="formdata ui-corner-all" size="80" maxlength="100" /><br />' .
                    '</div>';

            $data .= $this->getThreadDescForForm('c4gForumNewThreadDesc', $forumId, 'newthread', '');
            $data .= $this->getThreadSortForForm('c4gForumNewThreadSort', $forumId, 'newthread', '999');
            $editorId = '';

            if ($this->c4g_forum_editor === "bb") {
                $editorId = ' id="editor"';
            }elseif ($this->c4g_forum_editor === "ck") {
                $editorId = ' id="ckeditor"';
            }else{
                $editorId = '';
            }

            $aPost = array(
                "forumid" => $forumId,
                "tags" => array()
            );

            $sServerName = \Environment::get("serverName");
            $sHttps      = \Environment::get("https");
            $path        = \Environment::get("path");
            $sProtocol = !empty($sHttps) ? 'https://' : 'http://';
            $sSite     = $sProtocol . $sServerName . $path;
            if(substr($sSite,-1,1) != "/"){
                $sSite .= "/";
            }


            $data .= $this->getTagForm('c4gForumNewThreadPostTags', $aPost, 'newthread');
            $data .= '<div class="c4gForumNewThreadContent">' .
                     $GLOBALS['TL_LANG']['C4G_FORUM']['POST'] . ':<br/>' .
                     '<input type="hidden" name="uploadEnv" value="'.$sSite.'">' .
                     '<input type="hidden" name="uploadPath" value="' . $this->c4g_forum_bbcodes_editor_imguploadpath . '">' .
                     '<textarea' . $editorId . ' name="post" cols="80" rows="15" class="formdata ui-corner-all"></textarea><br/>' .
                     '</div>';
            $data .= $this->getPostlinkForForm('c4gForumNewThreadPostLink', $forumId, 'newthread', '', '');
            $data .= $this->getPostMapEntryForForm('c4gForumNewThreadMapData', $forumId, 'newthread', '', '', '', '', '', '', '','');

            $data .= '</div>';

            $return = array(
                "dialogtype"    => "form",
                "dialogid"      => "newthread",
                "dialogstate"   => "forum:" . $forumId . ";newthread:" . $forumId,
                "dialogdata"    => $data,
                "dialogbuttons" => array(
                    array(
                        "action" => 'sendthread:' . $forumId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SEND']
                    ),
                    array(
                        "action" => 'previewthread:' . $forumId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['PREVIEW']
                    ),
                    array(
                        "action" => 'cancelthread:' . $forumId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" =>
                                                                          sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['NEW_THREAD_TITLE'], $this->helper->getForumNameFromDB($forumId)),
                                                                      "modal" => true
                                                                  ))
            );

            return $return;
        }


        /**
         * @param int $threadId
         * @param $parentDialog
         *
         * @return array
         */
        public function generateNewPostForm($threadId, $parentDialog)
        {

            $thread = $this->helper->getThreadAndForumNameFromDB($threadId);

            $sLastPost = "";
            if($this->c4g_forum_show_last_post_on_new) {
                $posts  = $this->helper->getPostsOfThreadFromDB($threadId, true);
                if (!empty($posts)) {
                    $aPost     = $posts[0];
                    $sLastPost = "<h3>" . $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_POST'] . "</h3>";
                    $sLastPost .= $this->generatePostAsHtml($aPost, false, true);
                    $sLastPost .= "<br>";
                    $sLastPost .= "<h3>" . $GLOBALS['TL_LANG']['C4G_FORUM']['NEW_POST'] . "</h3>";
                }
            }



            list($access, $message) = $this->checkPermission($thread['forumid']);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }
            $editorId = '';
            if ($this->c4g_forum_editor === "bb") {
                $editorId = ' id="editor"';
            }elseif ($this->c4g_forum_editor === "ck") {
                $editorId = ' id="ckeditor"';
            }else{
                $editorId = '';
            }

            $aPost = array(
                "forumid" => $thread['forumid'],
                "tags" => array()
            );

            $sServerName = \Environment::get("serverName");
            $sHttps      = \Environment::get("https");
            $path        = \Environment::get("path");
            $sProtocol = !empty($sHttps) ? 'https://' : 'http://';
            $sSite     = $sProtocol . $sServerName . $path;
            if(substr($sSite,-1,1) != "/"){
                $sSite .= "/";
            }

            $data = $sLastPost;

            $data .= '<div class="c4gForumNewPost">' .
                    '<div class="c4gForumNewPostSubject">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['SUBJECT'] . ':<br/>' .
                    '<input name="subject" value="' . $thread['threadname'] . '" type="text" class="formdata ui-corner-all" size="80" maxlength="100" /><br />' .
                    '</div>';
            $data .= $this->getTagForm('c4gForumNewPostPostTags', $aPost, 'newpost');
            $data .='<div class="c4gForumNewPostContent">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['POST'] . ':<br/>' .
                    '<input type="hidden" name="uploadEnv" value="'.$sSite.'">' .
                    '<input type="hidden" name="uploadPath" value="' . $this->c4g_forum_bbcodes_editor_imguploadpath . '">' .
                    '<textarea' . $editorId . ' name="post" cols="80" rows="15" class="formdata ui-corner-all"></textarea>' .
                    '</div>';

            $data .= $this->getPostlinkForForm('c4gForumNewPostPostLink', $thread['forumid'], 'newpost', '', '');
            $locstyle = "";
            if ($this->map_enabled()) {
                $locstyle = $this->helper->getDefaultLocstyleFromDB($threadId);
            }
            $data .= $this->getPostMapEntryForForm('c4gForumNewPostMapData', $thread['forumid'], 'newpost', '', '', '', $locstyle, '', '', 0, '');

            $data .=
                '<input name="parentDialog" type="hidden" class="formdata" value="' . $parentDialog . '"></input>' .
                '</div>';


            $return = array(
                "dialogtype"    => "form",
                "dialogid"      => "newpost",
                "dialogdata"    => $data,
                "dialogstate"   => "forum:" . $thread['forumid'] . ";newpost:" . $threadId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'sendpost:' . $threadId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SEND']
                    ),
                    array(
                        "action" => 'previewpost:' . $threadId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['PREVIEW']
                    ),
                    array(
                        "action" => 'cancelpost:' . $threadId . ':newpost',
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" => sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['NEW_POST_TITLE'], $thread['threadname'], $thread['forumname']),
                                                                      "modal" => true
                                                                  ))
            );

            return $return;
        }


        /**
         * @param int $threadId
         *
         * @return array
         * @throws \Exception
         */
        public function sendPost($threadId)
        {

            $forumId = $this->helper->getForumIdForThread($threadId);
            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }
            if (!$this->putVars['post']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_MISSING'];

                return $return;
            }
            if (!$this->putVars['subject']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['SUBJECT_MISSING'];

                return $return;
            }
            $this->putVars['osmId'] = $this->putVars['osmIdType'] . '.' . $this->putVars['osmId'];
            $result                 = $this->helper->insertPostIntoDB($threadId, $this->User->id, $this->putVars['subject'], $this->putVars['post'], $this->putVars['tags'],
                                                                      $this->putVars['linkname'], $this->putVars['linkurl'], $this->putVars['geox'], $this->putVars['geoy'],
                                                                      $this->putVars['locstyle'], $this->putVars['label'], $this->putVars['tooltip'], $this->putVars['geodata'], $this->putVars['osmId']);

            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['ERROR_SAVE_POST'];
            } else {
                $return = $this->getForumInTable($result['forum_id'], true);
                //$return ['dialogclose'] = array("newpost", $this->putVars['parentDialog']);
                $return ['dialogclose'] = "newpost";
                if ($this->c4g_forum_threadclick == 'THREAD') {
                    $return ['performaction'] = "readthread:" . $threadId;
                } else {
                    $return ['performaction'] = "readpost:" . $result['post_id'];
                }

                $threadSubscribers = $this->helper->subscription->getThreadSubscribersFromDB($threadId);
                $forumSubscribers  = $this->helper->subscription->getForumSubscribersFromDB($forumId, 0);
                if ($threadSubscribers || $forumSubscribers) {
                    $this->helper->subscription->MailCache ['subject']  = $this->putVars['subject'];
                    $this->helper->subscription->MailCache ['post']     = $this->putVars['post'];
                    $this->helper->subscription->MailCache ['linkname'] = $this->putVars['linkname'];
                    $this->helper->subscription->MailCache ['linkurl']  = $this->putVars['linkurl'];
                    $cronjob                                            = $this->helper->subscription->sendSubscriptionEMail(
                        array_merge($threadSubscribers, $forumSubscribers), $threadId, 'new');
                    if ($cronjob) {
                        $return['cronexec'] = $cronjob;
                    }

                }

                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['SUCCESS_SAVE_POST'];
            }

            return $return;

        }


        /**
         * @param $threadId
         * @param $title
         *
         * @return array
         */
        public function previewPost($threadId, $title)
        {

            list($access, $message) = $this->checkPermission($this->helper->getForumIdForThread($threadId));
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            if (!$this->putVars['post']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_MISSING'];

                return $return;
            }


            $post             = array();
            $post['username'] = $this->User->username;
            $post['creation'] = time();
            $post['subject']  = nl2br(C4GUtils::secure_ugc($this->putVars['subject']));
            $post['text']     = nl2br(C4GUtils::secure_ugc($this->putVars['post']));
            $post['linkname'] = C4GUtils::secure_ugc($this->putVars['linkname']);
            $post['linkurl']  = C4GUtils::secure_ugc($this->putVars['linkurl']);
            $data             = $this->generatePostAsHtml($post, false, true);

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array("title" => $title)),
                "dialogid"      => 'previewpost',
                "dialogbuttons" => array(
                    array(
                        "action" => 'closedialog:previewpost',
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CLOSE']
                    )
                ),
            );

            return $return;
        }


        /**
         * @param $postId
         *
         * @return array
         */
        public function previewEditPost($postId)
        {

            $posts = $this->helper->getPostFromDB($postId);

            return $this->previewPost($posts[0]['threadid'], $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST_PREVIEW']);
        }


        /**
         * @param $threadId
         * @param $close
         *
         * @return array
         */
        public function cancelPost($threadId, $close)
        {

            //$close = preg_replace('/-/', ':', $close);

            $return = array(
                "dialogclose"   => array(
                    "readthread:" . $threadId,
                    $close
                ),
                "performaction" => "readthread:" . $threadId
            );

            return $return;
        }


        /**
         * @param $forumId
         *
         * @return array
         * @throws \Exception
         */
        public function sendThread($forumId)
        {

            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }
            if (!$this->putVars['thread']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['THREADNAME_MISSING'];

                return $return;
            }
            if (!$this->putVars['post']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_MISSING'];

                return $return;
            }

            if ($this->helper->checkPermission($forumId, 'threadsort')) {
                if (!isset($this->putVars['sort'])) {
                    $sort = 999;
                } else {
                    $sort = $this->putVars['sort'];
                }
            } else {
                $sort = 999;
            }

            if ($this->helper->checkPermission($forumId, 'threaddesc')) {
                $threaddesc = $this->putVars['threaddesc'];
            } else {
                $threaddesc = '';
            }

            $result = $this->helper->insertThreadIntoDB($forumId, $this->putVars['thread'], $this->User->id, $threaddesc, $sort, $this->putVars['post'],$this->putVars['tags'],
                                                        $this->putVars['linkname'], $this->putVars['linkurl'], $this->putVars['geox'], $this->putVars['geoy'], $this->putVars['locstyle'],
                                                        $this->putVars['label'], $this->putVars['tooltip'], $this->putVars['geodata'], $this->putVars['osmId']);

            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['ERROR_SAVE_THREAD'];
            } else {
                $return                   = $this->getForumInTable($forumId, true);
                $return ['dialogclose']   = "newthread";
                $return ['performaction'] = "readthread:" . $result['thread_id'];
                $return ['usermessage']   = $GLOBALS['TL_LANG']['C4G_FORUM']['SUCCESS_SAVE_THREAD'];

                $forumSubscribers = $this->helper->subscription->getForumSubscribersFromDB($forumId, 1);
                if ($forumSubscribers) {
                    $this->helper->subscription->MailCache ['subject']  = $this->putVars['subject'];
                    $this->helper->subscription->MailCache ['post']     = $this->putVars['post'];
                    $this->helper->subscription->MailCache ['linkname'] = $this->putVars['linkname'];
                    $this->helper->subscription->MailCache ['linkurl']  = $this->putVars['linkurl'];
                    $cronjob                                            =
                        $this->helper->subscription->sendSubscriptionEMail($forumSubscribers, $result['thread_id'], 'newThread');
                    if ($cronjob) {
                        $return['cronexec'][] = $cronjob;
                    }
                }

                $sitemapJob = $this->helper->generateSitemapCronjob($this, $forumId);
                if ($sitemapJob) {
                    $return['cronexec'][] = $sitemapJob;
                }

            }

            return $return;
        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function previewThread($forumId)
        {

            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }
            if (!$this->putVars['thread']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['THREADNAME_MISSING'];

                return $return;
            }
            if (!$this->putVars['post']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_MISSING'];

                return $return;
            }

            $thread               = array();
            $thread['threaddesc'] = $this->putVars['threaddesc'];
            $data                 = $this->generateThreadHeaderAsHtml($thread);

            $post             = array();
            $post['username'] = $this->User->username;
            $post['creation'] = time();
            $post['subject']  = nl2br(C4GUtils::secure_ugc($this->putVars['thread']));
            $post['tags']     = nl2br(C4GUtils::secure_ugc($this->putVars['tags']));
            $post['text']     = nl2br(C4GUtils::secure_ugc($this->putVars['post']));
            $post['linkname'] = C4GUtils::secure_ugc($this->putVars['linkname']);
            $post['linkurl']  = C4GUtils::secure_ugc($this->putVars['linkurl']);
            $data .= $this->generatePostAsHtml($post, false, true);

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array("title" => $GLOBALS['TL_LANG']['C4G_FORUM']['NEW_THREAD'] . ': ' . C4GUtils::secure_ugc($this->putVars['thread']))),
                "dialogid"      => 'previewthread',
                "dialogbuttons" => array(
                    array(
                        "action" => 'closedialog:previewthread',
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CLOSE']
                    )
                ),
            );


            return $return;
        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function cancelThread($forumId)
        {

            $return = array(
                "dialogclose" => "newthread"
            );

            return $return;
        }


        /**
         * @param $dialogId
         *
         * @return array
         */
        public function closeDialog($dialogId)
        {

            $return = array(
                "dialogclose" => $dialogId
            );

            return $return;
        }


        /**
         * @param $dialogId
         *
         * @return array
         */
        public function useDialog($dialogId)
        {

            $return = array(
                "usedialog" => $dialogId
            );

            return $return;
        }


        /**
         * @param $parentId
         *
         * @return array
         */
        public function getForumInBoxes($parentId)
        {

            $forums = $this->helper->getForumsFromDB($parentId);
            if (count($forums) == 0) {

                return array(
                    "breadcrumb"     => $this->getBreadcrumb($parentId),
                    "contenttype"    => "html",
                    "contentoptions" => array("scrollable" => false),
                    "contentdata"    => sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['NO_ACTIVE_FORUMS'], $parentId)
                );

            }
            $addClass = "";
            if ($this->c4g_forum_boxes_center) {
                $addClass = " c4gGuiCenterDiv";
            }
            $data = '<div class="c4gForumBoxes' . $addClass . '">';
            foreach ($forums as $forum) {
                if ($forum['linkurl'] != '') {
                    $action = "";
                    $href   = ' data-href="' . $this->getForumLink($forum) . '"';
                    if ($forum['link_newwindow']) {
                        $href .= ' data-href_newwindow="1"';
                    }
                } else {
                    $href = "";
                    if (($forum['use_intropage']) && (!$this->c4g_forum_hide_intropages)) {
                        $action = "forumintro:" . $forum['id'];
                    } else {
                        if ($forum['subforums'] > 0) {
                            $action = "forumbox:" . $forum['id'];
                        } else {
                            $action = "forum:" . $forum['id'];
                        }
                    }
                }
                $divId = "c4gForumBox" . $forum['id'];

                $divClass = "c4gForumBox c4gGuiAction c4gGuiTooltip ";
                if ($this->c4g_forum_boxes_jqui_layout) {
                    $divClass .= " ui-widget";
                }
// TODO

                $objFile               = FilesModel::findByUuid($forum['box_imagesrc']);
                $forum['box_imagesrc'] = $objFile->path;

                if ($forum['box_imagesrc']) { // check if bin is empty!!!!
                    $divClass .= " c4gForumBoxWithImage";
                    $hoverClass = "c4gForumBoxWithImageHover";
                } else {
                    $divClass .= " c4gForumBoxWithoutImage";
                    $hoverClass = "c4gForumBoxHover";
                    if ($this->c4g_forum_boxes_jqui_layout) {
                        $divClass .= " ui-state-default ui-corner-all";
                        $hoverClass .= " ui-state-hover";
                    } else {
                        $divClass .= " c4gForumBoxNoJqui";
                    }
                }
                $data .= '<div class="' . $divClass . '" id="' . $divId . '" title="' . nl2br(C4GUtils::secure_ugc($forum['description'])) . '" data-action="' . $action . '" data-hoverclass="' . $hoverClass . '"' . $href . '>';
                $break = false;
// TODO
                if ($forum['box_imagesrc']) { // check if bin is empty !!!!
                    $imgClass = "c4gForumBoxImage";
                    if ($this->c4g_forum_boxes_jqui_layout) {
                        $imgClass .= " ui-corner-all";
                    }
                    /*if (version_compare(VERSION, '3.2', '>=')) {
					// Contao 3.2.x Format


				} else if (is_numeric($forum['box_imagesrc'])) {
					// Contao 3.x Format
					$objFile = FilesModel::findByPk($forum['box_imagesrc']);
					$forum['box_imagesrc'] = $objFile->path;
				}*/
                    $data .= '<img src="' . $forum['box_imagesrc'] . '" class="' . $imgClass . '" alt="' . $forum['name'] . '">';

                }
                if ($this->c4g_forum_boxes_text) {
                    $data .= '<div class="c4gForumBoxText">' . $forum['name'] . '</div>';
                }
                if ($forum['subforums'] > 0) {

                    if ($this->c4g_forum_boxes_subtext) {
                        $data .= '<div class="c4gForumBoxSubtext">';
                        if ($forum['subforums'] == 1) {
                            $data .= $forum['subforums'] . ' ' . $GLOBALS['TL_LANG']['C4G_FORUM']['SUBFORUM'];
                        } else {
                            $data .= $forum['subforums'] . ' ' . $GLOBALS['TL_LANG']['C4G_FORUM']['SUBFORUMS'];
                        }
                        $data .= '</div>';
                    }

                } else {

                    if ($this->c4g_forum_boxes_subtext) {
                        $data .= '<div class="c4gForumBoxSubtext">';
                        if ($forum['threads'] > 0) {
                            $data .= $forum['threads'] . ' ';
                            if ($forum['threads'] == 1) {
                                $data .= $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'];
                            } else {
                                $data .= $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS'];
                            }
                        }
                        if ($forum['posts'] > 0) {
                            $data .= '<br>' . $forum['posts'] . ' ';
                            if ($forum['posts'] == 1) {
                                $data .= $GLOBALS['TL_LANG']['C4G_FORUM']['POST'];
                            } else {
                                $data .= $GLOBALS['TL_LANG']['C4G_FORUM']['POSTS'];
                            }
                        }
                        $data .= '</div>';
                    }

                    if (($forum['posts'] > 0) && ($this->c4g_forum_boxes_lastpost)) {
                        $data .=
                            '<div class="c4gForumBoxLastPost">' .
                            sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['BOX_LAST_POST'],
                                    '<span class="c4gForumBoxLastDate">' . $this->helper->getDateTimeString($forum['last_post_creation']) . '</span>',
                                    '<span class="c4gForumBoxLastAuthor">' . $forum['last_username'] . '</span>',
                                    '<span class="c4gForumBoxLastThread">' . $this->helper->checkThreadname($forum['last_threadname']) . '</span>') .
                            '</div>';
                    }
                }


                $data .= '</div>';

            }
            $data .= '</div>';

            $buttons = array();
            if ($this->map_enabled() && $this->helper->checkPermission($parentId, 'mapview')) {
                $forum = $this->helper->getForumFromDB($parentId);
                if ($forum['enable_maps'] || $forum['enable_maps_inherited']) {
                    array_insert($buttons, 0, array(
                        array(
                            "id"   => 'viewmapforforum:' . $parentId,
                            "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['VIEW_MAP_FOR_FORUM']
                        )
                    ));
                }
            }

            $return = array(
                "contenttype"    => "html",
                "contentoptions" => array("scrollable" => false),
                "contentdata"    => $data,
                "state"          => "forumbox:" . $parentId,
                "buttons"        => $this->addDefaultButtons($buttons, $parentId),
                "breadcrumb"     => $this->getBreadcrumb($parentId),
            );

            $parentForum = $this->helper->getForumFromDB($parentId);
            if ($parentForum['pretext']) {
                $return['precontent'] = $this->replaceInsertTags($parentForum['pretext']);
            }
            if ($parentForum['posttext']) {
                $return['postcontent'] = $this->replaceInsertTags($parentForum['posttext']);
            }
            $return['headline'] = $this->getHeadline($parentForum['headline']);

            return $return;
        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function getForumintro($forumId)
        {

            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }
            $forums = $this->helper->getForumsFromDB($forumId, false, false, 'id');
            $forum  = $forums[0];
            $data   = '<div class="c4gForumIntropage">';
            $data .= $this->replaceInsertTags($forum['intropage']);
            if ($forum['intropage_forumbtn'] != '') {
                if ($forum['subforums'] > 0) {
                    $action = "forumbox:" . $forumId;
                } else {
                    $action = "forum:" . $forumId;
                }
                $class = 'c4gGuiAction';
                if ($forum['intropage_forumbtn_jqui']) {
                    $class .= ' c4gGuiButton';
                }
                $data .= '<a href="#" data-action="' . $action . '" class="' . $class . '">' . specialchars($forum['intropage_forumbtn']) . '</a>';
            }
            $data .= '</div>';
            $return = array(
                "contenttype" => "html",
                "contentdata" => $data,
                "state"       => "forumintro:" . $forumId,
                "buttons"     => $this->addDefaultButtons(array(), $forumId),
                "breadcrumb"  => $this->getBreadcrumb($forumId),
                "headline"    => $this->getHeadline($forum['headline'])
            );

            if ($this->c4g_forum_comf_navigation == 'TREE') {
                $return['treedata'] = $this->getForumTree($forumId, 0);
            }

            return $return;
        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function delThread($threadId)
        {

            $forumId = $this->helper->getForumIdForThread($threadId);
            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $threadSubscribers = $this->helper->subscription->getThreadSubscribersFromDB($threadId);
            $forumSubscribers  = $this->helper->subscription->getForumSubscribersFromDB($forumId, 1);


            if ($threadSubscribers || $forumSubscribers) {
                $cronexec =
                    $this->helper->subscription->sendSubscriptionEMail(
                        array_merge($threadSubscribers, $forumSubscribers), $threadId, 'delThread');
            }
            $result = $this->helper->deleteThreadFromDB($threadId);
            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_THREAD_ERROR'];
            } else {
                $return                 = $this->getForumInTable($forumId, true);
                $return ['dialogclose'] = array(
                    "delthread" . $threadId,
                    "thread" . $threadId
                );
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_THREAD_SUCCESS'];
                if ($cronexec) {
                    $return['cronexec'][] = $cronexec;
                }

                $sitemapJob = $this->helper->generateSitemapCronjob($this, $forumId);
                if ($sitemapJob) {
                    $return['cronexec'][] = $sitemapJob;
                }

            }

            return $return;
        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function delThreadDialog($threadId)
        {

            list($access, $message) = $this->checkPermission($this->helper->getForumIdForThread($threadId));
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $thread = $this->helper->getThreadAndForumNameFromDB($threadId);
            $data   = sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['DEL_THREAD_WARNING'], $thread['threadname'], $thread['forumname']);

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_THREAD'],
                                                                      "height" => 200,
                                                                      "modal"  => true
                                                                  )),
                "dialogid"      => 'delthread' . $threadId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'delthread:' . $threadId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_THREAD']
                    ),
                    array(
                        "action" => 'closedialog:delthread' . $threadId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function subscribeSubforumDialog($forumId)
        {

            list ($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $subscriptionId = $this->helper->subscription->getSubforumSubscriptionFromDB($forumId, $this->User->id);

            if ($subscriptionId) {
                $dialogData = sprintf($GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_SUBSCRIPTION_CANCEL'], $this->helper->getForumNameFromDB($forumId));
                $buttonTxt  = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_CANCEL'];
                $title      = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['UNSUBSCRIBE_SUBFORUM'];
            } else {
                $dialogData = sprintf($GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_TEXT'], $this->helper->getForumNameFromDB($forumId));
                $buttonTxt  = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIBE_SUBFORUM'];

                $dialogData .= '<div>' . '<input id="c4gForumSubscriptionForumOnlyThreads"  type="checkbox" name="subscription_only_threads" class="formdata" />' . '<label for="c4gForumSubscriptionForumOnlyThreads">' .
                               $GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_SUBFORUM_ONLY_THREADS'] . '</label>' . '</div>';
                $title = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIBE_SUBFORUM'];
            }

            $dialogbuttons = array();

            $dialogbuttons [] = array(
                "action" => 'subscribesubforum:' . $forumId . ':' . $subscriptionId,
                "type"   => 'send',
                "text"   => $buttonTxt
            );

            $dialogbuttons [] = array(
                "action" => 'closedialog:subscribesubforum' . $forumId,
                "type"   => 'get',
                "text"   => $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['CANCEL']
            );

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $dialogData,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $title,
                                                                      "height" => 200,
                                                                      "modal"  => true
                                                                  )),
                "dialogstate"   => "forum:" . $forumId . ";subscriptionsubforumdialog:" . $forumId,
                "dialogid"      => 'subscribesubforum' . $forumId,
                "dialogbuttons" => $dialogbuttons
            );

            return $return;

        }


        /**
         * @param $forumId
         * @param $subscriptionId
         * @param $subscriptionOnlyThreads
         *
         * @return array
         */
        public function subscribeSubforum($forumId, $subscriptionId, $subscriptionOnlyThreads)
        {

            list ($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            if ($subscriptionId) {
                $result = $this->helper->subscription->deleteSubscriptionSubforum($subscriptionId);
                if ($result) {
                    $return                 = $this->getForumInTable($forumId, true);
                    $return ['dialogclose'] = "subscribesubforum" . $forumId;
                    $return ['usermessage'] = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_CANCEL_SUCCESS'];
                } else {
                    $return ['usermessage'] = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_ERROR'];
                }

            } else {

                $subscriptionOnlyThreads = ($subscriptionOnlyThreads == 'true');

                $result = $this->helper->subscription->insertSubscriptionSubforumIntoDB($forumId, $this->User->id, $subscriptionOnlyThreads);
                if (!$result) {
                    $return ['usermessage'] = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_ERROR'];
                } else {
                    $return                 = $this->getForumInTable($forumId, true);
                    $return ['dialogclose'] = "subscribesubforum" . $forumId;
                    $return ['usermessage'] = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_SUCCESS'];
                }
            }

            return $return;
        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function subscribeThreadDialog($threadId)
        {

            $forumId = $this->helper->getForumIdForThread($threadId);
            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $thread = $this->helper->getThreadAndForumNameFromDB($threadId);

            $subscriptionId = $this->helper->subscription->getThreadSubscriptionFromDB($threadId, $this->User->id);
            if ($subscriptionId) {
                $dialogData = sprintf($GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_THREAD_SUBSCRIPTION_CANCEL'], $thread ['threadname'], $thread ['forumname']);
                $buttonTxt  = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_THREAD_CANCEL'];
                $title      = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['UNSUBSCRIBE_THREAD'];
            } else {
                $dialogData = sprintf($GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_THREAD_TEXT'], $thread ['threadname'], $thread ['forumname']);
                $buttonTxt  = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIBE_THREAD'];
                $title      = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIBE_THREAD'];
            }

            $dialogbuttons = array();

            $dialogbuttons [] = array(
                "action" => 'subscribethread:' . $threadId . ':' . $subscriptionId,
                "type"   => 'get',
                "text"   => $buttonTxt
            );

            $dialogbuttons [] = array(
                "action" => 'closedialog:subscribethread' . $threadId,
                "type"   => 'get',
                "text"   => $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['CANCEL']
            );

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $dialogData,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $title,
                                                                      "height" => 200,
                                                                      "modal"  => true
                                                                  )),
                "dialogid"      => 'subscribethread' . $threadId,
                "dialogstate"   => "forum:" . $forumId . ";subscribethreaddialog:" . $threadId,
                "dialogbuttons" => $dialogbuttons
            );

            return $return;

        }


        /**
         * @param $threadId
         * @param $subscriptionId
         *
         * @return mixed
         */
        public function subscribeThread($threadId, $subscriptionId)
        {

            list($access, $message) = $this->checkPermission($this->helper->getForumIdForThread($threadId));
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            if ($subscriptionId) {

                $result = $this->helper->subscription->deleteSubscriptionThread($subscriptionId);
                if ($result) {
                    $return ['dialogclose']   = "subscribethread" . $threadId;
                    $return ['usermessage']   = $GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_THREAD_CANCEL_SUCCESS'];
                    $return ['performaction'] = "readthread:" . $threadId;

                } else {
                    $return ['usermessage'] = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_THREAD_ERROR'];
                }

            } else {
                $result = $this->helper->subscription->insertSubscriptionThreadIntoDB($threadId, $this->User->id);
                if (!$result) {
                    $return ['usermessage'] = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_THREAD_ERROR'];
                } else {
                    $return ['dialogclose']   = "subscribethread" . $threadId;
                    $return ['usermessage']   = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_THREAD_SUCCESS'];
                    $return ['performaction'] = "readthread:" . $threadId;
                }
            }

            return $return;

        }


        /**
         * @param $value
         *
         * @return mixed
         */
        public function unsubscribeLinkThread($value)
        {

            $result                  = $this->helper->subscription->unsubscribeLinkThread($value);
            $return['usermessage']   = $result['message'];
            $return['performaction'] = 'initnav';

            return $return;
        }


        /**
         * @param $value
         *
         * @return mixed
         */
        public function unsubscribeLinkSubforum($value)
        {

            $result                  = $this->helper->subscription->unsubscribeLinkSubforum($value);
            $return['usermessage']   = $result['message'];
            $return['performaction'] = 'initnav';

            return $return;
        }


        /**
         * @param $value
         *
         * @return mixed
         */
        public function unsubscribeLinkAll($value)
        {

            $return['usermessage']   =
                $this->helper->subscription->unsubscribeLinkAll($value);
            $return['performaction'] = 'initnav';

            return $return;
        }


        /**
         * @param $threadId
         * @param $newForumId
         *
         * @return array
         */
        public function moveThread($threadId, $newForumId)
        {

            $forumId = $this->helper->getForumIdForThread($threadId);
            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            list($access, $message) = $this->checkPermission($newForumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $threadSubscribers   = $this->helper->subscription->getThreadSubscribersFromDB($threadId);
            $forumSubscribers    = $this->helper->subscription->getForumSubscribersFromDB($forumId, 1);
            $newForumSubscribers = $this->helper->subscription->getForumSubscribersFromDB($newForumId, 1);

            if ($threadSubscribers || $forumSubscribers || $newForumSubscribers) {
                $threadOld                                                   = $this->helper->getThreadAndForumNameFromDB($threadId);
                $this->helper->subscription->MailCache ['moveThreadOldName'] = $threadOld['forumname'];
            }

            $result = $this->helper->moveThreadDB($threadId, $newForumId);
            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD_ERROR'];
            } else {
                $return                 = $this->getForumInTable($forumId, true);
                $return ['dialogclose'] = array(
                    "movethread" . $threadId,
                    "thread" . $threadId
                );
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD_SUCCESS'];

                if ($threadSubscribers || $forumSubscribers || $newForumSubscribers) {
                    $cronjob =
                        $this->helper->subscription->sendSubscriptionEMail(
                            array_merge($threadSubscribers, $forumSubscribers, $newForumSubscribers), $threadId, 'moveThread');
                    if ($cronjob) {
                        $return['cronexec'][] = $cronjob;
                    }
                }

                $sitemapJob = $this->helper->generateSitemapCronjob($this, $forumId);
                if ($sitemapJob) {
                    $return['cronexec'][] = $sitemapJob;
                }

            }

            return $return;
        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function moveThreadDialog($threadId)
        {

            $forumId = $this->helper->getForumIdForThread($threadId);
            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $thread = $this->helper->getThreadAndForumNameFromDB($threadId);
            $select = sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD_TEXT'], $thread['threadname'], $thread['forumname']);

            // get forums as flat array (without hierarchy)
            $forums = $this->helper->getForumsFromDB($this->c4g_forum_startforum, true, true);
            $select .= '<select name="forum" class="formdata ui-corner-all">';
            foreach ($forums AS $forum) {
                if ($forum['subforums'] == 0) {
                    if (($forum['id'] != $forumId) && ($forum['linkurl'] == '') && ($this->helper->checkPermissionForAction($forum['id'], $this->action))) {
                        $select .= '<option value="' . $forum['id'] . '">' . $forum['name'] . '</option>';
                        $entries = true;
                    }
                }
            }
            $select .= '</select>';
            $dialogbuttons = array();
            if ($entries) {
                $data            = $select;
                $dialogbuttons[] =
                    array(
                        "action" => 'movethread:' . $threadId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD']
                    );
            } else {
                $data = $GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD_NO_FORUMS'];
            }
            $dialogbuttons[] =
                array(
                    "action" => 'closedialog:movethread' . $threadId,
                    "type"   => 'get',
                    "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                );
            $return          = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $GLOBALS['TL_LANG']['C4G_FORUM']['MOVE_THREAD'],
                                                                      "height" => 200,
                                                                      "modal"  => true
                                                                  )),
                "dialogid"      => 'movethread' . $threadId,
                "dialogbuttons" => $dialogbuttons,
            );

            return $return;

        }


        /**
         * @param $forumId
         *
         * @return mixed
         */
        public function addMember($forumId)
        {

            if (!$this->helper->checkPermissionForAction($forumId, $this->action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }
            if (!$this->putVars['membergroup']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['MEMBER_GROUP_MISSING'];

                return $return;
            }
            if (!$this->putVars['member']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['MEMBER_MISSING'];

                return $return;
            }

            $result = $this->helper->addMemberGroupDB($this->putVars['membergroup'], $this->putVars['member']);

            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['ADD_MEMBER_ERROR'];
            } else {
                $return ['dialogclose'] = array("addmember" . $forumId);
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['ADD_MEMBER_SUCCESS'];
            }

            return $return;
        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function addMemberDialog($forumId)
        {

            list($access, $message) = $this->checkPermission($forumId);
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            $data = $GLOBALS['TL_LANG']['C4G_FORUM']['MEMBER_GROUP'] . ':<br/>';
            $data .= '<select name="membergroup" class="formdata ui-corner-all">';
            $groups = $this->helper->getMemberGroupsForForum($forumId);
            foreach ($groups AS $group) {
                $data .= '<option value="' . $group['id'] . '">' . $group['name'] . '</option>';
            }
            $data .= '</select><br/>';

            $data .= $GLOBALS['TL_LANG']['C4G_FORUM']['MEMBER'] . ':<br/>';
            $data .= '<select name="member" class="formdata ui-corner-all">';
            $members = $this->helper->getNonMembersOfForum($forumId);
            foreach ($members AS $member) {
                $data .= '<option value="' . $member['id'] . '">' . $member['firstname'] . ' ' . $member['lastname'] . ' (' . $member['username'] . ')</option>';
            }
            $data .= '</select>';

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $GLOBALS['TL_LANG']['C4G_FORUM']['ADD_MEMBER'],
                                                                      "height" => 200,
                                                                      "modal"  => true
                                                                  )),
                "dialogid"      => 'addmember' . $forumId,
                "dialogstate"   => "forum:" . $forumId . ";addmemberdialog:" . $forumId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'addmember:' . $forumId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['ADD_MEMBER']
                    ),
                    array(
                        "action" => 'closedialog:addmember' . $forumId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $postId
         *
         * @return array
         */
        public function delPost($postId)
        {

            $posts = $this->helper->getPostFromDB($postId);
            $post  = $posts[0];
            if ($post['authorid'] == $this->User->id) {
                $action = 'delownpost';
            } else {
                $action = 'delpost';
            }
            if (!$this->helper->checkPermissionForAction($post['forumid'], $action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }

            $result = $this->helper->deletePostFromDB($postId);
            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_POST_ERROR'];
            } else {
                $return                   = $this->getForumInTable($post['forumid'], true);
                $return ['dialogclose']   = array(
                    "delpost" . $postId,
                    "post" . $postId
                );
                $return ['performaction'] = "readthread:" . $post['threadid'];

                $threadSubscribers = $this->helper->subscription->getThreadSubscribersFromDB($post ['threadid']);
                $forumSubscribers  = $this->helper->subscription->getForumSubscribersFromDB($post ['forumid'], 0);

                if ($threadSubscribers || $forumSubscribers) {
                    $this->helper->subscription->MailCache ['subject']  = $post ['subject'];
                    $this->helper->subscription->MailCache ['post']     = str_replace('<br />', '', $post ['text']);
                    $this->helper->subscription->MailCache ['linkname'] = $post ['linkname'];
                    $this->helper->subscription->MailCache ['linkurl']  = $post ['linkurl'];
                    $cronjob                                            =
                        $this->helper->subscription->sendSubscriptionEMail(
                            array_merge($threadSubscribers, $forumSubscribers), $post ['threadid'], 'delete');
                    if ($cronjob) {
                        $return['cronexec'] = $cronjob;
                    }
                }

                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_POST_SUCCESS'];
            }

            return $return;
        }


        /**
         * @param $postId
         *
         * @return array
         */
        public function delPostDialog($postId)
        {

            $posts = $this->helper->getPostFromDB($postId);
            $post  = $posts[0];
            if ($post['authorid'] == $this->User->id) {
                $action = 'delownpostdialog';
            } else {
                $action = 'delpostdialog';
            }
            if (!$this->helper->checkPermissionForAction($post['forumid'], $action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }

            $data = sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['DEL_POST_WARNING'], $post['forumname'], $post['threadname'], $post['username'], $post['subject']);

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_POST'],
                                                                      "height" => 300,
                                                                      "modal"  => true
                                                                  )),
                "dialogid"      => 'delpost' . $postId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'delpost:' . $postId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['DEL_POST']
                    ),
                    array(
                        "action" => 'closedialog:delpost' . $postId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $postId
         *
         * @return array
         */
        public function editPost($postId)
        {

            $posts = $this->helper->getPostFromDB($postId);
            $post  = $posts[0];
            if ($post['authorid'] == $this->User->id) {
                $action = 'editownpost';
            } else {
                $action = 'editpost';
            }
            if (!$this->helper->checkPermissionForAction($post['forumid'], $action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }
            if (!$this->putVars['post']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['POST_MISSING'];

                return $return;
            }
            if (!$this->putVars['subject']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['SUBJECT_MISSING'];

                return $return;
            }
            $this->putVars['osmId'] = $this->putVars['osmIdType'] . '.' . $this->putVars['osmId'];
            $this->putVars['tags']  = \Contao\Input::xssClean($this->putVars['tags']);
            $result                 = $this->helper->updatePostDB($post, $this->User->id, $this->putVars['subject'], $this->putVars['tags'], $this->putVars['post'],
                                                                  $this->putVars['linkname'], $this->putVars['linkurl'], $this->putVars['geox'], $this->putVars['geoy'],
                                                                  $this->putVars['locstyle'], $this->putVars['label'], $this->putVars['tooltip'], $this->putVars['geodata'], $this->putVars['osmId']);


            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST_ERROR'];
            } else {
                $return                 = $this->getForumInTable($post['forumid'], true);
                $return ['dialogclose'] = array(
                    "editpost" . $postId,
                    "post" . $postId
                );
                if ($this->c4g_forum_threadclick == 'THREAD') {
                    $return ['performaction'] = "readthread:" . $post['threadid'];
                } else {
                    $return ['performaction'] = "readpost:" . $postId;
                }

                $threadSubscribers = $this->helper->subscription->getThreadSubscribersFromDB($post['threadid']);
                $forumSubscribers  = $this->helper->subscription->getForumSubscribersFromDB($post['forumid'], 0);

                if ($threadSubscribers || $forumSubscribers) {
                    $this->helper->subscription->MailCache ['subject']  = $this->putVars['subject'];
                    $this->helper->subscription->MailCache ['post']     = $this->putVars['post'];
                    $this->helper->subscription->MailCache ['linkname'] = $this->putVars['linkname'];
                    $this->helper->subscription->MailCache ['linkurl']  = $this->putVars['linkurl'];
                    $cronjob                                            =
                        $this->helper->subscription->sendSubscriptionEMail(
                            array_merge($threadSubscribers, $forumSubscribers), $post['threadid'], 'edit');
                    if ($cronjob) {
                        $return['cronexec'] = $cronjob;
                    }
                }


                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST_SUCCESS'];
            }

            return $return;
        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function editThread($threadId)
        {

            $thread = $this->helper->getThreadFromDB($threadId);
            if ($thread['author'] == $this->User->id) {
                $action = 'editownthread';
            } else {
                $action = 'editthread';
            }
            if (!$this->helper->checkPermissionForAction($thread['forumid'], $action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }
            if (!$this->putVars['thread']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['THREADNAME_MISSING'];

                return $return;
            }

            if ($this->helper->checkPermission($thread['forumid'], 'threadsort')) {
                if (isset($this->putVars['sort'])) {
                    $sort = $this->putVars['sort'];
                } else {
                    $sort = 999;
                }
            } else {
                $sort = $thread['sort'];
            }

            if ($this->helper->checkPermission($thread['forumid'], 'threaddesc')) {
                $threaddesc = $this->putVars['threaddesc'];
            } else {
                $threaddesc = $thread['threaddesc'];
            }


            $result = $this->helper->updateThreadDB($thread, $this->User->id, $this->putVars['thread'], $threaddesc, $sort);

            if (!$result) {
                $return ['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_THREAD_ERROR'];
            } else {
                $return                   = $this->getForumInTable($thread['forumid'], true);
                $return ['dialogclose']   = array(
                    "editthread" . $threadId,
                    "thread" . $threadId
                );
                $return ['performaction'] = "readthread:" . $threadId;
                $return ['usermessage']   = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_THREAD_SUCCESS'];
            }

            return $return;
        }


        /**
         * @param $divname
         * @param $forumid
         * @param $dialogId
         * @param $linkname
         * @param $linkurl
         *
         * @return string
         */
        public function getPostlinkForForm($divname, $forumid, $dialogId, $linkname, $linkurl)
        {

            if ($this->helper->checkPermissionForAction($forumid, 'postlink') && false) {
                $addClass = "";
                if ($this->dialogs_jqui) {
                    $addClass = " c4gGuiButton";
                }

                return
                    '<div class="' . $divname . '">' .
                    '<a href="#" data-action="postlink:' . $forumid . ':' . $dialogId . '" class="c4gGuiAction' . $addClass . '">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST_LINK'] . '</a>' .
                    '<input name="linkname" id="' . $dialogId . '_linkname" value="' . $linkname . '" type="text" disabled class="formdata ui-corner-all" size="60">' .
                    '<input name="linkurl" id="' . $dialogId . '_linkurl" value="' . $linkurl . '" type="hidden" class="formdata" ><br/>' .
                    '</div>';
            } else {
                return '';
            }

        }


        /**
         * @param $sDivName
         * @param $aPost
         * @param $sForumId
         *
         * @return string
         */
        public function getTagForm($sDivName, $aPost, $sForumId)
        {

            $aTags       = $this->getTagsRecursivByParent($aPost['forumid']);
            $sHtml = "";
            if(!empty($aTags)) {
                $sHtml = "<div class=\"" . $sDivName . "\">";
                $sHtml .= $GLOBALS['TL_LANG']['C4G_FORUM']['TAGS'] . ':<br/>';
                $sHtml .= "<select name=\"tags\" class=\"formdata c4g_tags\" multiple=\"multiple\" style='width:100%;' data-placeholder='" . $GLOBALS['TL_LANG']['C4G_FORUM']['SELECT_TAGS_PLACEHOLDER'] . "'>";
                foreach ($aTags as $sTag) {

                    $sHtml .= "<option";
                    if (in_array($sTag, $aPost['tags'])) {
                        $sHtml .= ' selected="selected"';
                    }
                    $sHtml .= ">" . $sTag . "</option>";
                }
                $sHtml .= "</select>";
                $sHtml .= "</div>";

                $sHtml .= "<script>jQuery(document).ready(function(){jQuery('.c4g_tags').chosen();});</script>";
            }

            return $sHtml;
        }

        private function getTagsRecursivByParent($sForumId){
            $sReturn = "";
            $aTagsResult = \Contao\Database::getInstance()->prepare("SELECT tags, pid FROM tl_c4g_forum WHERE id = %s")->execute($sForumId);
            $aTags       = $aTagsResult->row();
            if(!empty($aTags['tags'])){
                $sReturn =  $aTags['tags'];
            }else{
                if($aTags['pid'] != '0'){
                    $sReturn = $this->getTagsRecursivByParent($aTags['pid']);
                }
            }
            $aReturn = explode(",", $sReturn);
            if(empty($aReturn)){
                $aReturn = array();
            }
            if(count($aReturn) === 1){
                if($aReturn[0] === ''){
                    $aReturn = array();
                }
            }

            return $aReturn;
        }


        /**
         * @return boolean
         */
        public function map_enabled()
        {

            return
                ($GLOBALS['c4g_maps_extension']['installed']) &&
                ($this->c4g_forum_enable_maps);
        }


        /**
         * @param $divname
         * @param $forumId
         * @param $dialogId
         * @param $geox
         * @param $geoy
         * @param $geodata
         * @param $locstyle
         * @param $label
         * @param $tooltip
         * @param $postId
         * @param $osmId
         *
         * @return string
         */
        public function getPostMapEntryForForm($divname, $forumId, $dialogId, $geox, $geoy, $geodata, $locstyle, $label, $tooltip, $postId, $osmId)
        {

            if ($this->map_enabled()) {
                $forum = $this->helper->getForumFromDB($forumId);
                if (($forum['enable_maps']) || ($forum['enable_maps_inherited'])) {

                    if ($forum['map_type'] == 'OSMID') {
                        // OSM-ID(-Picker)
                        //check Permission
                        if (!$this->helper->checkPermission($forumId, 'mapextend')) {
                            return '';
                        }

                        $osmId = explode('.', $osmId);
                        if ($osmId[0] == 'way') {
                            $selectWay  = ' selected';
                            $selectNode = '';
                        } else {
                            $selectWay  = '';
                            $selectNode = ' selected';
                        }

                        // part for locationstyle
                        $locstyles = '';
                        if ($forum['map_override_locationstyle']) {
                            $locstyles = $this->helper->getLocStylesForForum($forumId);
                            if (is_array($locstyles)) {
                                $locationstyle =
                                    $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION_STYLE'] .
                                    '<select id="' . $dialogId . '_locstyle" name="locstyle" value="' . $locstyle . '" ' .
                                    'class="formdata">';
                                foreach ($locstyles AS $locstyle) {
                                    $locationstyle .= '<option value="' . $locstyle['id'] . '">' . $locstyle['name'] . '</option>';
                                }
                                $locationstyle .=
                                    '</select></div>';
                            }
                        }

                        // end of locstyle

                        return
                            '<div class="' . $divname . '">' .
                            $GLOBALS['TL_LANG']['C4G_FORUM']['OSM_ID'] .
                            '<select name="osmIdType" id="' . $dialogId . '_osmIdType" class="formdata">' .
                            '<option' . $selectNode . '>node</option>' .
                            '<option' . $selectWay . '>way</option>' .
                            '</select>' .
                            '<input name="osmId" id="' . $dialogId . '_osmId" value="' . $osmId[1] . '" type="text" class="formdata"> ' .
                            $locationstyle .
                            //			 			'<input name="locstyle" id="'.$dialogId.'_locstyle" value="'.$locstyle.'" type="hidden" class="formdata">'.
                            '</div>';
                    } else {
                        // GEO-Picker & Editor

                        //check Permission
                        if (!$this->helper->checkPermission($forumId, 'mapedit')) {
                            return '';
                        }

                        $addClass = "";
                        if ($this->dialogs_jqui) {
                            $addClass = " c4gGuiButton";
                        }
                        if (($geox && $geoy) || $geodata) {
                            $butText = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_MAP_LOCATION'];
                            $add     = 0;
                        } else {
                            $butText = $GLOBALS['TL_LANG']['C4G_FORUM']['ADD_MAP_LOCATION'];
                            $add     = 1;
                        }

                        return
                            '<div class="' . $divname . '">' .
                            '<a href="#" data-action="postmapentry:' . $forumId . ':' . $dialogId . ':' . $add . ':' . $postId . '" ' .
                            'class="c4gGuiAction' . $addClass . '">' .
                            sprintf($butText,
                                ($forum['map_location_label'] ? $forum['map_location_label'] : $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION'])
                            ) . '</a>' .
                            '<input name="geox" id="' . $dialogId . '_geox" value="' . $geox . '" type="text" disabled="disabled" class="formdata">' .
                            '<input name="geoy" id="' . $dialogId . '_geoy" value="' . $geoy . '" type="text" disabled="disabled" class="formdata">' .
                            '<br>' .
                            '<input name="geodata" id="' . $dialogId . '_geodata" value=\'' . $geodata . '\' type="hidden" class="formdata">' .
                            '<input name="locstyle" id="' . $dialogId . '_locstyle" value="' . $locstyle . '" type="hidden" class="formdata">' .
                            '<input name="label" id="' . $dialogId . '_label" value="' . $label . '" type="hidden" class="formdata">' .
                            '<input name="tooltip" id="' . $dialogId . '_tooltip" value="' . $tooltip . '" type="hidden" disabled class="formdata">' .
                            '</div>';
                    }
                }
            } else {
                return '';
            }

        }


        /**
         * @param $divname
         * @param $forumid
         * @param $dialogId
         * @param $sortId
         *
         * @return string
         */
        public function getThreadSortForForm($divname, $forumid, $dialogId, $sortId)
        {

            if ($this->helper->checkPermission($forumid, 'threadsort')) {
                return
                    '<div class="' . $divname . '">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['THREADSORT'] . ':<br/>' .
                    '<input name="sort" id="' . $dialogId . '_sortid" value="' . $sortId . '" type="text" class="formdata ui-corner-all" size="3" ></input><br />' .
                    '</div>';
            } else {
                return '';
            }

        }


        /**
         * @param $divname
         * @param $forumid
         * @param $dialogId
         * @param $desc
         *
         * @return string
         */
        public function getThreadDescForForm($divname, $forumid, $dialogId, $desc)
        {

            if ($this->helper->checkPermission($forumid, 'threaddesc')) {
                return
                    '<div class="' . $divname . '">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['THREADDESC'] . ':<br/>' .
                    '<textarea name="threaddesc" id="' . $dialogId . '_threaddesc" class="formdata ui-corner-all" cols="80" rows="3">' . strip_tags($desc) . '</textarea><br />' .
                    '</div>';
            } else {
                return '';
            }
        }


        /**
         * @param $postId
         *
         * @return array
         */
        public function editPostDialog($postId)
        {

            $dialogId = 'editpost' . $postId;
            $posts    = $this->helper->getPostFromDB($postId);
            $post     = $posts[0];
            if (!empty($post['tags'])) {
                $post['tags'] = explode(", ",$post['tags']);
            }
            if ($post['authorid'] == $this->User->id) {
                $action        = 'editownpostdialog';
                $previewAction = 'previeweditownpost';
            } else {
                $action        = 'editpostdialog';
                $previewAction = 'previeweditpost';
            }
            if (!$this->helper->checkPermissionForAction($post['forumid'], $action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }
            $editorId = '';
            if ($this->c4g_forum_editor === "bb") {
                $editorId = ' id="editor"';
            }elseif ($this->c4g_forum_editor === "ck") {
                $editorId = ' id="ckeditor"';
            }else{
                $editorId = '';
            }


            $sServerName = \Environment::get("serverName");
            $sHttps      = \Environment::get("https");
            $path        = \Environment::get("path");
            $sProtocol = !empty($sHttps) ? 'https://' : 'http://';
            $sSite     = $sProtocol . $sServerName . $path;
            if(substr($sSite,-1,1) != "/"){
                $sSite .= "/";
            }


            $data = "";

            $data .= '<div class="c4gForumEditPost">' .
                     '<div class="c4gForumEditPostSubject">' .
                     $GLOBALS['TL_LANG']['C4G_FORUM']['SUBJECT'] . ':<br/>' .
                     '<input name="subject" value="' . $post['subject'] . '" type="text" class="formdata ui-corner-all" size="80" maxlength="100" /><br />' .
                     '</div>';
            $data .= $this->getTagForm('c4gForumEditPostTags', $post, $dialogId);

            $data .= '<div class="c4gForumEditPostContent">' .
                     $GLOBALS['TL_LANG']['C4G_FORUM']['POST'] . ':<br/>' .
                     '<input type="hidden" name="uploadEnv" value="'.$sSite.'">' .
                     '<input type="hidden" name="uploadPath" value="' . $this->c4g_forum_bbcodes_editor_imguploadpath . '">' .
                     '<textarea' . $editorId . ' name="post" cols="80" rows="15" class="formdata ui-corner-all">' . strip_tags($post['text']) . '</textarea>' .
                     '</div>';

            $data .= $this->getPostlinkForForm('c4gForumEditPostLink', $post['forumid'], $dialogId, $post['linkname'], $post['linkurl']);
            $data .= $this->getPostMapEntryForForm('c4gForumEditPostMapData', $post['forumid'], $dialogId,
                                                   $post['loc_geox'], $post['loc_geoy'], $post['loc_data_content'], $post['locstyle'], $post['loc_label'], $post['loc_tooltip'], $postId, $post['loc_osm_id']);

            $data .=
                '</div>';


            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" => $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST'],
                                                                      "modal" => true
                                                                  )),
                "dialogid"      => $dialogId,
                "dialogstate"   => "forum:" . $post['forumid'] . ";editpostdialog:" . $postId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'editpost:' . $postId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SAVE_POST_CHANGES']
                    ),
                    array(
                        "action" => $previewAction . ':' . $postId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['PREVIEW']
                    ),
                    //array( "action" => 'closedialog:'.$dialogId, "type" => 'get', "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL'])
                    //array( "action" => 'cancelpost:'.$post['threadid'].':editpostdialog-'.$postId, "type" => 'get', "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL'])
                    array(
                        "action" => 'cancelpost:' . $post['threadid'] . ':' . $dialogId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $threadId
         *
         * @return array
         */
        public function editThreadDialog($threadId)
        {

            $dialogId = 'editthread' . $threadId;
            $thread   = $this->helper->getThreadFromDB($threadId);
            if ($thread['author'] == $this->User->id) {
                $action = 'editownthreaddialog';
            } else {
                $action = 'editthreaddialog';
            }
            if (!$this->helper->checkPermissionForAction($thread['forumid'], $action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }

            $data = $this->getThreadDescForForm('c4gForumEditThreadDesc', $thread['forumid'], 'editthread', $thread['threaddesc']);
            $data .= '<div class="c4gForumEditThread">' .
                     '<div class="c4gForumEditThreadName">' .
                     $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'] . ':<br/>' .
                     '<input name="thread" value="' . $thread['name'] . '" type="text" class="formdata ui-corner-all" size="80" maxlength="100" /><br />' .
                     $data .= '</div>';
            $data .= $this->getThreadSortForForm('c4gForumEditThreadSort', $thread['forumid'], 'editthread', $thread['sort']);
            $data .= '</div>';

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" => $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_THREAD'],
                                                                      "modal" => true
                                                                  )),
                "dialogid"      => $dialogId,
                "dialogstate"   => "forum:" . $thread['forumid'] . ";editthreaddialog:" . $threadId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'editthread:' . $threadId,
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SAVE_THREAD_CHANGES']
                    ),
                    array(
                        "action" => 'closedialog:' . $dialogId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $forumId
         * @param $dialogId
         *
         * @return array
         */
        public function postLink($forumId, $dialogId)
        {

            if (!$this->helper->checkPermissionForAction($forumId, $this->action)) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }

            $linkName = '#' . $dialogId . '_linkname';
            $linkUrl  = '#' . $dialogId . '_linkurl';

            $data = '<div class="c4gForumPostLink">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['LINKNAME'] . ':<br/>' .
                    '<input name="linkname" value="" ' .
                    'data-source="' . $linkName . '" data-srcattr="value" ' .
                    'data-target="' . $linkName . '" data-trgattr="value" type="text" class="formlink ui-corner-all" size="80" maxlength="80" /><br />' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['LINKURL'] . ':<br/>' .
                    '<input name="linkurl" value="" ' .
                    'data-source="' . $linkUrl . '" data-srcattr="value" ' .
                    'data-target="' . $linkUrl . '" data-trgattr="value" type="text" class="formlink ui-corner-all" size="80" maxlength="255" /><br />' .
                    '</div>';


            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_POST_LINK'],
                                                                      "modal"  => true,
                                                                      "height" => 300
                                                                  )),
                "dialogid"      => 'postlink' . $forumId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'submit',
                        "type"   => 'submit',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SUBMIT']
                    ),
                    array(
                        "action" => 'clear',
                        "type"   => 'submit',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['DELETE_LINK']
                    ),
                    array(
                        "action" => 'closedialog:postlink' . $forumId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $forumId
         * @param $dialogId
         * @param $add
         * @param $postId
         *
         * @return array
         */
        public function postMapEntry($forumId, $dialogId, $add, $postId)
        {

            if ((!$this->map_enabled()) ||
                (!$this->helper->checkPermission($forumId, 'mapedit'))
            ) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }

            $forum      = $this->helper->getForumFromDB($forumId);
            $geox       = '#' . $dialogId . '_geox';
            $geoy       = '#' . $dialogId . '_geoy';
            $geodata    = '#' . $dialogId . '_geodata';
            $locstyleId = '#' . $dialogId . '_locstyle';
            $label      = '#' . $dialogId . '_label';
            $tooltip    = '#' . $dialogId . '_tooltip';

            $data = '<div class="c4gForumPostMapEntry">';

            if ($forum['map_type'] == 'PICK') {
                // GEO Picker
                $data .= '<div class="c4gForumPostMapGeoCoords">' .
                         '<div class="c4gForumColumn1">' .
                         $GLOBALS['TL_LANG']['C4G_FORUM']['GEO_COORDS'] .
                         '</div>' .
                         '<input id="c4gForumPostMapEntryGeoX" name="geox" value="" ' .
                         'data-source="' . $geox . '" data-srcattr="value" ' .
                         'data-target="' . $geox . '" data-trgattr="value" disabled type="text" class="formlink ui-corner-all" size="20" maxlength="20" />' .
                         '<input id="c4gForumPostMapEntryGeoY" name="geoy" value="" ' .
                         'data-source="' . $geoy . '" data-srcattr="value" ' .
                         'data-target="' . $geoy . '" data-trgattr="value" disabled type="text" class="formlink ui-corner-all" size="20" maxlength="20" />' .
                         '</div>';
            } else {
                // Feature Editor
                $data .= '<input id="c4gForumPostMapEntryGeodata" name="geodata" value="" ' .
                         'data-source="' . $geodata . '" data-srcattr="value" ' .
                         'data-target="' . $geodata . '" data-trgattr="value" type="hidden" class="formlink"></input>';

            }
            $disabled = "";
            if (!$this->helper->checkPermission($forumId, 'mapedit_style')) {
                $disabled = "disabled ";

            }

            if ($forum['map_type'] == 'PICK' || $forum['map_type'] == 'OSMID') {
                // $locstyles = C4GMaps::getLocStyles($this->Database);
                $locstyles = $this->helper->getLocStylesForForum($forumId);
                if (is_array($locstyles)) {
                    $data .=
                        '<div class="c4gForumPostMapLocStyle">' .
                        '<div class="c4gForumColumn1">' .
                        $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION_STYLE'] .
                        '</div>' .
                        '<select id="c4gForumPostMapEntryLocStyle" name="locstyle" value="" ' . $disabled .
                        'data-source="' . $locstyleId . '" data-srcattr="value" ' .
                        'data-target="' . $locstyleId . '" data-trgattr="value" class="formlink ui-corner-all">';
                    foreach ($locstyles AS $locstyle) {
                        $data .= '<option value="' . $locstyle['id'] . '">' . $locstyle['name'] . '</option>';
                    }
                    $data .=
                        '</select></div>';
                }
            }

            if ($forum['map_label'] == 'CUST') {
                $data .=
                    '<div class="c4gForumPostMapLocLabel">' .
                    '<div class="c4gForumColumn1">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION_LABEL'] .
                    '</div>' .
                    '<input id="c4gForumPostMapEntryLabel" name="label" value="" ' .
                    'data-source="' . $label . '" data-srcattr="value" ' .
                    'data-target="' . $label . '" data-trgattr="value" type="text" class="formlink ui-corner-all" size="50" maxlength="100" />' .
                    '</div>';
            }

            if ($forum['map_tooltip'] == 'CUST') {
                $data .=
                    '<div class="c4gForumPostMapLocTooltip">' .
                    '<div class="c4gForumColumn1">' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION_TOOLTIP'] .
                    '</div>' .
                    '<input id="c4gForumPostMapEntryTooltip" name="tooltip" value="" ' .
                    'data-source="' . $tooltip . '" data-srcattr="value" ' .
                    'data-target="' . $tooltip . '" data-trgattr="value"  type="text" class="formlink ui-corner-all" size="50" maxlength="100" />' .
                    '</div>';
            }
            $data .=
                '</div>';

            $this->c4g_map_id                    = $forum['map_id'];
            C4GForumHelper::$postIdToIgnoreInMap = $postId;
            $mapData                             = C4GMaps::prepareMapData($this, $this->Database, null, true);
            if ($forum['map_type'] == 'PICK') {

                // GEO Picker
                $mapData['pickGeo']          = true;
                $mapData['pickGeo_xCoord']   = '#c4gForumPostMapEntryGeoX';
                $mapData['pickGeo_yCoord']   = '#c4gForumPostMapEntryGeoY';
                $mapData['pickGeo_initzoom'] = 14;

                $mapData['geocoding']     = true;
                $mapData['geocoding_url'] = 'system/modules/c4g_maps/C4GNominatim.php';
                $mapData['geocoding_div'] = 'c4gForumPostMapGeocoding';

                $mapData['div'] = 'c4gForumPostMap';
                $data .= '<div id="c4gForumPostMapGeocoding" class="c4gForumPostMapGeocoding"></div>';
                $data .= '<div id="c4gForumPostMap" class="c4gForumPostMap mod_c4g_maps"></div>';
            } else {

                // Feature Editor
                $mapData['editor']        = true;
                $mapData['editor_labels'] = $GLOBALS['TL_LANG']['c4g_maps']['editor_labels'];
                $mapData['editor_field']  = '#c4gForumPostMapEntryGeodata';
                switch ($forum['map_type']) {
                    case 'EDIT1' :
                        $mapData['editor_types'] = array('polygon');
                        break;
                    case 'EDIT2' :
                        $mapData['editor_types'] = array('path');
                        break;
                    default:


                }

                $mapData['geocoding_url']         = 'system/modules/c4g_maps/C4GNominatim.php';
                $mapData['geosearch']             = true;
                $mapData['geosearch_div']         = 'c4gForumPostMapGeosearch';
                $mapData['geosearch_zoomto']      = 14;
                $mapData['geosearch_zoombounds']  = true;
                $mapData['geosearch_attribution'] = true;

                $mapData['div'] = 'c4gForumPostMap';
                $data .= '<div id="c4gForumPostMapGeosearch" class="c4gForumPostMapGeosearch"></div>';
                $data .= '<div id="c4gForumPostMap" class="c4gForumPostMap mod_c4g_maps"></div>';
            }

            if ($add) {
                $title = $GLOBALS['TL_LANG']['C4G_FORUM']['ADD_MAP_LOCATION'];
            } else {
                $title = $GLOBALS['TL_LANG']['C4G_FORUM']['EDIT_MAP_LOCATION'];
            }

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "mapdata"       => $mapData,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" =>
                                                                          sprintf($title,
                                                                              ($forum['map_location_label'] ? $forum['map_location_label'] : $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION'])
                                                                          )
                                                                      ,
                                                                      "modal" => true
                                                                  )),
                "dialogid"      => 'postmapentry' . $forumId,
            );

            $return['dialogbuttons'][] = array(
                "action" => 'submit',
                "type"   => 'submit',
                "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SUBMIT']
            );
            if (!$add) {
                $return['dialogbuttons'][] =
                    array(
                        "action" => 'clear',
                        "type"   => 'submit',
                        "text"   =>
                            sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['DELETE_MAP_LOCATION'],
                                ($forum['map_location_label'] ? $forum['map_location_label'] : $GLOBALS['TL_LANG']['C4G_FORUM']['LOCATION'])
                            )
                    );

            }
            $return['dialogbuttons'][] = array(
                "action" => 'closedialog:postmapentry' . $forumId,
                "type"   => 'get',
                "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
            );

            return $return;

        }


        /**
         * @param $postId
         *
         * @return array
         */
        public function viewMapForPost($postId)
        {

            $posts = $this->helper->getPostFromDB($postId);
            $post  = $posts[0];

            $forum = $this->helper->getForumFromDB($post['forumid']);
            if ((!$this->map_enabled()) ||
                (!$this->helper->checkPermissionForAction($post['forumid'], 'viewmapforpost'))
            ) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }
            if ((!$forum['enable_maps']) && (!$forum['enable_maps_inherited'])) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['ERROR_MAP_NOT_ACTIVE'];

                return $return;
            }

            $this->c4g_map_id = $forum['map_id'];
            $locations        = array();
            $locations[]      = $this->helper->getMapLocationForPost($post);

            $mapData = C4GMaps::prepareMapData($this, $this->Database, $locations);
            if (($post['loc_geox'] != '') && ($post['loc_geoy'] != '')) {
                $mapData['calc_extent'] = 'CENTERZOOM';
                $mapData['center_geox'] = $post['loc_geox'];
                $mapData['center_geoy'] = $post['loc_geoy'];
                $mapData['zoom']        = 14;
            } else {
                $mapData['calc_extent']    = 'ID';
                $mapData['calc_extent_id'] = $locations[0]['id'];
            }

            $mapData['div'] = 'c4gForumPostMap';
            $data           = '<div id="c4gForumPostMap" class="c4gForumPostMap mod_c4g_maps"></div>';

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "mapdata"       => $mapData,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" => $GLOBALS['TL_LANG']['C4G_FORUM']['VIEW_MAP_FOR_POST'],
                                                                      "modal" => true
                                                                  )),
                "dialogid"      => 'viewmapforpost' . $postId,
                "dialogstate"   => "forum:" . $post['forumid'] . ";viewmapforpost:" . $postId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'closedialog:viewmapforpost' . $postId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CLOSE']
                    )
                ),
            );

            return $return;

        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function viewMapForForum($forumId)
        {

            $forum = $this->helper->getForumFromDB($forumId);
            if ((!$this->map_enabled()) ||
                (!$this->helper->checkPermissionForAction($forumId, 'viewmapforforum'))
            ) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }
            if ((!$forum['enable_maps']) && (!$forum['enable_maps_inherited'])) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['ERROR_MAP_NOT_ACTIVE'];

                return $return;
            }

            $this->c4g_map_id = $forum['map_id'];
            $locations        = $this->helper->getMapLocationsForForum($forumId);
            $mapData          = C4GMaps::prepareMapData($this, $this->Database, $locations);

            $mapData['div'] = 'c4gForumPostMap';
            $data           = '<div id="c4gForumPostMap" class="c4gForumPostMap mod_c4g_maps"></div>';

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "mapdata"       => $mapData,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title" => $GLOBALS['TL_LANG']['C4G_FORUM']['VIEW_MAP_FOR_FORUM'],
                                                                      "modal" => true
                                                                  )),
                "dialogid"      => 'viewmapforforum' . $forumId,
                "dialogstate"   => "forum:" . $forumId . ";viewmapforforum:" . $forumId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'closedialog:viewmapforforum' . $forumId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CLOSE']
                    )
                ),
            );

            return $return;

        }


        /**
         * Dialog for the global and temporary forumsearch
         *
         * @param unknown_type $forumId
         *
         * @return multitype:string multitype:multitype:string NULL   Ambigous <string, unknown, multitype:>
         */
        public function searchDialog($forumId)
        {

            $dialogId = 'search';

            //check permissions
            if (!$this->helper->checkPermissionForAction($forumId, 'search')) {
                return $this->getPermissionDenied($this->helper->permissionError);
            }

            //build dialog layout
            $data = '<div class="c4gForumSearch">' .
                    //start of upper-div
                    '<div>' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_LBL_SEARCH_FOR'] . ':<br/>' .
                    '<input name="search" value="" type="text" class="formdata ui-corner-all" style="width:95%;"> </input> ' .
                    '<span onClick="return false" class="c4gGuiTooltip" style="text-decoration:none; cursor:help" title="' . nl2br(C4GUtils::secure_ugc($GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH_HELPTEXT_SEARCHFIELD'])) . '">(?)</span>' .
                    '<br/> ' .

                    '<div>' .
                    '<input type="checkbox" id="onlyThreads" name="onlyThreads" class="formdata ui-corner-all" /><label for="onlyThreads">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_CB_ONLYTHREADS'] . '</label><br/>' .
                    '<input type="checkbox" id="wholeWords" name="wholeWords" class="formdata ui-corner-all" /><label for="wholeWords">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_CB_WHOLEWORDS'] . '</label>' .
                    '</div>' .
                    '<br /> ' .
                    $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_LBL_SEARCH_ALL_THEMES'] . ' ';
            $data .= $this->helper->getForumsAsHTMLDropdownMenuFromDB($this->c4g_forum_startforum, $forumId, ' - ');

            $data .= ' <span onClick="return false" class="c4gGuiTooltip" style="text-decoration:none; cursor:help" title="' . nl2br(C4GUtils::secure_ugc($GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH_HELPTEXT_AREA'])) . '">(?)</span>' .
                     '<br />' .
                     //end of upper-div
                     '</div>' .

                     '<br /><hr>' .

                     //start lower-div
                     '<div>' .
                     $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_LBL_DISPLAY_ONLY'] . ' <br />' .
                     '<input name="author" value="" type="text" class="formdata ui-corner-all" style="width:95%"></input> ' .
                     '<span onClick="return false" class="c4gGuiTooltip" style="text-decoration:none; cursor:help" title="' . nl2br(C4GUtils::secure_ugc($GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH_HELPTEXT_AUTHOR'])) . '">(?)</span><br />' .
                     $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_LBL_AND_WHICH'] .
                     ' <div>' .
                     '<select name="dateRelation" class="formdata ui-corner-all">' .
                     '<option value="dateOfBirth">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_CREATIONDATE'] . '</option>' .
                     '<option value="dateOfLastPost">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_LASTPOST'] . '</option>' .
                     '</select> ' .
                     '<select name="timeDirection" class="formdata ui-corner-all">' .
                     '<option value=">">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_NOTPRIOR'] . '</option>' .
                     '<option value="<">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_PRIOR'] . '</option>' .
                     '</select> ' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_LBL_IS_THAN'] .
                     ' <input name="timePeriod" value="0" type="number" class="formdata ui-corner-all" style="width:50px;"></input> ' .
                     '<select name="timeUnit" class="formdata ui-corner-all">' .
                     '<option value="hour">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_HOUR'] . '</option>' .
                     '<option selected value="day">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_DAY'] . '</option>' .
                     '<option value="week">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_WEEK'] . '</option>' .
                     '<option value="month">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_MONTH'] . '</option>' .
                     '<option value="year">' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_DDL_YEAR'] . '</option>' .
                     '</select> ' .
                     '<span onClick="return false" class="c4gGuiTooltip" style="text-decoration:none; cursor:help" title="' . nl2br(C4GUtils::secure_ugc($GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH_HELPTEXT_TIMEPERIOD'])) . '">(?)</span>' .
                     '</div>' .
                     //end of lower-div
                     '</div>' .

                     '</div>' .
                     '<br/>';

            $forum = $this->helper->getForumFromDB($forumId);

            if ($forum['subforums'] > 0) {
                $action = "forumbox:" . $forumId;
            } else {
                $action = "forum:" . $forumId;
            }

            $return = array(
                "dialogtype"    => "html",
                "dialogdata"    => $data,
                "dialogoptions" => $this->addDefaultDialogOptions(array(
                                                                      "title"  => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHDIALOG_HEADLINE'],
                                                                      "modal"  => true,
                                                                      "width"  => 470,
                                                                      "height" => 325
                                                                  )),
                "dialogid"      => $dialogId,
                "dialogstate"   => $action . ";searchDialog:" . $forumId,
                "dialogbuttons" => array(
                    array(
                        "action" => 'search:' . $forumId,
                        'class'  => 'c4gGuiDefaultAction',
                        "type"   => 'send',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH']
                    ),
                    array(
                        "action" => 'closedialog:' . $dialogId,
                        "type"   => 'get',
                        "text"   => $GLOBALS['TL_LANG']['C4G_FORUM']['CANCEL']
                    )
                ),
            );

            return $return;

        }


        /**
         *
         * @param int   $forumId
         * @param array $searchParam
         *
         * @return unknown|multitype:string multitype:number boolean  multitype:multitype:string NULL   Ambigous <multitype:boolean string multitype:multitype:number string   multitype:NULL string multitype:string   multitype:multitype:string boolean multitype:number   multitype:NULL string multitype:number   multitype:NULL boolean number multitype:number   multitype:NULL boolean number multitype:number  multitype:string   multitype:boolean multitype:number   multitype:string boolean multitype:string  multitype:number   multitype:NULL boolean multitype:number   multitype:NULL string multitype:number  multitype:multitype:number string     , multitype:number string unknown NULL >
         */
        public function search($forumId, $searchParam)
        {

            list($access, $message) = $this->checkPermissionForAction($forumId, 'search');
            if (!$access) {
                return $this->getPermissionDenied($message);
            }

            //prompt a message if search-field is empty
            if (!$this->putVars['search']) {
                $return['usermessage'] = $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH_MESSAGE_NO_SEARCH_ENTRY'];

                return $return;
            }

            //save parameters for the resultevaluation
            // TODO delete This!!! This doesn't work!
            // store search in Session!
            $GLOBALS['c4gForumSearchParamCache'] = $searchParam;

            //prepare all given information-data
            //searchLocation
            $searchLocations               = array($searchParam['searchLocation']);
            $searchLocations               = array_merge($searchLocations, $this->helper->getForumsIdsFromDB($searchParam['searchLocation'], true));
            $searchParam['searchLocation'] = implode(", ", $searchLocations);

            //search
            $threads = array();
            $threads = array_merge($threads, $this->helper->searchSpecificThreadsFromDB($searchParam));


            /** *************************************************************************************************************************************\
             * |* building datatable
             * \****************************************************************************************************************************************/
            $data                 = array();
            $data['aoColumnDefs'] = array(
                array(
                    'sTitle'      => 'key',
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(0)
                ),
                array(
                    'sTitle'                => $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'],
                    "sClass"                => 'c4g_forum_searchres_threadname',
                    "sWidth"                => '30%',
                    "aDataSort"             => array(
                        10,
                        1
                    ),
                    "aTargets"              => array(1),
                    "c4gMinTableSizeWidths" => array(
                        array(
                            "tsize" => 500,
                            "width" => '50%'
                        ),
                        array(
                            "tsize" => 0,
                            "width" => ''
                        )
                    )
                ),
                array(
                    'sTitle'   => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHRESULTPAGE_DATATABLE_AREA'],
                    "sClass"   => 'c4g_forum_searchres_area',
                    "sWidth"   => '20%',
                    "aTargets" => array(2)
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_AUTHOR_SHORT'],
                    "sClass"          => 'c4g_forum_searchres_last_author',
                    "aDataSort"       => array(
                        10,
                        3,
                        5
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(3),
                    "c4gMinTableSize" => 700
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_POST_SHORT'],
                    "sClass"          => 'c4g_forum_searchres_last_post',
                    "aDataSort"       => array(
                        11,
                        5
                    ),
                    "bSearchable"     => false,
                    "asSorting"       => array(
                        'desc',
                        'asc'
                    ),
                    "aTargets"        => array(4),
                    "c4gMinTableSize" => 700
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(5)
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['AUTHOR'],
                    "sClass"          => 'c4g_forum_searchres_author',
                    "aDataSort"       => array(
                        10,
                        6,
                        8
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(6),
                    "c4gMinTableSize" => 500
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['CREATED_ON'],
                    "sClass"          => 'c4g_forum_searchres_created',
                    "aDataSort"       => array(
                        11,
                        8
                    ),
                    "asSorting"       => array(
                        'desc',
                        'asc'
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(7),
                    "c4gMinTableSize" => 500
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(8)
                ),
                array(
                    'sTitle'      => '#',
                    "sClass"      => 'c4g_forum_searchres_postcount',
                    "asSorting"   => array(
                        'desc',
                        'asc'
                    ),
                    "bSearchable" => false,
                    "aTargets"    => array(9)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(10)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(11)
                ),
                array(
                    'sTitle'      => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHRESULTPAGE_DATATABLE_HITS'],
                    "sClass"      => 'c4g_forum_searchres_hits',
                    "bVisible"    => true,
                    "bSearchable" => false,
                    "aTargets"    => array(12)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(13)
                ),
            );
            if ($this->c4g_forum_table_jqui_layout) {
                $data['bJQueryUI'] = true;
            }

            $data['aaSorting']       = array(
                array(
                    12,
                    'desc'
                )
            );
            $data['bScrollCollapse'] = true;
            $data['bStateSave']      = true;
            $data['sPaginationType'] = 'full_numbers';
            $data['oLanguage']       = array(
                "oPaginate"      => array(
                    "sFirst"    => '<<',
                    "sLast"     => '>>',
                    "sPrevious" => '<',
                    "sNext"     => '>'
                ),
                "sEmptyTable"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_EMPTY'],
                "sInfo"          => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_INFO'],
                "sInfoEmpty"     => "-",
                "sInfoFiltered"  => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_FILTERED'],
                "sInfoThousands" => '.',
                "sLengthMenu"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_LENGTHMENU'],
                "sProcessing"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_PROCESSING'],
                "sSearch"        => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_SEARCH'],
                "sZeroRecords"   => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_NOTFOUND']
            );

            foreach ($threads as $thread) {
                switch ($this->c4g_forum_threadclick) {
                    case 'LPOST':
                        $threadAction = 'readlastpost:' . $thread['id'];
                        break;

                    case 'FPOST':
                        $threadAction = 'readpostnumber:' . $thread['id'] . ':1';
                        break;

                    default:
                        $threadAction = 'readthread:' . $thread['id'];
                        break;
                }
                if ($thread['lastPost']) {
                    $lastPost     = $thread['lastPost'];
                    $lastUsername = $thread['lastUsername'];
                } else {
                    $lastPost     = $thread['creation'];
                    $lastUsername = $thread['username'];
                }

                if ($thread['threaddesc']) {
                    $tooltip = $thread['threaddesc'];
                } else {
                    //$tooltip = $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_NODESC'];
                    $tooltip = $this->helper->getFirstPostLimitedTextOfThreadFromDB($thread['id'], 250);
                    $tooltip = preg_replace('/\[[^\[\]]*\]/i', '', $tooltip);
                }
                if (strlen($tooltip) >= 245) {
                    $tooltip = substr($tooltip, 0, strrpos($tooltip, ' '));
                    $tooltip .= ' [...]';
                }

                $data['aaData'][] = array(
                    $threadAction,
                    $this->helper->checkThreadname($thread['name']),
                    $this->helper->getForumNameForThread($thread['id']),
                    $lastUsername,
                    $this->helper->getDateTimeString($lastPost),
                    $lastPost,
                    // hidden column for sorting
                    $thread['username'],
                    $this->helper->getDateTimeString($thread['creation']),
                    $thread['creation'],
                    // hidden column for sorting
                    $thread['posts'],
                    $thread['sort'],
                    // hidden column for sorting
                    999 - $thread['sort'],
                    // hidden column for sorting
                    $thread['hits'],
                    $tooltip
                );    // hidden column for tooltip
            }

            $forum = $this->helper->getForumFromDB($forumId);

            if ($forum['subforums'] > 0) {
                $action = "forumbox:" . $forumId;
            } else {
                $action = "forum:" . $forumId;
            }

            $return = array(
                "dialogclose"    => "search",
                "contenttype"    => "datatable",
                "contentdata"    => $data,
                "contentoptions" => array(
                    "actioncol"     => 0,
                    "tooltipcol"    => 13,
                    "selectOnHover" => true,
                    "clickAction"   => true
                ),
                "state"          => $action . ";searchDialog:" . $forumId,
                "headline"       => '<div class="ui-widget-header"><center>' . $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHRESULTPAGE_HEADLINE'] . '</center></div>' .
                                    '<div class="ui-widget-content"><center>' . $GLOBALS['c4gForumSearchParamCache']['search'] . ' </center></div>',
                "buttons"        => array(
                    array(
                        "id"   => 'searchDialog:' . $forumId,
                        "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHRESULTPAGE_BUTTON_START_NEW_SEARCH']
                    )
                )
            );

            return $return;
        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function getThreadlist($forumId)
        {

            list($access, $message) = $this->checkPermissionForAction($forumId, 'latestthreads');
            if (!$access) {
                return $this->getPermissionDenied($message);
            }


            //search
            $threads = $this->helper->getThreadsFromDBWithSubforums($forumId);


            /** *************************************************************************************************************************************\
             * |* building datatable
             * \****************************************************************************************************************************************/
            $data                 = array();
            $data['aoColumnDefs'] = array(
                array(
                    'sTitle'      => 'key',
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(0)
                ),
                array(
                    'sTitle'                => $GLOBALS['TL_LANG']['C4G_FORUM']['THREAD'],
                    "sWidth"                => '30%',
                    "aDataSort"             => array(1),
                    "aTargets"              => array(1),
                    "c4gMinTableSizeWidths" => array(
                        array(
                            "tsize" => 500,
                            "width" => '50%'
                        ),
                        array(
                            "tsize" => 0,
                            "width" => ''
                        )
                    )
                ),
                array(
                    'sTitle'   => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCHRESULTPAGE_DATATABLE_AREA'],
                    "sWidth"   => '20%',
                    "aTargets" => array(2)
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_AUTHOR_SHORT'],
                    "aDataSort"       => array(
                        3,
                        5
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(3),
                    "c4gMinTableSize" => 700
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['LAST_POST_SHORT'],
                    "aDataSort"       => array(5),
                    "bSearchable"     => false,
                    "asSorting"       => array(
                        'desc',
                        'asc'
                    ),
                    "aTargets"        => array(4),
                    "c4gMinTableSize" => 700
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(5)
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['AUTHOR'],
                    "aDataSort"       => array(
                        6,
                        8
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(6),
                    "c4gMinTableSize" => 500
                ),
                array(
                    'sTitle'          => $GLOBALS['TL_LANG']['C4G_FORUM']['CREATED_ON'],
                    "aDataSort"       => array(8),
                    "asSorting"       => array(
                        'desc',
                        'asc'
                    ),
                    "bSearchable"     => false,
                    "aTargets"        => array(7),
                    "c4gMinTableSize" => 500
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(8)
                ),
                array(
                    'sTitle'      => '#',
                    "asSorting"   => array(
                        'desc',
                        'asc'
                    ),
                    "bSearchable" => false,
                    "aTargets"    => array(9)
                ),
                array(
                    "bVisible"    => false,
                    "bSearchable" => false,
                    "aTargets"    => array(10)
                ),
            );
            if ($this->c4g_forum_table_jqui_layout) {
                $data['bJQueryUI'] = true;
            }

            $data['aaSorting']       = array(
                array(
                    4,
                    'desc'
                )
            );
            $data['bScrollCollapse'] = true;
            $data['bStateSave']      = true;
            $data['sPaginationType'] = 'full_numbers';
            $data['oLanguage']       = array(
                "oPaginate"      => array(
                    "sFirst"    => '<<',
                    "sLast"     => '>>',
                    "sPrevious" => '<',
                    "sNext"     => '>'
                ),
                "sEmptyTable"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_EMPTY'],
                "sInfo"          => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_INFO'],
                "sInfoEmpty"     => "-",
                "sInfoFiltered"  => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_FILTERED'],
                "sInfoThousands" => '.',
                "sLengthMenu"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_LENGTHMENU'],
                "sProcessing"    => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_PROCESSING'],
                "sSearch"        => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_SEARCH'],
                "sZeroRecords"   => $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_NOTFOUND']
            );

            foreach ($threads as $thread) {
                switch ($this->c4g_forum_threadclick) {
                    case 'LPOST':
                        $threadAction = 'readlastpost:' . $thread['id'];
                        break;

                    case 'FPOST':
                        $threadAction = 'readpostnumber:' . $thread['id'] . ':1';
                        break;

                    default:
                        $threadAction = 'readthread:' . $thread['id'];
                        break;
                }
                if ($thread['lastPost']) {
                    $lastPost     = $thread['lastPost'];
                    $lastUsername = $thread['lastUsername'];
                } else {
                    $lastPost     = $thread['creation'];
                    $lastUsername = $thread['username'];
                }
                if ($thread['threaddesc']) {
                    $tooltip = $thread['threaddesc'];
                } else {
                    //$tooltip = $GLOBALS['TL_LANG']['C4G_FORUM']['THREADS_NODESC'];
                    $tooltip = $this->helper->getFirstPostLimitedTextOfThreadFromDB($thread['id'], 250);
                    $tooltip = preg_replace('/\[[^\[\]]*\]/i', '', $tooltip);
                }
                if (strlen($tooltip) >= 245) {
                    $tooltip = substr($tooltip, 0, strrpos($tooltip, ' '));
                    $tooltip .= ' [...]';
                }
                $data['aaData'][] = array(
                    $threadAction,
                    $this->helper->checkThreadname($thread['name']),
                    $this->helper->getForumNameForThread($thread['id']),
                    $lastUsername,
                    $this->helper->getDateTimeString($lastPost),
                    $lastPost,
                    // hidden column for sorting
                    $thread['username'],
                    $this->helper->getDateTimeString($thread['creation']),
                    $thread['creation'],
                    // hidden column for sorting
                    $thread['posts'],
                    $tooltip
                );    // hidden column for tooltip
            }

            $forum = $this->helper->getForumFromDB($forumId);

            if ($forum['subforums'] > 0) {
                $action = "forumbox:" . $forumId;
            } else {
                $action = "forum:" . $forumId;
            }

            $return = array(
                "dialogclose"    => "search",
                "contenttype"    => "datatable",
                "contentdata"    => $data,
                "contentoptions" => array(
                    "actioncol"     => 0,
                    "tooltipcol"    => 10,
                    "selectOnHover" => true,
                    "clickAction"   => true
                ),
                "state"          => $action . ";threadlist:" . $forumId,
                "headline"       => '<div class="ui-widget-header"><center>' . $GLOBALS['TL_LANG']['C4G_FORUM']['LATESTTHREADS_HEADLINE'] . '</center></div>',
                "buttons"        => array()
                //array(array( id=>'threadlist:'.$forumId, text=>$GLOBALS['TL_LANG']['C4G_FORUM']['LATESTTHREADS']))
            );

            return $return;
        }


        /**
         * @param $buttons
         * @param $forumId
         *
         * @return array
         */
        public function addDefaultButtons($buttons, $forumId)
        {

            //$buttons[] = array( id=>'search', text=>$GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH']);
            //if ($this->c4g_forum_comf_navigation=='BOXES') {
            //	$buttons[] = array( id=>'recalculate', text=>'Neuberechnung (Debug)');
            //}
            if ($this->helper->checkPermissionForAction($forumId, 'search')) {
                $buttons[] = array(
                    "id"   => 'searchDialog:' . $forumId,
                    "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['SEARCH']
                );
            }

            if ($this->helper->checkPermissionForAction($forumId, 'latestthreads') && ($this->action == "forumbox")) {
                $buttons[] = array(
                    "id"   => 'threadlist:' . $forumId,
                    "text" => $GLOBALS['TL_LANG']['C4G_FORUM']['LATESTTHREADS']
                );
            }

            return $buttons;
        }


        /**
         * @param $forumId
         *
         * @return array
         */
        public function getBreadcrumb($forumId)
        {

            if (($this->c4g_forum_comf_navigation == 'TREE') || (!$this->c4g_forum_breadcrumb)) {
                return array();
            }
            $path = $this->helper->getForumPath($forumId, $this->c4g_forum_startforum);

            $data = array();
            foreach ($path as $value) {
                $value['name'] = $this->repInsertTags($value['name']);
                if (($value['use_intropage']) && (!$this->c4g_forum_hide_intropages)) {
                    $data[] = array(
                        "id"   => 'forumintro:' . $value['id'],
                        "text" => $value['name']
                    );
                } else {
                    if ($value['subforums'] == 0) {
                        $data[] = array(
                            "id"   => 'forum:' . $value['id'],
                            "text" => $value['name']
                        );
                    } else {
                        $data[] = array(
                            "id"   => 'forumbox:' . $value['id'],
                            "text" => $value['name']
                        );
                    }
                }
            }

            return $data;
        }


        /**
         * @param $headline
         *
         * @return string
         */
        public function getHeadline($headline)
        {

            $headline = deserialize($headline);
            if (($headline) && ($headline['value'] != '')) {
                $unit = $headline['unit'];

                return '<' . $unit . '>' . $headline['value'] . '</' . $unit . '>';
            } else {
                return '';
            }
        }


        /**
         * @param $forum
         *
         * @return string
         */
        public function getForumLink($forum)
        {

            return C4GUtils::addParametersToURL(
                $this->replaceInsertTags($forum['linkurl']),
                array(
                    'c4g_forum_fmd'   => $this->id,
                    'c4g_forum_forum' => $forum['id']
                ));
        }


        /**
         * @param $e
         *
         * @return mixed
         */
        public function showException($e)
        {

            $message = $GLOBALS['TL_LANG']['C4G_FORUM']['PHP_ERROR'];
            if ($GLOBALS['TL_CONFIG']['displayErrors']) {
                $message .= ' Message: ' . $e->getMessage();
            }
            $return ['usermessage'] = $message;
            try {
                if ($GLOBALS['TL_CONFIG']['logErrors']) {
                    log_message($e->getMessage() . ' File: ' . $e->getFile() . ' Line ' . $e->getLine() . ' (Code: ' . $e->getCode() . ')');
                    $this->log('C4G-Forum PHP-Error: ' . $e->getMessage(), $e->getFile() . ' Line ' . $e->getLine() . ' (Code: ' . $e->getCode() . ')', TL_ERROR);
                }
            } catch (Exception $exc) {
            }

            return $return;
        }


        /**
         * @param $action
         *
         * @return array
         */
        public function performAction($action)
        {

            $values       = explode(':', $action, 5);
            $this->action = $values[0];
            switch ($values[0]) {
                case 'forumtree':
                    $return = $this->generateForumTree();
                    break;
                case 'forumbox':
                    $return = $this->getForumInBoxes($values[1]);
                    break;
                case 'forumintro':
                    $return = $this->getForumintro($values[1]);
                    break;
                case 'forum':
                    $return = $this->getForumInTable($values[1], $values[2]);
                    break;
                case 'readthread':
                    $return = $this->getThreadAsHtml($values[1]);
                    break;
                case 'readpost':
                    $return = $this->getPostAsHtml($values[1]);
                    break;
                case 'readlastpost':
                    $return = $this->getLastPostOfThreadAsHtml($values[1]);
                    break;
                case 'readpostnumber':
                    $return = $this->getPostNumberOfThreadAsHtml($values[1], $values[2]);
                    break;
                case 'newpost':
                    $return = $this->generateNewPostForm($values[1], $values[2]);
                    break;
                case 'newthread':
                    $return = $this->generateNewThreadForm($values[1]);
                    break;
                case 'sendpost':
                    $return = $this->sendPost($values[1]);
                    break;

                case 'previewpost':
                    $return = $this->previewPost($values[1], $GLOBALS['TL_LANG']['C4G_FORUM']['NEW_POST_PREVIEW']);
                    break;
                case 'cancelpost':
                    $return = $this->cancelPost($values[1], $values[2]);
                    break;
                case 'sendthread':
                    $return = $this->sendThread($values[1]);
                    break;
                case 'previewthread':
                    $return = $this->previewThread($values[1]);
                    break;
                case 'cancelthread':
                    $return = $this->cancelThread($values[1]);
                    break;
                case 'closedialog':
                    $return = $this->closeDialog($values[1]);
                    break;
                case 'usedialog':
                    $return = $this->useDialog($values[1]);
                    break;
                case 'delthreaddialog':
                    $return = $this->delThreadDialog($values[1]);
                    break;
                case 'delthread':
                    $return = $this->delThread($values[1]);
                    break;
                case 'movethreaddialog':
                    $return = $this->moveThreadDialog($values[1]);
                    break;
                case 'movethread':
                    $return = $this->moveThread($values[1], $this->putVars['forum']);
                    break;
                case 'editownthreaddialog':
                case 'editthreaddialog':
                    $return = $this->editThreadDialog($values[1]);
                    break;
                case 'editthread':
                    $return = $this->editThread($values[1]);
                    break;
                case 'delownpostdialog':
                case 'delpostdialog':
                    $return = $this->delPostDialog($values[1]);
                    break;
                case 'delpost':
                    $return = $this->delPost($values[1]);
                    break;
                case 'editownpostdialog':
                case 'editpostdialog':
                    $return = $this->editPostDialog($values[1]);
                    break;
                case 'previeweditpost':
                case 'previeweditownpost':
                    $return = $this->previewEditPost($values[1]);
                    break;
                case 'editpost':
                    $return = $this->editPost($values[1]);
                    break;
                case 'postlink':
                    $return = $this->postLink($values[1], $values[2]);
                    break;
                case 'postmapentry':
                    $return = $this->postMapEntry($values[1], $values[2], $values[3], $values[4]);
                    break;
                case 'addmemberdialog':
                    $return = $this->addMemberDialog($values[1]);
                    break;
                case 'addmember':
                    $return = $this->addMember($values[1]);
                    break;
                case 'recalculate':
                    $this->helper->recalculateHelperData();
                    $return = $this->getForumInBoxes($this->c4g_forum_startforum);
                    break;
                case 'subscribethreaddialog':
                    $return = $this->subscribeThreadDialog($values[1]);
                    break;
                case 'subscribethread':
                    $return = $this->subscribeThread($values[1], $values[2]);
                    break;
                case 'subscribesubforumdialog':
                    $return = $this->subscribeSubforumDialog($values[1]);
                    break;
                case 'subscribesubforum':
                    $return = $this->subscribeSubforum($values[1], $values[2], $this->putVars['subscription_only_threads']);
                    break;
                case 'unsubscribethread':
                    $return = $this->unsubscribeLinkThread($values[1]);
                    break;
                case 'unsubscribesubforum':
                    $return = $this->unsubscribeLinkSubforum($values[1]);
                    break;
                case 'unsubscribeall':
                    $return = $this->unsubscribeLinkAll($values[1]);
                    break;
                case 'viewmapforpost':
                    $return = $this->viewMapForPost($values[1]);
                    break;
                case 'viewmapforforum':
                    $return = $this->viewMapForForum($values[1]);
                    break;
                case 'cron':
                    $this->helper->performCron($values[1]);
                    break;
                case 'searchDialog':
                    $return = $this->searchDialog($values[1]);
                    break;
                case 'search':
                    if (isset($values[2])) {
                        $return = $this->search($values[1], $values[2]);
                    } else {
                        $return = $this->search($values[1],
                                                array(
                                                    "searchLocation"    => $this->putVars['searchLocation'],
                                                    "search"            => $this->putVars['search'],
                                                    "searchOnlyThreads" => $this->putVars['onlyThreads'],
                                                    "searchWholeWords"  => $this->putVars['wholeWords'],
                                                    "author"            => $this->putVars['author'],
                                                    "dateRelation"      => $this->putVars['dateRelation'],
                                                    "timeDirection"     => $this->putVars['timeDirection'],
                                                    "timePeriod"        => $this->putVars['timePeriod'],
                                                    "timeUnit"          => $this->putVars['timeUnit'],
                                                )
                        );
                    }
                    break;
                case 'threadlist':
                    $return = $this->getThreadlist($values[1]);
                    break;
                default:
                    break;
            }
            // HOOK: for enhancements to change the result
            if (isset($GLOBALS['TL_HOOKS']['C4gForumAfterAction']) && is_array($GLOBALS['TL_HOOKS']['C4gForumAfterAction'])) {
                foreach ($GLOBALS['TL_HOOKS']['C4gForumAfterAction'] as $callback) {
                    $this->import($callback[0]);
                    $return = $this->$callback[0]->$callback[1]($return, $this, $this->helper, $this->putVars, $values[0], $values[1], $values[2], $values[3]);
                }
            }

            if (isset($return)) {
                return $return;
            } else {
                return;
            }

        }


        /**
         * @param $historyAction
         *
         * @return array
         */
        public function performHistoryAction($historyAction)
        {

            $values       = explode(':', $historyAction);
            $this->action = $values[0];
            switch ($values[0]) {
                case 'forum':
                    $result = $this->getForumInTable($values[1], true);
                    break;
                default:
                    $result = $this->performAction($historyAction);
            }

            // close all dialogs that have been open to avoid conflicts
            $result['dialogcloseall'] = true;

            return $result;

        }


        /**
         * get reaction for denied permission
         *
         * @param string $message
         */
        public function getPermissionDenied($message)
        {

            if ($this->c4g_forum_jumpTo) {

                // redirect to defined page
                $objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
                    ->limit(1)
                    ->execute($this->c4g_forum_jumpTo);

                if ($objPage->numRows) {
                    $return['jump_to_url'] = $this->generateFrontendUrl($objPage->fetchAssoc());
                }

            }

            if (!$return['jump_to_url']) {
                // no redirect -> show message
                $return['usermessage'] = $message;
            }

            return $return;
        }


        /**
         * @return bool|string
         */
        public function getForumPageUrl(){
            $id = $this->c4g_forum_sitemap_root;
            $sFrontendUrl = false;
            if(!empty($id)){
                $oPage =\Contao\PageModel::findPublishedById($id);
                if (version_compare(VERSION, '3.1', '<')) {
                    $sFrontendUrl = $this->Environment->url;
                } else {
                    $sFrontendUrl = $this->Environment->url . TL_PATH . '/';
                }
                $sFrontendUrl .= $this->getFrontendUrl($oPage->row());
            }
            return $sFrontendUrl;
        }


        /**
         * function is called by every Ajax requests
         */
        public function generateAjax($request = null)
        {
            global $objPage;

            // auf die benutzerdefinierte Fehlerbehandlung umstellen
            $old_error_handler = set_error_handler("c4gForumErrorHandler");
            if ($request == null) {

                // Ajax Request: read get parameter "req"
                $request = $_GET['req'];

                if ($request != 'undefined') {
                    // replace "state" parameter in Session-Referer to force correct
                    // handling after login with "redirect back" set
                    $session                       = $this->Session->getData();
                    $session['referer']['last']    = $session['referer']['current'];
                    $session['referer']['current'] = C4GUtils::addParametersToURL(
                        $session['referer']['last'],
                        array('state' => $request));
                    $this->Session->setData($session);
                }
            }
            if(empty($this->c4g_forum_language)){
                $this->c4g_forum_language = $GLOBALS['TL_LANGUAGE'];
            }

            $this->loadLanguageFile('frontendModules', $this->c4g_forum_language);
            $this->loadLanguageFile('stopwords', $this->c4g_forum_language);

            try {


                $this->initMembers();
                $session = $this->Session->getData();
                if (version_compare(VERSION, '3.1', '<')) {
                    $frontendUrl = $this->Environment->url . $session['current_forum_url'];
                } else {
                    $frontendUrl = $this->Environment->url . TL_PATH . '/' . $session['current_forum_url'];
                }

                $this->helper = new C4GForumHelper($this->Database, $this->Environment, $this->User, $this->headline,
                                                   $frontendUrl, $this->c4g_forum_show_realname);

                if (($_SERVER['REQUEST_METHOD']) == 'PUT') {
                    parse_str(file_get_contents("php://input"), $this->putVars);
                }

                // if there was an initial get parameter "state" then use it for jumping directly
                // to the refering function
                if (($request == 'initnav') && $_GET['initreq']) {
                    $_GET['historyreq'] = $_GET['initreq'];
                }

                // History navigation
                if ($_GET['historyreq']) {
                    $actions = explode(';', $_GET['historyreq']);
                    $result  = array();
                    foreach ($actions AS $action) {
                        $r = $this->performHistoryAction($action);
                        array_insert($result, 0, $r);
                    }

                } else {
                    switch ($request) {
                        case 'initnav' :
                            switch ($this->c4g_forum_comf_navigation) {
                                case 'TREE':
                                    $result = $this->performAction('forumtree');
                                    break;

                                case 'BOXES':
                                    $forum = $this->helper->getForumFromDB($this->c4g_forum_startforum);
                                    if (($forum['use_intropage']) && (!$this->c4g_forum_hide_intropages)) {
                                        $this->action = 'forumintro';
                                        $result       = $this->performAction('forumintro:' . $this->c4g_forum_startforum);
                                    } else {
                                        $this->action = 'forumbox';
                                        $result       = $this->performAction('forumbox:' . $this->c4g_forum_startforum);
                                    }
                                    break;

                                default:
                                    break;

                            }
                            break;
                        default:
                            $actions = explode(';', $request);
                            $result  = array();
                            foreach ($actions AS $action) {
                                $r = $this->performAction($action);
                                if (is_array($r)) {
                                    $result = array_merge($result, $r);
                                }
                            }
                    }
                }
            } catch (Exception $e) {
                $result = $this->showException($e);
            }
            set_error_handler($old_error_handler);
            if (count($GLOBALS['c4gForumErrors']) > 0) {
                $result['phpErrors'] = $GLOBALS['c4gForumErrors'];
            }
            if (($this->c4g_forum_sitemap_updated == 0) && ($this->c4g_forum_sitemap)) {
                $sitemapJob = $this->helper->generateSitemapCronjob($this, 0);
                if ($sitemapJob) {
                    $result['cronexec'][] = $sitemapJob;
                }
            }
            if ($this->plainhtml) {
                return $result;
            } else {
                return json_encode($result);
            }
        }


        /**
         * Needed for C4G-Maps integration
         */
        public function repInsertTags($str)
        {

            return parent::replaceInsertTags($str);
        }


        /**
         * Needed for C4G-Maps integration
         */
        public function import($strClass, $strKey = false, $blnForce = false)
        {

            parent::import($strClass, $strKey, $blnForce);
        }


        /**
         * Needed for C4G-Maps integration
         */
        public function getInput()
        {

            return $this->Input;
        }


        /**
         * Needed for C4G-Maps integration
         */
        public function getFrontendUrl($arrRow)
        {

            return parent::generateFrontendUrl($arrRow);
        }


        /**
         *
         */
        protected function initMembers()
        {

            if (!$this->c4g_forum_jqui) {
                // jQuery UI is deactivated -> automatically deactivate all jQuery UI dependant options
                $this->c4g_forum_jqui_lib               = false;
                $this->c4g_forum_uitheme_css_src        = '';
                $this->c4g_forum_dialogs_embedded       = true;  // real dialogs only with jQuery UI
                $this->c4g_forum_embdialogs_jqui        = false;
                $this->c4g_forum_breadcrumb_jqui_layout = false;
                $this->c4g_forum_buttons_jqui_layout    = false;
                $this->c4g_forum_table_jqui_layout      = false;
                $this->c4g_forum_posts_jqui             = false;
                $this->c4g_forum_boxes_jqui_layout      = false;
                //$this->c4g_forum_enable_scrollpane = false;
            }

            $this->dialogs_jqui = ((!$this->c4g_forum_dialogs_embedded) || ($this->c4g_forum_embdialogs_jqui));
            $this->import('FrontendUser', 'User');

        }
    }

?>