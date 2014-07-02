<?php

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
 * Class C4GForumSubscription
 */
class C4GForumSubscription
{

	protected $Database = null;
	protected $Environment = null;
	protected $ForumName = null;
	protected $User = null;
	public $frontendUrl = null;

	public $MailCache = array();
	
	/**
	 * @var C4GForumHelper
	 */
	protected $helper = null;
	
	/**
	 * Construktor
	 */
	public function __construct( $helper = null, $database, $environment = null, $user = null, $forumName = "", $frontendUrl = "" )
	{
		$this->helper = $helper;
		$this->Database = $database;
		$this->User = $user;
		$this->Environment = $environment;
		$this->frontendUrl = $frontendUrl;
		if ($forumName=="") {
			$this->ForumName = $GLOBALS['TL_LANG']['C4G_FORUM']['FORUM'];
		} else {
			$this->ForumName = $forumName;
		}
	}
	
	/**
	 * Give back a subforum subscription
	 * @param int $forumId
	 * @param int $userId
	 */
	public function getSubforumSubscriptionFromDB($forumId, $userId)
	{
		return $this->Database->prepare(
				"SELECT id AS subscriptionId ".
				"FROM tl_c4g_forum_subforum_subscription ".
				"WHERE pid = ? AND member = ?")
				->execute($forumId,$userId)->subscriptionId;
	}

	/**
	 * Give back a subforum subscription
	 * @param int $forumId
	 * @param int $userId
	 */
	public function getCompleteSubforumSubscriptionFromDB($forumId, $userId)
	{
		return $this->Database->prepare(
				"SELECT id AS subscriptionId ".
				"FROM tl_c4g_forum_subforum_subscription ".
				"WHERE pid = ? AND member = ? AND thread_only=?")
				->execute($forumId,$userId,false)->subscriptionId;
	}
	
	/**
	 *
	 * Give back a thread subscription
	 * @param int $threadId
	 * @param int $userId
	 */
	public function getThreadSubscriptionFromDB($threadId, $userId)
	{
		return $this->Database->prepare(
				"SELECT id AS subscriptionId ".
				"FROM tl_c4g_forum_thread_subscription ".
				"WHERE pid = ? AND member = ?")
				->execute($threadId,$userId)->subscriptionId;
	}


	/**
	 *
	 * Give back subscribers of a given forum id from DB as array.
	 * @param int $threadId
	 * @param int $thread_only
	 */
	public function getForumSubscribersFromDB($forumId, $all)
	{
		if ($all){
			return $this->Database->prepare(
					"SELECT a.member AS memberId, b.email AS email, b.username as username, 1 AS type ".
					"FROM tl_c4g_forum_subforum_subscription a ".
					"LEFT JOIN tl_member b ON b.id = a.member ".
					"WHERE a.pid = ?")
					->execute($forumId)->fetchAllAssoc();
		}
		else {
			return $this->Database->prepare(
					"SELECT a.member AS memberId, b.email AS email, b.username as username, 1 as type ".
					"FROM tl_c4g_forum_subforum_subscription a ".
					"LEFT JOIN tl_member b ON b.id = a.member ".
					"WHERE a.pid = ? AND a.thread_only = 0")
					->execute($forumId)->fetchAllAssoc();
		}
	}
	
	/**
	 *
	 * Give back all subscribers of a given thread id from DB as array.
	 * @param int $threadId
	 */
	public function getThreadSubscribersFromDB($threadId)
	{
	
		return $this->Database->prepare(
				"SELECT a.member AS memberId, b.email AS email, b.username as username, 0 as type ".
				"FROM tl_c4g_forum_thread_subscription a ".
				"LEFT JOIN tl_member b ON b.id = a.member ".
				"WHERE a.pid = ?")
				->execute($threadId)->fetchAllAssoc();
	}
	
	/**
	 * @param int $forumId
	 * @param int $userId
	 */
	public function insertSubscriptionSubforumIntoDB($forumId, $userId, $subscriptionOnlyThreads) {
	
		$set = array ();
		$set ['pid'] = $forumId;
		$set ['member'] = $userId;
		$set ['thread_only'] = $subscriptionOnlyThreads;
		$objInsertStmt = $this->Database->prepare ( "INSERT INTO tl_c4g_forum_subforum_subscription %s" )->set ( $set )->execute ();
	
		return $objInsertStmt->affectedRows;
	}
	
	/**
	 * @param int $threadId
	 * @param int $userId
	 */
	public function insertSubscriptionThreadIntoDB($threadId, $userId) {
	
		$set = array ();
		$set ['pid'] = $threadId;
		$set ['member'] = $userId;
	
		$objInsertStmt = $this->Database->prepare ( "INSERT INTO tl_c4g_forum_thread_subscription %s" )->set ( $set )->execute ();
	
		return $objInsertStmt->affectedRows;
	}
	
	/**
	 * Delete subscription by $subscriptionId
	 * @param int $subscriptionId
	 */
	public function deleteSubscriptionThread( $subscriptionId )
	{
		$objDeleteStmt = $this->Database->prepare("DELETE FROM tl_c4g_forum_thread_subscription WHERE id = ?")
		->execute($subscriptionId);
		if ($objDeleteStmt->affectedRows==0) {
			return false;
		}
		return true;
	}
	
	/**
	 * Delete subscription by $subscriptionId
	 * @param int $subscriptionId
	 */
	public function deleteSubscriptionSubforum( $subscriptionId )
	{
		$objDeleteStmt = $this->Database->prepare("DELETE FROM tl_c4g_forum_subforum_subscription WHERE id = ?")
		->execute($subscriptionId);
		if ($objDeleteStmt->affectedRows==0) {
			return false;
		}
		return true;
	}
	
	/**
	 * Delete all subscription of a member
	 * @param int $subscriptionId
	 */
	public function deleteAllSubscriptions( $memberId )
	{
		$rows = 0;
		
		$objDeleteStmt = $this->Database->prepare("DELETE FROM tl_c4g_forum_subforum_subscription WHERE member = ?")
			->execute($memberId);
		$rows += $objDeleteStmt->affectedRows;
		
		$objDeleteStmt = $this->Database->prepare("DELETE FROM tl_c4g_forum_thread_subscription WHERE member = ?")
		->execute($memberId);
		$rows += $objDeleteStmt->affectedRows;
				
		if ($rows==0) {
			return false;
		}
		return true;
	}
	
	/**
	 * Delete subscription by $threadId
	 * @param int $threadId
	 */
	public function deleteSubscriptionForThread( $threadId )
	{
		$objDeleteStmt = $this->Database->prepare("DELETE FROM tl_c4g_forum_thread_subscription WHERE pid = ?")
		->execute($threadId);
		if ($objDeleteStmt->affectedRows==0) {
			return false;
		}
		return true;
	}
	
	/**
	 * Check validity of Mail-Address
	 * @param string $email
	 * @return boolean
	 */
	protected function checkmail($email) {
	
		if (! filter_var ( $email, FILTER_VALIDATE_EMAIL )) {
			return false;
		} else {
			return true;
		}
	}
	
	/**
	 * @param int $subscribers
	 */
	public function sendSubscriptionEMail($subscribers, $threadId, $sendKind) {
	
		$thread = $this->helper->getThreadAndForumNameFromDBUncached ( $threadId );
	
		$cron = array();
		$addresses = array();
		foreach ( $subscribers as $subscriber ) {
			if ((!$addresses[$subscriber ['email']]) && ($subscriber['memberId']!=$this->User->id)) {
				if ($subscriber['type']==1) {
					$sType = 'SUBFORUM';
					$sPerm = 'subscribeforum';
				} else {
					$sType = 'THREAD';						
					$sPerm = 'subscribethread';
				}
				
				// check if subscriber still has permission to get subscription mails
				if ($this->helper->checkPermission($thread['forumid'], $sPerm, $subscriber['memberId'])) {
					switch ($sendKind) {
						case "new" :
							$subjectAddition = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_NEW'];
							$intro = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_NEW_INTRO'];
							break;
						case "edit" :
							$subjectAddition = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_EDIT'];
							$intro = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_EDIT_INTRO'];
							break;
						case "delete" :
							$subjectAddition = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_DELETE'];
							$intro = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_DELETE_INTRO'];
							break;
						case "delThread" :
							$subjectAddition = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_DELTHREAD'];
							$intro = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_DELTHREAD_INTRO'];
							break;
						case "moveThread" :
							$subjectAddition = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_MOVETHREAD'];
							$intro = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_'.$sType.'_MAIL_MOVETHREAD_INTRO'];
							break;
						case "newThread" : // only subforum
							$subjectAddition = $GLOBALS['TL_LANG']['C4G_FORUM']['NEW_THREAD'];
							$intro = $GLOBALS ['TL_LANG'] ['C4G_FORUM'] ['SUBSCRIPTION_SUBFORUM_MAIL_NEWTHREAD_INTRO'];
							break;
					}
				
					$data = array();
					$data['command'] = 'sendmail';
					$data['charset'] = 'UTF-8';			
			
					if ($GLOBALS ['TL_CONFIG'] ['useSMTP'] and $this->checkmail ( $GLOBALS ['TL_CONFIG'] ['smtpUser'] )) {
						$data['from'] = $GLOBALS ['TL_CONFIG'] ['smtpUser'];
					} else {
						$data['from'] = $GLOBALS ['TL_CONFIG'] ['adminEmail'];
					}
						
					$data['subject'] = sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_'.$sType.'_MAIL_SUBJECT'],
							$subjectAddition,
							$GLOBALS['TL_CONFIG']['websiteTitle'],
							$thread['forumname'],
							$thread['threadname'] );
						
			
					$text = sprintf( $intro,
							$GLOBALS['TL_CONFIG']['websiteTitle'],
							$thread['forumname'],
							$thread['threadname'],
							$this->User->username,
							$this->MailCache ['subject'],
							$this->MailCache ['moveThreadOldName'] );
						

					// umformatierung BBC-Quotes
					$this->MailCache ['post'] = preg_replace(
							'/\[quote=([^\]]+)\]([\s\S]*?)\[\/quote\]/i', 
							'"$2" (Zitat von $1)', 
							$this->MailCache ['post']
						);
					$this->MailCache ['post'] = preg_replace(
							'/\[quote\]([\s\S]*?)\[\/quote\]/i', 
							'"$1" (Zitat)', 
							$this->MailCache ['post']
						);
					// umformatierung Spoiler
					$this->MailCache ['post'] = preg_replace(
							'/\[spoiler\]([\s\S]*?)\[\/spoiler\]/i', 
							'(Spoiler)', 
							$this->MailCache ['post']
						);
					// umformatierung Listen
					$this->MailCache ['post'] = preg_replace(
							'/\[\*\]([^\[]*)/i', 
							'\n- $1', 
							$this->MailCache ['post']
						);
					// entferne BBCodes
					$this->MailCache ['post'] = preg_replace('/\[[^\[\]]*\]/i', '', $this->MailCache ['post']);
						

					if (($sendKind != 'delThread') && ($sendKind != 'moveThread')) {
						$text .= sprintf( $GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_MAIL_TEXT'],
								$this->MailCache ['post'] );
			
						if ($this->MailCache ['linkurl']) {
							$text .= sprintf( $GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_MAIL_LINK'],
									$this->MailCache ['linkname'],
									$this->MailCache ['linkurl'] );
						}
			
					}
	
					$data['text'] =
						sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_MAIL_HELLO'],$subscriber['username'])
						.$text;
					if (($sendKind!='delThread') || ($subscriber['type']==1)) { 
						$data['text'] .= sprintf(
								$GLOBALS['TL_LANG']['C4G_FORUM']['SUBSCRIPTION_'.$sType.'_MAIL_END'],
						        $this->helper->getUrlForThread( $threadId,$thread['forumid'] ), 
								$this->helper->getUrlForForum( $thread['forumid'] ),
								$this->generateUnsubscribeLinkThread( $threadId, $subscriber['email'] ),
								$this->generateUnsubscribeLinkSubforum( $thread['forumid'], $subscriber['email'] ),
								$this->generateUnsubscribeLinkAll( $subscriber['email'] )
						);
					}	
					
					$data['to'] = $subscriber['email'];
					$cron[] = $data;
					
					$addresses[$subscriber ['email']] = true;
				}	
			}	
		}

		
		if ($cron) {
			// send mails via cron job, (will be triggered in Javascript part)
			$filename = md5(uniqid(mt_rand(), true));		
			$objFile = fopen(TL_ROOT . '/system/tmp/' . $filename.'.tmp', 'wb');
			fputs($objFile, serialize($cron));
			fclose($objFile);
		}	
		
		return $filename;
	}

	/**
	 * @param string $string
	 * @return string
	 */
	protected function encryptLinkData($string)
	{
		return str_rot13(rtrim(base64_encode($string),'='));		
	}
	
	
	/**
	 * @param string $string
	 * @return string
	 */
	protected function decryptLinkData($string) {
		return base64_decode(str_rot13($string).'==');
	}	
	
	/**
	 * @param int $threadId
	 * @param string $mail
	 */
	public function generateUnsubscribeLinkThread($threadId, $mail)
	{
		return strtok($this->helper->frontendUrl,'?') . '?state=unsubscribethread:'.$this->encryptLinkData($threadId.';'.$mail);
	}

	/**
	 *
	 * @param int $forumId
	 * @param string $mail
	 */
	public function generateUnsubscribeLinkSubforum($forumId, $mail)
	{
		return strtok($this->helper->frontendUrl,'?') . '?state=unsubscribesubforum:'.$this->encryptLinkData($forumId.';'.$mail);		
	}

	/**
	 *
	 * @param string $mail
	 */
	public function generateUnsubscribeLinkAll($mail)
	{
		return strtok($this->helper->frontendUrl,'?') . '?state=unsubscribeall:'.$this->encryptLinkData($mail);		
	}
		
	/**
	 * @param string $value
	 */
	public function unsubscribeLinkThread($value)
	{
		$values = explode(';',$this->decryptLinkData($value),2);
		$thread = $this->helper->getThreadFromDB($values[0]);
		$member = $this->Database->prepare(
				"SELECT id,username FROM tl_member ".
				"WHERE email=?")
				->execute($values[1]);
		if ($member->id) {
			$subscriptionId = $this->getThreadSubscriptionFromDB($values[0], $member->id);
			if ($subscriptionId) { 
				if ($this->deleteSubscriptionThread($subscriptionId)) {
					$message = 
						sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_THREAD_LINK_SUCCESS'],$thread['name'],$member->username);
				}
			}
			
		}		
		if (!$message) {
			$message = $GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_THREAD_LINK_FAILED'];
		}
		$return['message'] = $message;
		$return['forumid'] = $thread['forumid'];	
		$return['threadid'] = $values[0];
		return $return;	
	}
	
	/**
	 * @param string $value
	 */
	public function unsubscribeLinkSubforum($value)
	{
		$values = explode(';',$this->decryptLinkData($value),2);
		$member = $this->Database->prepare(
				"SELECT id,username FROM tl_member ".
				"WHERE email=?")
				->execute($values[1]);
		if ($member->id) {
			$subscriptionId = $this->getSubforumSubscriptionFromDB($values[0], $member->id);
			if ($subscriptionId) {
				if ($this->deleteSubscriptionSubforum($subscriptionId)) {
					$message = 
						sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_SUBFORUM_LINK_SUCCESS'],$this->helper->getForumNameFromDB($values[0]),$member->username);
				}
			}
				
		}
		
		if (!$message) {
			$return['message'] = $GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_SUBFORUM_LINK_FAILED'];
		}	
		$return['message'] = $message;
		$return['forumid'] = $values[0];
		return $return;
		
	}
	
	/**
	 * @param string $value
	 */
	public function unsubscribeLinkAll($value)
	{
		$email = $this->decryptLinkData($value);
		$member = $this->Database->prepare(
				"SELECT id,username FROM tl_member ".
				"WHERE email=?")
				->execute($email);
		if ($member->id) {
			if ($this->deleteAllSubscriptions($member->id)) {
				return sprintf($GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_ALL_LINK_SUCCESS'],$member->username);				
			}
				
		}
		return $GLOBALS['TL_LANG']['C4G_FORUM']['UNSUBSCRIBE_ALL_LINK_FAILED'];
	}			
}

?>