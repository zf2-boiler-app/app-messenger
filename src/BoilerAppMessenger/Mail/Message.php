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
