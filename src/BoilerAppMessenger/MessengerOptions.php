<?php
namespace BoilerAppMessenger;
class MessengerOptions extends \Zend\Stdlib\AbstractOptions{
	/**
	 * @var \BoilerAppMessenger\MessageUserInterface
	 */
	protected $systemUser;

	/**
	 * @param \BoilerAppMessenger\Message\MessageUserInterface $oSystemUser
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\MessengerOptions
	 */
	public function setSystemUser(\BoilerAppMessenger\Message\MessageUserInterface $oSystemUser){
		$this->systemUser = $oSystemUser;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\Message\MessageUserInterface
	 */
	public function getSystemUser(){
		if($this->systemUser instanceof \BoilerAppMessenger\Message\MessageUserInterface)return $this->systemUser;
		throw new \LogicException('System user is undefined');
	}
}