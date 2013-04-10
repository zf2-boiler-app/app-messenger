<?php
namespace BoilerAppMessenger\Mail;
class Message extends \Zend\Mail\Message{

	/**
	 * @var array
	 */
	protected $attachments = array();

	/**
	 * @param string $sFilePath
	 * @throws \InvalidArgumentException
	 * @return \Messenger\Mail\Message
	 */
	public function addAttachment($sFilePath){
		if(empty($sFilePath) || !file_exists($sFilePath))throw new \InvalidArgumentException('Attachment file "'.$sFilePath.'" does not exist');
		$this->attachments[] = $sFilePath;
		return $this;
	}

	/**
	 * @param string $sFilePath
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Mail\Message
	 */
	public function removeAttachment($sFilePath = null){
		if(is_null($sFilePath))$this->attachments = array();
		elseif(!is_string($sFilePath))throw new \InvalidArgumentException('File path exptects string ,"'.gettype($sFilePath).'" given');
		elseif(($iKey = array_search($sFilePath,$this->attachments)) === false)throw new \InvalidArgumentException('File path "'.$this->attachments.'" is not an attachment');
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
