<?php
namespace BoilerAppMessenger\Message;
class Message{
	/**
	 * @var \BoilerAppMessenger\Message\MessageUserInterface
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
	 * @var \Zend\View\Model\ViewModel
	 */
	protected $body;

	/**
	 * @var array
	 */
	protected $attachments = array();

	/**
	 * Set "From" sender
	 * @param \BoilerAppMessenger\Message\MessageUserInterface $oFrom
	 * @return \BoilerAppMessenger\Message\Message
	 */
	public function setFrom(\BoilerAppMessenger\Message\MessageUserInterface $oFrom){
		$this->from = $oFrom;
		return $this;
	}

	/**
	 * Retrieve "From" sender
	 * @return \BoilerAppMessenger\Message\MessageUserInterface
	 */
	public function getFrom(){
		return $this->from;
	}

	/**
	 * Set "To" recipients
	 * @param \BoilerAppMessenger\Message\MessageAwareInterface|array $aTo
	 * @return \BoilerAppMessenger\Message\Message
	 */
	public function setTo($aTo){
		$this->to = array();
		return $this->addTo($aTo);
	}

	/**
	 * Add one or more recipients to the "To" recipients
	 * @param \BoilerAppMessenger\Message\MessageAwareInterface|array $aTo
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Message\Message
	 */
	public function addTo($aTo){
		if($aTo instanceof \BoilerAppMessenger\Message\MessageUserInterface)$aTo = array($aTo);
		elseif($aTo instanceof \Traversable)$aTo = \Zend\Stdlib\ArrayUtils::iteratorToArray($aTo);
		elseif(!is_array($aTo))throw new \InvalidArgumentException('To recipients expects an instance of \BoilerAppMessenger\Message\MessageUserInterface, an array or a Traversable object');
		$this->to = array_unique(array_merge(
			$this->to,
			array_filter($aTo,function($oTo){
				if($oTo instanceof \BoilerAppMessenger\Message\MessageUserInterface)return true;
				else throw new \InvalidArgumentException('Recipient expects an instanceof \BoilerAppMessenger\Message\MessageUserInterface');
			})
		));
		return $this;
	}

	/**
	 * Access the address list of the "To" recipients
	 * @return array
	 */
	public function getTo(){
		if(!is_array($this->to))throw new \LogicException('To recipiants are undefined');
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
	 * Set the message body view model
	 * @param \Zend\View\Model\ViewModel $oBody
	 * @return \BoilerAppMessenger\Message\Message
	 */
	public function setBody(\Zend\View\Model\ViewModel $oBody){
		$this->body = $oBody;
		return $this;
	}

	/**
	 * Return the currently set message body view model
	 * @return \Zend\View\Model\ViewModel
	 */
	public function getBody(){
		return $this->body;
	}

	/**
	 * @param string $sFilePath
	 * @throws \InvalidArgumentException
	 * @return \Messenger\Mail\Message
	*/
	public function addAttachment($sFilePath){
		if(empty($sFilePath) || !file_exists($sFilePath))throw new \InvalidArgumentException('Attachment file "'.$sFilePath.'" does not exist');
		$this->attachments[] = realpath($sFilePath);
		return $this;
	}

	/**
	 * @param string $sFilePath
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Mail\Message
	 */
	public function removeAttachment($sFilePath = null){
		if(is_null($sFilePath))$this->attachments = array();
		elseif(!is_string($sFilePath))throw new \InvalidArgumentException('File path expects string ,"'.gettype($sFilePath).'" given');
		elseif(
			($iKey = array_search(realpath($sFilePath),$this->attachments)) === false
		)throw new \InvalidArgumentException('File path "'.$sFilePath.'" is not an attachment');
		else{
			unset($this->attachments[$iKey]);
			$this->attachments = array_values(array_filter($this->attachments));
		}
		return $this;
	}

	/**
	 * @return array
	 */
	public function getAttachments(){
		return $this->attachments;
	}

	/**
	 * @return boolean
	 */
	public function hasAttachments(){
		return !!$this->attachments;
	}
}