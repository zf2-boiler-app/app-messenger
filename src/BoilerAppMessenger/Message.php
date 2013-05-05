<?php
namespace BoilerAppMessenger;
class Message{
	const SYSTEM_USER = 'system';

	/**
	 * @var \BoilerAppMessenger\MessageAwareInterface
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
	 * @param \BoilerAppMessenger\MessageAwareInterface $oFrom
	 * @return \BoilerAppMessenger\Message
	 */
	public function setFrom(\BoilerAppMessenger\MessageAwareInterface $oFrom){
		$this->from = $oFrom;
		return $this;
	}

	/**
	 * Retrieve "From" sender
	 * @return \BoilerAppMessenger\MessageAwareInterface
	 */
	public function getFrom(){
		return $this->from;
	}

	/**
	 * Set "To" recipients
	 * @param \BoilerAppMessenger\MessageAwareInterface|array $aTo
	 * @return \BoilerAppMessenger\Message
	 */
	public function setTo($aTo){
		$this->to = array();
		return $this->addTo($aTo);
	}

	/**
	 * Add one or more recipients to the "To" recipients
	 * @param \BoilerAppMessenger\MessageAwareInterface|array $aTo
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message
	 */
	public function addTo($aTo){
		if($aTo instanceof \BoilerAppMessenger\MessageAwareInterface)$aTo = array($aTo);
		elseif($aTo instanceof \Traversable)$aTo = \Zend\Stdlib\ArrayUtils::iteratorToArray($aTo);
		elseif(!is_array($aTo))throw new \InvalidArgumentException('To recipients expects \BoilerAppMessenger\MessageAwareInterface, array or Traversable object');
		$this->to = array_unique(array_merge(
			$this->to,
			array_filter($aTo,function($oTo){
				if($oTo instanceof \BoilerAppMessenger\MessageAwareInterface)return true;
				else throw new \InvalidArgumentException('Recipient expects an instanceof \BoilerAppMessenger\MessageAwareInterface');
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