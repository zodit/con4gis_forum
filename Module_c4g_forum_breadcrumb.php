<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 *
 * @version   php 5
 * @package   con4gis
 * @author    Jürgen Witte <http://www.kuestenschmiede.de> 
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2014
 * @link      https://www.kuestenschmiede.de
 * @filesource 
 */



/**
 * Class Module_c4g_forum_breadcrumb 
 */
class Module_c4g_forum_breadcrumb extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_c4g_forum_breadcrumb';

	protected $forumModule = null;

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### '.$GLOBALS['TL_LANG']['FMD']['c4g_forum_breadcrumb'][0].' ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->title;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	/**
	 * @param array $data
	 * @param int $forumId
	 * @param boolean $actionIntro
	 * @param boolean $actionThread
	 */
	public function getBreadcrumb($forumId)
	{
		$headline = deserialize($this->forumModule->headline);
		$helper = new C4GForumHelper($this->Database,null,null,$headline['value']);
		$path = $helper->getForumPath($forumId,$this->forumModule->c4g_forum_startforum);
	
		// redirect to defined page
		$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
		->limit(1)
		->execute($this->c4g_forum_breadcrumb_jumpTo);
			
		if ($objPage->numRows)
		{
			$url = $this->generateFrontendUrl($objPage->fetchAssoc());
		}
				
		$data = array();
		if ($url) {
			$i = 0;
			foreach ($path as $value) {
				if (($value['use_intropage']) && (!$this->c4g_forum_hide_intropages)){
					$action = 'forumintro';
				} else {
					if ($value['subforums']==0) {
						$action = 'forum';
					} else {
						$action = 'forumbox';
					}
				}
				if (++$i===count($path)) {
					// last button without functionality (id is empty)
					$data[] = array(
							id=>'',
							text=>$value['name']);
						
				} else {
					$data[] = array( 
							url=>C4GUtils::addParametersToURL($url, array('state'=>$action.':'.$value['id'])),
							text=>$value['name']);
				}
			}
		}
		return $data;
	}
	
	/**
	 * 
	 */
	protected function getInitData()
	{
		return json_encode(array(
			breadcrumb => $this->getBreadcrumb($_GET['c4g_forum_forum']),
		));				
	}
	
	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->loadLanguageFile('frontendModules', $this->c4g_forum_language);
		
		if (!$_GET['c4g_forum_fmd']) {
			// try to get parameters from referer, if they don't exist
			$session = $this->Session->getData();				
			list($urlpart, $qspart) = array_pad(explode('?', $session['referer']['current'], 2), 2, '');
			parse_str($qspart, $qsvars);
			if ($qsvars['c4g_forum_fmd']) {
				$_GET['c4g_forum_fmd'] = $qsvars['c4g_forum_fmd'];
			}
			if ((!$_GET['c4g_forum_forum']) && ($qsvars['c4g_forum_forum'])) {
				$_GET['c4g_forum_forum'] = $qsvars['c4g_forum_forum'];				
			}
					
		}
		$this->forumModule = $this->Database->prepare("SELECT * FROM tl_module WHERE id=?")
			->limit(1)
			->execute($_GET['c4g_forum_fmd']);
		 
		if ($this->forumModule->numRows) {
			
			// initialize used Javascript Libraries and CSS files
			C4GJQueryGUI::initializeLibraries(
				true,												  // add c4gJQuery GUI Core LIB
				($this->forumModule->c4g_forum_jquery_lib == true),   // add JQuery
				($this->forumModule->c4g_forum_jqui_lib == true),	  // add JQuery UI
				false, 												  // add Tree Control
				false,												  // add Table Control
				false,												  // add history.js
				false,											      // add simple tooltip
				false,												  // add C4GMaps
				false,												  // add C4GMaps - GoogleMaps
				false,												  // add C4GMaps - MapsEditor
				false,												  // add WYSIWYG editor
				false);        										  // add jScrollPane
			$data['id'] = $this->id;	
			$data['div'] = 'c4g_forum';	
			$data['initData'] = $this->getInitData();
			$data['jquiBreadcrumb'] = $this->forumModule->c4g_forum_breadcrumb_jqui_layout;
			if (!$this->forumModule->c4g_forum_breadcrumb_jqui_layout) {
				$data['breadcrumbDelim'] = '>';
			}
			
			//Override JQuery UI Default Theme CSS if defined
			if ($this->forumModule->c4g_forum_uitheme_css_src) {
				if (version_compare(VERSION, '3.2', '>=')) {
					// Contao 3.2.x Format
					$objFile = FilesModel::findByUuid($this->forumModule->c4g_forum_uitheme_css_src);
					$GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
					
				} else if (is_numeric($this->forumModule->c4g_forum_uitheme_css_src))
				{
					// Contao 3 Format
					$objFile = FilesModel::findByPk($this->forumModule->c4g_forum_uitheme_css_src);
					$GLOBALS['TL_CSS']['c4g_jquery_ui'] = $objFile->path;
				}
				else {
					// Contao 2 Format
					$GLOBALS['TL_CSS']['c4g_jquery_ui'] = $this->forumModule->c4g_forum_uitheme_css_src;
				}
			}
							
			$GLOBALS ['TL_CSS'] [] = 'system/modules/con4gis_forum/html/css/c4gForum.css';
						
		}	
		
		$this->Template->c4gdata = $data;
		
	}	
}
	
?>