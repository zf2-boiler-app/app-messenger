<?php
namespace BoilerAppMessenger;
interface MessageAdapterInterface{

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 */
	public function adaptMessage(\BoilerAppMessenger\Message $oMessage);
}