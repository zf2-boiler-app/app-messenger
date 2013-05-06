<?php
namespace BoilerAppMessenger\Message;
interface MessageTransporterInterface{

	/**
	 * @param \BoilerAppMessenger\Message\Message $oMessage
	 * @return \BoilerAppMessenger\Message\MessageTransporterInterface
	 */
	public function sendMessage(\BoilerAppMessenger\Message\Message $oMessage);

	/**
	 * @param \BoilerAppMessenger\Message\MessageRendererInterface $oMessageRenderer
	 * @return \BoilerAppMessenger\Message\MessageTransporterInterface
	 */
	public function setMessageRenderer(\BoilerAppMessenger\Message\MessageRendererInterface $oMessageRenderer);

	/**
	 * @return \BoilerAppMessenger\Message\MessageRendererInterface
	 */
	public function getMessageRenderer();
}