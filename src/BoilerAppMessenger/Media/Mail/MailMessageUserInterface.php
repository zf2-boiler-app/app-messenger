<?php
namespace BoilerAppMessenger\Media\Mail;
interface MailMessageUserInterface extends \BoilerAppMessenger\Message\MessageUserInterface{
	/**
	 * @return string
	 */
	public function getUserEmail();
}