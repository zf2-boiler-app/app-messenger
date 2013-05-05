<?php
namespace BoilerAppMessenger;
interface MailMessageUserInterface extends \BoilerAppMessenger\MessageUserInterface{
	/**
	 * @param string $sEmail
	 * @return \BoilerAppMessenger\Mail\MailMessageUserInterface
	 */
	public function setUserEmail($sEmail);

	/**
	 * @return string
	 */
	public function getUserEmail();
}