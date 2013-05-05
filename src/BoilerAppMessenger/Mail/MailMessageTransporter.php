<?php
namespace BoilerAppMessenger\Mail;
class MailMessageTransporter implements \BoilerAppMessenger\MessageTransporter\MessageTransporterInterface{
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
	protected $transporter;

	/**
	 * @see \BoilerAppMessenger\MessageTransporter\MessageTransporterInterface::sendMessage()
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @throws \UnexpectedValueException
	 * @return \BoilerAppMessenger\Mail\MailMessageTransporter
	 */
	public function sendMessage(\BoilerAppMessenger\Message $oMessage){
		//Adapt message
		$oAdaptedMessage = new \BoilerAppMessenger\Mail\MailMessage();
		$oAdaptedMessage->setEncoding('UTF-8');

		//From Sender
		$oFrom = $oMessage->getFrom();
		if($oFrom === \BoilerAppMessenger\Message::SYSTEM_USER)$oAdaptedMessage->setFrom(
			$this->getOptions()->getSystemUserEmail(),
			$this->getOptions()->getSystemUserName()
		);
		elseif($oFrom instanceof \BoilerAppUser\Entity\UserEntity)$oAdaptedMessage->setFrom(
			$oFrom->getUserAuthAccess()->getAuthAccessEmailIdentity(),
			$oFrom->getUserDisplayName()
		);
		else throw new \UnexpectedValueException(sprintf(
			'"From" sender expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity, "%s" given',
			is_scalar($oFrom)?$oFrom:(is_object($oFrom)?get_class($oFrom):gettype($oFrom))
		));

		//To Recipiants
		foreach($oMessage->getTo() as $oTo){
			if($oTo === \BoilerAppMessenger\Message::SYSTEM_USER)$oAdaptedMessage->addTo(
				$this->getOptions()->getSystemUserEmail(),
				$this->getOptions()->getSystemUserName()
			);
			elseif($oTo instanceof \BoilerAppUser\Entity\UserEntity)$oAdaptedMessage->addTo(
				$oTo->getUserAuthAccess()->getAuthAccessEmailIdentity(),
				$oTo->getUserDisplayName()
			);
			else throw new \UnexpectedValueException('"To" Recipiant expects \BoilerAppMessenger\Message::SYSTEM_USER or \BoilerAppUser\Entity\UserEntity');
		}

		//Subject
		$oAdaptedMessage->setSubject($oMessage->getSubject());

		//Reset attachments
		$this->attachments = array();

		//Body
		$oBodyPart = new \Zend\Mime\Part(preg_replace_callback('/src="([^"]+)"/',array($this,'processImageSrc'),$oMessage->getBody()));
		$oBodyPart->type = \Zend\Mime\Mime::TYPE_HTML;
		$oBody = new \Zend\Mime\Message();
		$oBody->setParts(array_merge(array($oBodyPart),$this->attachments));
		$oMessage->setBody($oBody)->setEncoding('UTF-8');

		//Send message
		$this->getTransporter()->send($oAdaptedMessage);

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
		if(!file_exists($sImgUrl) && ($sBaseDir = $this->getBaseDir()) && false === strpos($sImgUrl, '://'))$sImgUrl = $sBaseDir.'/'.current(explode('?',$sImgUrl));
		$oAttachment = $this->addAttachment($sImgUrl,\Zend\Mime\Mime::DISPOSITION_INLINE);
		return 'src="cid:'.$oAttachment->id.'"';
	}

	/**
	 * @param string $sFilePath
	 * @throws \Exception
	 * @return \Zend\Mime\Part
	 */
	protected function addAttachment($sFilePath,$sDisposition = \Zend\Mime\Mime::DISPOSITION_ATTACHMENT){
		if(!is_readable($sFilePath) || ($sFileContent = file_get_contents($sFilePath)) === false)throw new \InvalidArgumentException('Attachment file not found : '.$sFilePath);

		$oAttachment = new \Zend\Mime\Part($sFileContent);
		$oFInfo = new \finfo(FILEINFO_MIME_TYPE);
		$oAttachment->type = $oFInfo->buffer($sFileContent);
		$oAttachment->description = $oAttachment->filename = pathinfo($sFilePath,PATHINFO_FILENAME);
		$oAttachment->id = md5(uniqid());
		$oAttachment->encoding = \Zend\Mime\Mime::ENCODING_BASE64;
		$oAttachment->disposition = $sDisposition;
		$this->attachments[] = $oAttachment;
		return $oAttachment;
	}

	/**
	 * @param \Zend\Mail\Transport\TransportInterface $oTransporter
	 * @return \BoilerAppMessenger\MessageTransporter\MessageTransporterInterface
	 */
	public function setTransporter(\Zend\Mail\Transport\TransportInterface $oTransporter){
		$this->transporter = $oTransporter;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \Zend\Mail\Transport\TransportInterface
	 */
	public function getTransporter(){
		if($this->transporter instanceof \Zend\Mail\Transport\TransportInterface)return $this->transporter;
		throw new \LogicException('Transporter is undefined');
	}

	/**
	 * @param string $sBaseDir
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\Mail\Transport\AttachementsAwareTrait
	*/
	public function setBaseDir($sBaseDir){
		if(
		(is_dir($sBaseDir) && ($sBaseDir = realpath($sBaseDir)))
		|| ($sBaseDir = filter_var($sBaseDir,FILTER_VALIDATE_URL)) !== false
		)$this->baseDir = $sBaseDir;
		else throw new \InvalidArgumentException(sprintf(
				'base dir expects valid directory or valid url, "%s" given',
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
		throw new \LogicException('Base dir option is undefined');
	}
}