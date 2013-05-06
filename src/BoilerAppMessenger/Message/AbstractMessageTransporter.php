<?php
namespace BoilerAppMessenger\Message;
abstract class AbstractMessageTransporter implements \BoilerAppMessenger\Message\MessageTransporterInterface{
	/**
	 * @var \BoilerAppMessenger\Message\MessageRendererInterface
	 */
	protected $messageRenderer;

	/**
	 * @see \BoilerAppMessenger\Message\MessageTransporterInterface::setMessageRenderer()
	 * @param \BoilerAppMessenger\Message\MessageRendererInterface $oMessageRenderer
	 * @return \BoilerAppMessenger\Message\AbstractMessageTransporter
	 */
	public function setMessageRenderer(\BoilerAppMessenger\Message\MessageRendererInterface $oMessageRenderer){
		$this->messageRenderer = $oMessageRenderer;
		return $this;
	}

	/**
	 * @see \BoilerAppMessenger\Message\MessageTransporterInterface::getMessageRenderer()
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\Message\MessageRendererInterface
	 */
	public function getMessageRenderer(){
		if($this->messageRenderer instanceof \BoilerAppMessenger\Message\MessageRendererInterface)return $this->messageRenderer;
		throw new \LogicException('Message renderer is undefined');
	}
}