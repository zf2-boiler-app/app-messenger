<?php
namespace BoilerAppMessenger\Media\Mail;
class MailMessageTransporter extends \BoilerAppMessenger\Message\AbstractMessageTransporter{

	/**
	 * @var string
	 */
	protected $baseDir;

	/**
	 * @var array
	 */
	protected $attachments = array();

	/**
	 * @var \Zend\Mail\Transport\TransportInterface
	 */
	protected $mailTransporter;

	/**
	 * @see \BoilerAppMessenger\Message\MessageTransporterInterface::sendMessage()
	 * @param \BoilerAppMessenger\Message\Message $oMessage
	 * @throws \UnexpectedValueException
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageTransporter
	 */
	public function sendMessage(\BoilerAppMessenger\Message\Message $oMessage){
		//Adapt message
		$oAdaptedMessage = new \Zend\Mail\Message();
		$oAdaptedMessage->setEncoding('UTF-8');

		//From Sender
		$oFrom = $oMessage->getFrom();
		if($oFrom instanceof \BoilerAppMessenger\Media\Mail\MailMessageUserInterface)$oAdaptedMessage->setFrom(
			$oFrom->getUserEmail(),
			$oFrom->getUserDisplayName()
		);
		else throw new \UnexpectedValueException(sprintf(
			'"From" sender expects an instance of \BoilerAppMessenger\Mail\MailMessageUserInterface, "%s" given',
			is_scalar($oFrom)?$oFrom:(is_object($oFrom)?get_class($oFrom):gettype($oFrom))
		));

		//To Recipiants
		foreach($oMessage->getTo() as $oTo){
			if($oTo instanceof \BoilerAppMessenger\Media\Mail\MailMessageUserInterface)$oAdaptedMessage->addTo(
				$oTo->getUserEmail(),
				$oTo->getUserDisplayName()
			);
			else throw new \UnexpectedValueException(sprintf(
				'"To" Recipiant expects an instance of \BoilerAppMessenger\Mail\MailMessageUserInterface, "%s" given',
				is_scalar($oTo)?$oTo:(is_object($oTo)?get_class($oTo):gettype($oTo))
			));
		}

		//Subject
		$oAdaptedMessage->setSubject($oMessage->getSubject());

		//Reset attachments
		$this->attachments = array();

		foreach($oMessage->getAttachments() as $sAttachmentFilePath){
			$this->addFileAttachment($sAttachmentFilePath);
		}

		//Body
		$oBodyPart = new \Zend\Mime\Part(preg_replace_callback('/src="([^"]+)"/',array($this,'processImageSrc'),$this->getMessageRenderer()->renderMessageBody($oMessage)));
		$oBodyPart->type = \Zend\Mime\Mime::TYPE_HTML;
		$oBody = new \Zend\Mime\Message();
		$oBody->setParts(array_merge(array($oBodyPart),$this->attachments));
		$oAdaptedMessage->setBody($oBody)->setEncoding('UTF-8');

		//Send message
		$this->getMailTransporter()->send($oAdaptedMessage);

		return $this;
	}

	/**
	 * Add image to attachments
	 * @param array $aMatches
	 * @throws \Exception
	 * @return string
	 */
	protected function processImageSrc(array $aMatches){
		if(empty($aMatches[1]))throw new \InvalidArgumentException('Image "src" match is empty: '.print_r($aMatches));

		$sImgUrl = urldecode($aMatches[1]);
		if(!file_exists($sImgUrl) && ($sBaseDir = $this->getBaseDir()) && false === strpos($sImgUrl,'://'))$sImgUrl = $sBaseDir.'/'.current(explode('?',$sImgUrl));
		$oAttachment = $this->addFileAttachment($sImgUrl,\Zend\Mime\Mime::DISPOSITION_INLINE);
		return 'src="cid:'.$oAttachment->id.'"';
	}

	/**
	 * @param string $sFilePath
	 * @param string $sDisposition
	 * @throws \InvalidArgumentException
	 * @return \Zend\Mime\Part
	 */
	protected function addFileAttachment($sAttachmentFilePath,$sDisposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT){
		if(!is_readable($sAttachmentFilePath) || ($sFileContent = file_get_contents($sAttachmentFilePath)) === false)throw new \InvalidArgumentException('Attachment file not found : '.$sAttachmentFilePath);

		$oAttachment = new \Zend\Mime\Part($sFileContent);
		$oFInfo = new \finfo(FILEINFO_MIME_TYPE);
		$oAttachment->type = $oFInfo->buffer($sFileContent);
		$oAttachment->description = $oAttachment->filename = pathinfo($sAttachmentFilePath,PATHINFO_FILENAME);
		$oAttachment->id = md5(uniqid());
		$oAttachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
		$oAttachment->disposition = $sDisposition;
		$this->attachments[] = $oAttachment;
		return $oAttachment;
	}

	/**
	 * @param \Zend\Mail\Transport\TransportInterface $oTransporter
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageTransporter
	 */
	public function setMailTransporter(\Zend\Mail\Transport\TransportInterface $oMailTransporter){
		$this->mailTransporter = $oMailTransporter;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \Zend\Mail\Transport\TransportInterface
	 */
	public function getMailTransporter(){
		if($this->mailTransporter instanceof \Zend\Mail\Transport\TransportInterface)return $this->mailTransporter;
		throw new \LogicException('Mail transporter is undefined');
	}

	/**
	 * @param string $sBaseDir
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Media\Mail\MailMessageTransporter
	 */
	public function setBaseDir($sBaseDir){
		if(
			(is_dir($sBaseDir) && ($sBaseDir = realpath($sBaseDir)))
			|| ($sBaseDir = filter_var($sBaseDir,FILTER_VALIDATE_URL)) !== false
		)$this->baseDir = $sBaseDir;
		else throw new \InvalidArgumentException(sprintf(
			'Base dir expects a valid directory or a valid url, "%s" given',
			is_scalar($sBaseDir)?$sBaseDir:(is_object($sBaseDir)?get_class($sBaseDir):gettype($sBaseDir))
		));
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return string
	 */
	public function getBaseDir(){
		if(is_string($this->baseDir))return $this->baseDir;
		throw new \LogicException('Base dir is undefined');
	}
}