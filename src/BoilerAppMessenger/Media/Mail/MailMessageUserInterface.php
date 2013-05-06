<?php
namespace BoilerAppMessenger\Media\Mail;
interface MailMessageUserInterface extends \BoilerAppMessenger\Message\MessageUserInterface{
	/**
	 * @param string $sUserEmail
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageUserInterface
	 */
	public function setUserEmail($sUserEmail);

	/**
	 * @return string
	 */
	public function getUserEmail();
}