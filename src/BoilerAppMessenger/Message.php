<?php
namespace BoilerAppMessenger;
class Message{
	const SYSTEM_USER = 'system';

	/**
	 * @var \BoilerAppUser\Entity\UserEntity|string
	 */
	protected $from;

	/**
	 * @var array
	 */
	protected $to = array();

	/**
	 * Subject of the message
	 * @var string
	 */
	protected $subject;

	/**
	 * Content of the message
	 * @var string
	 */
	protected $body;

	/**
	 * Set "From" sender
	 * @param \BoilerAppUser\Entity\UserEntity|string $oFrom
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message
	 */
	public function setFrom($oFrom = self::SYSTEM_USER){
		if($oFrom === self::SYSTEM_USER || $oFrom instanceof \BoilerAppUser\Entity\UserEntity)$this->from = $oFrom;
		else throw new \InvalidArgumentException('From sender expects \Messenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity');
		return $this;
	}

	/**
	 * Retrieve "From" sender
	 * @return \BoilerAppUser\Entity\UserEntity|string
	 */
	public function getFrom(){
		return $this->from;
	}

	/**
	 * Set "To" recipients
	 * @param \BoilerAppUser\Entity\UserEntity|string|array $aTo
	 * @return \BoilerAppMessenger\Message
	 */
	public function setTo($aTo){
		$this->to = array();
		return $this->addTo($aTo);
	}

	/**
	 * Add one or more recipients to the "To" recipients
	 * @param \BoilerAppUser\Entity\UserEntity|string|array $aTo
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message
	 */
	public function addTo($aTo){
		if($aTo === self::SYSTEM_USER || $aTo instanceof \BoilerAppUser\Entity\UserEntity)$aTo = array($aTo);
		elseif($aTo instanceof \Traversable)$aTo = \Zend\Stdlib\ArrayUtils::iteratorToArray($aTo);
		elseif(!is_array($aTo))throw new \InvalidArgumentException('To recipients expects \BoilerAppMessenger\Message::SYSTEM_USER, \User\Entity\UserEntity, array or Traversable object');
		$this->to = array_unique(array_merge(
			$this->to,
			array_filter($aTo,function($oTo){
				if($oTo === self::SYSTEM_USER || $oTo instanceof \BoilerAppUser\Entity\UserEntity)return true;
				else throw new \InvalidArgumentException('Recipient expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity');
			})
		));
		return $this;
	}

	/**
	 * Access the address list of the "To" recipients
	 * @return array
	 */
	public function getTo(){
		return $this->to;
	}

	/**
	 * Set the message subject value
	 * @param string $sSubject
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message
	 */
	public function setSubject($sSubject){
		if(!is_string($sSubject))throw new \InvalidArgumentException('Subject expects a string value');
		$this->subject = $sSubject;
		return $this;
	}

	/**
	 * Get the message subject header value
	 * @return string
	 */
	public function getSubject(){
		return $this->subject?:'';
	}

	/**
	 * Set the message body value
	 * @param string $sBody
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message
	 */
	public function setBody($sBody){
		if(!is_string($sBody))throw new \InvalidArgumentException('Body expects a string value');
		$this->body = $sBody;
		return $this;
	}

	/**
	 * Return the currently set message body
	 * @return string
	 */
	public function getBody(){
		return $this->body;
	}

	/**
	 * Serialize to string
	 * @return string
	 */
	public function toString(){
		return $this->getSubject().PHP_EOL.PHP_EOL.$this->getBody();
	}
}