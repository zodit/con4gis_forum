<?php

    /**
     * Contao Open Source CMS
     *
     * Copyright (c) 2005-2014 Leo Feyer
     *
     * @package   wef
     * @author    wef
     * @license   wef
     * @copyright wef
     */


    /**
     * Table tl_c4g_pn
     */
    $GLOBALS['TL_DCA']['tl_c4g_pn'] = array
    (

        // Config
        'config' => array
        (
            'dataContainer'               => 'Table',
            'enableVersioning'            => true,
            'sql' => array
            (
                'keys' => array
                (
                    'id' => 'primary'
                )
            )
        ),

        // List
        'list' => array
        (
            'sorting' => array
            (
                'mode'                    => 1,
                'fields'                  => array(''),
                'flag'                    => 1
            ),
            'label' => array
            (
                'fields'                  => array(''),
                'format'                  => '%s'
            ),
            'global_operations' => array
            (
                'all' => array
                (
                    'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                    'href'                => 'act=select',
                    'class'               => 'header_edit_all',
                    'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
                )
            ),
            'operations' => array
            (
                'edit' => array
                (
                    'label'               => &$GLOBALS['TL_LANG']['tl_c4g_pn']['edit'],
                    'href'                => 'act=edit',
                    'icon'                => 'edit.gif'
                ),
                'copy' => array
                (
                    'label'               => &$GLOBALS['TL_LANG']['tl_c4g_pn']['copy'],
                    'href'                => 'act=copy',
                    'icon'                => 'copy.gif'
                ),
                'delete' => array
                (
                    'label'               => &$GLOBALS['TL_LANG']['tl_c4g_pn']['delete'],
                    'href'                => 'act=delete',
                    'icon'                => 'delete.gif',
                    'attributes'          => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
                ),
                'show' => array
                (
                    'label'               => &$GLOBALS['TL_LANG']['tl_c4g_pn']['show'],
                    'href'                => 'act=show',
                    'icon'                => 'show.gif'
                )
            )
        ),

        // Select
        'select' => array
        (
            'buttons_callback' => array()
        ),

        // Edit
        'edit' => array
        (
            'buttons_callback' => array()
        ),

        // Palettes
        'palettes' => array
        (
            '__selector__'                => array(''),
            'default'                     => '{title_legend},title;'
        ),

        // Subpalettes
        'subpalettes' => array
        (
            ''                            => ''
        ),

        // Fields
        'fields' => array
        (
            'id' => array
            (
                'sql'                     => "int(10) unsigned NOT NULL auto_increment"
            ),
            'tstamp' => array(
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'sender_id' => array(
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'recipient_id' => array(
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            ),
            'subject' => array(
                'sql'                     => "varchar(255) NOT NULL default ''"
            ),
            'status' => array(
                'sql'                     => "char(1) NOT NULL default '0'"
            ),
            'message' => array(
                'sql'                     => "longtext NULL"
            ),
            'dt_created' => array(
                'sql'                     => "int(10) unsigned NOT NULL default '0'"
            )
        )
    );
