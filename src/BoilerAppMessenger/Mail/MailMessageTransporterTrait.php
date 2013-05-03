<?php
namespace BoilerAppMessenger\Mail;
trait MailMessageTransporterTrait{

	/**
	 * @var string
	 */
	protected $baseDir;

	/**
	 * @var array
	 */
	protected $attachments = array();

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


	/**
	 * Prepare message body
	 * @param \BoilerAppMessenger\Mail\Message $oMessage
	 * @return string
	 */
	protected function prepareMessage(\BoilerAppMessenger\Mail\Message $oMessage){
		if($oMessage->hasattachments())foreach($oMessage->getAttachments() as $sAttachmentPath){
			$this->addAttachment($sAttachmentPath);
		}

		//Inline style / attach images
		$oBodyPart = new \Zend\Mime\Part(preg_replace_callback('/src="([^"]+)"/',array($this,'processImageSrc'),$oMessage->getBodyText()));
		$oBodyPart->type = \Zend\Mime\Mime::TYPE_HTML;
		$oBody = new \Zend\Mime\Message();
		$oBody->setParts(array_merge(array($oBodyPart),$this->attachments));
		return $oMessage->setBody($oBody)->setEncoding('UTF-8');
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
	 * Send email
	 * @param \Zend\Mail\Message $message
	 */
	public function send(\Zend\Mail\Message $oMessage){
		parent::send($this->prepareMessage($oMessage));
	}
}