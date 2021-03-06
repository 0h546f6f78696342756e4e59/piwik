<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 * @version $Id$
 * 
 * @category Piwik
 * @package Piwik
 */

/**
 * Class for sending mails, for more information see: 
 *
 * @package Piwik
 * @see Zend_Mail, libs/Zend/Mail.php
 * @link http://framework.zend.com/manual/en/zend.mail.html 
 */
class Piwik_Mail extends Zend_Mail
{
	/**
	 * Default charset utf-8
	 * @param string $charset
	 */
	public function __construct($charset = 'utf-8')
	{
		parent::__construct($charset);
		$this->initSmtpTransport();
	}
	
	public function setFrom($email, $name = null)
	{
		$hostname = Piwik_Config::getInstance()->mail['defaultHostnameIfEmpty'];
		$piwikHost = Piwik_Url::getCurrentHost($hostname);
		
		// If known Piwik URL, use it instead of "localhost"
		$piwikUrl = Piwik::getPiwikUrl();
		$url = parse_url($piwikUrl);
		if(isset($url['host'])
			&& $url['host'] != 'localhost'
			&& $url['host'] != '127.0.0.1')
		{
			$piwikHost = $url['host'];
		}
		$email = str_replace('{DOMAIN}', $piwikHost, $email);
		parent::setFrom($email, $name);
	}
	
	private function initSmtpTransport()
	{
		$mailConfig = Piwik_Config::getInstance()->mail;
		if ( empty($mailConfig['host']) 
			 || $mailConfig['transport'] != 'smtp')
		{
			return;
		}
		$smtpConfig = array();
		if (!empty($mailConfig['type']))
			$smtpConfig['auth'] = strtolower($mailConfig['type']);
		if (!empty($mailConfig['username']))
			$smtpConfig['username'] = $mailConfig['username'];
		if (!empty($mailConfig['password']))
			$smtpConfig['password'] = $mailConfig['password'];
		if (!empty($mailConfig['encryption']))
			$smtpConfig['ssl'] = $mailConfig['encryption'];
		
		$tr = new Zend_Mail_Transport_Smtp($mailConfig['host'], $smtpConfig);
		Piwik_Mail::setDefaultTransport($tr);
		ini_set("smtp_port", $mailConfig['port']);
	}
}
