<?php
namespace BoilerAppMessenger;
interface MessageTransporterInterface{

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 */
	public function sendMessage(\BoilerAppMessenger\Message $oMessage);
}