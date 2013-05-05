<?php
namespace BoilerAppMessenger;
interface MessageTransporterInterface{

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @return \BoilerAppMessenger\MessageTransporter\MessageTransporterInterface
	 */
	public function sendMessage(\BoilerAppMessenger\Message $oMessage);
}