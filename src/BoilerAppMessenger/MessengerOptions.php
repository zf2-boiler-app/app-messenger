<?php
namespace BoilerAppMessenger;
class MessengerOptions extends \Zend\Stdlib\AbstractOptions{
	/**
	 * @var \BoilerAppMessenger\MessageUserInterface
	 */
	protected $systemUser;

	/**
	 * @param \BoilerAppMessenger\MessageUserInterface $oSystemUser
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\MessengerOptions
	 */
	public function setSystemUser(\BoilerAppMessenger\MessageUserInterface $oSystemUser){
		$this->systemUser = $oSystemUser;
		return $this;
	}


	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\MessageUserInterface
	 */
	public function getSystemUser(){
		if($this->systemUser instanceof \BoilerAppMessenger\MessageUserInterface)return $this->systemUser;
		throw new \LogicException('System user is undefined');
	}
}