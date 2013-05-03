<?php
namespace BoilerAppMessenger\Mail;
class MailMessageAdapter implements \BoilerAppMessenger\MessageAdapterInterface{

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @throws \UnexpectedValueException
	 * @return \BoilerAppMessenger\Mail\Message
	 */
	public function adaptMessageToMedia(\BoilerAppMessenger\Message $oMessage){
		$oFormatMessage = new \BoilerAppMessenger\Mail\MailMessage();
		$oFormatMessage->setEncoding('UTF-8');

		//From Sender
		$oFrom = $oMessage->getFrom();
		if($oFrom === \BoilerAppMessenger\Message::SYSTEM_USER)$oFormatMessage->setFrom(
			$this->getOptions()->getSystemUserEmail(),
			$this->getOptions()->getSystemUserName()
		);
		elseif($oFrom instanceof \BoilerAppUser\Entity\UserEntity)$oFormatMessage->setFrom(
			$oFrom->getUserAuthAccess()->getAuthAccessEmailIdentity(),
			$oFrom->getUserDisplayName()
		);
		else throw new \UnexpectedValueException(sprintf(
			'"From" sender expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity, "%s" given',
			is_scalar($oFrom)?$oFrom:(is_object($oFrom)?get_class($oFrom):gettype($oFrom))
		));

		//To Recipiants
		foreach($oMessage->getTo() as $oTo){
			if($oTo === \BoilerAppMessenger\Message::SYSTEM_USER)$oFormatMessage->addTo(
				$this->getOptions()->getSystemUserEmail(),
				$this->getOptions()->getSystemUserName()
			);
			elseif($oTo instanceof \BoilerAppUser\Entity\UserEntity)$oFormatMessage->addTo(
				$oTo->getUserAuthAccess()->getAuthAccessEmailIdentity(),
				$oTo->getUserDisplayName()
			);
			else throw new \UnexpectedValueException('"To" Recipiant expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity');
		}

		//Subject
		$oFormatMessage->setSubject($oMessage->getSubject());

		//Body
		return $oFormatMessage->setBody($oMessage->getBody());
	}
}