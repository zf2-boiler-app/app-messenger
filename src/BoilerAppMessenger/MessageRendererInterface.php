<?php
namespace BoilerAppMessenger;
interface MessageRendererInterface{

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @param callable $oCallback
	 * @return \BoilerAppMessenger\MessageRendererInterface
	 */
	public function renderMessage(\BoilerAppMessenger\Message $oMessage,callable $oCallback);
}