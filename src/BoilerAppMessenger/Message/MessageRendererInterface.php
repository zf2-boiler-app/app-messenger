<?php
namespace BoilerAppMessenger\Message;
interface MessageRendererInterface{
	/**
	 * @param \BoilerAppMessenger\Message\Message $oMessage
	 * @return string
	 */
	public function renderMessageBody(\BoilerAppMessenger\Message\Message $oMessage);
}