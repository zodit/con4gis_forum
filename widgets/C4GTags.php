<?php

    /**
     * Contao Open Source CMS
     *
     * Copyright (c) 2005-2014 Leo Feyer
     *
     * @package Core
     * @link    https://contao.org
     * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
     */


    /**
     * Run in a custom namespace, so the class can be replaced
     */


    /**
     * Class SelectMenu
     *
     * Provide methods to handle select menus.
     * @copyright  Leo Feyer 2005-2014
     * @author     Leo Feyer <https://contao.org>
     * @package    Core
     */
    class C4GTags extends Contao\SelectMenu
    {

        /**
         * Submit user input
         * @var boolean
         */
        protected $blnSubmitInput = true;

        /**
         * Template
         * @var string
         */
        protected $strTemplate = 'be_tag_widget';





        /**
         * Generate the widget and return it as string
         * @return string
         */
        public function generate()
        {

            $this->arrAttributes['multiple'] = 'multiple';
            $this->arrAttributes['chosen'] = true;

            $this->multiple = true;
            $this->chosen = true;
            $sOutput = parent::generate();


            return $sOutput;
        }
    }
