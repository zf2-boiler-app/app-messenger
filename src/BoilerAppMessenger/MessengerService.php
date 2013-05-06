<?php
namespace BoilerAppMessenger;
class MessengerService{
	/**
	 * @var \BoilerAppMessenger\MessengerOptions
	 */
	private $options;

	/**
	 * @var array<\BoilerAppMessenger\Message\MessageTransporterInterface>
	 */
	private $messageTransporters = array();

	/**
	 * Constructor
	 * @param \BoilerAppMessenger\MessengerOptions $oOptions
	 */
	private function __construct(\BoilerAppMessenger\MessengerOptions $oOptions = null){
		if($oOptions)$this->setOptions($oOptions);
	}

	/**
	 * Instantiate a messenger service
	 * @param array|Traversable $aOptions
	 * @return \BoilerAppMessenger\MessengerService
	 */
	public static function factory($aOptions){
		if($aOptions instanceof \Traversable)$aOptions = \Zend\Stdlib\ArrayUtils::iteratorToArray($aOptions);
		elseif(!is_array($aOptions))throw new \InvalidArgumentException(__METHOD__.' expects an array or Traversable object; received "'.(is_object($aOptions)?get_class($aOptions):gettype($aOptions)).'"');
		return new static(new \BoilerAppMessenger\MessengerOptions($aOptions));
	}

	/**
	 * @param \BoilerAppMessenger\Message $oMessage
	 * @param string|array $aMedias
	 * @throws \InvalidArgumentException
	 * @throws \DomainException
	 * @return \BoilerAppMessenger\MessengerService
	 */
	public function sendMessage(\BoilerAppMessenger\Message\Message $oMessage,$aMedias){
		if(empty($aMedias))throw new \InvalidArgumentException('A media must be specified');
		elseif(is_string($aMedias))$aMedias = array($aMedias);
		elseif(!is_array($aMedias))throw new \InvalidArgumentException('$aMedias expects an array or a string, "'.gettype($aMedias).'" given');

		foreach(array_filter(array_unique($aMedias)) as $sMedia){
			$this->getMessageTransporter($sMedia)->sendMessage($oMessage);
		}
		return $this;
	}

	/**
	 * @return \BoilerAppMessenger\Message\MessageUserInterface
	 */
	public function getSystemUser(){
		return $this->getOptions()->getSystemUser();
	}

	/**
	 * @param \BoilerAppMessenger\MessengerOptions $oOptions
	 * @return \BoilerAppMessenger\MessengerService
	 */
	public function setOptions(\BoilerAppMessenger\MessengerOptions $oOptions){
		$this->options = $oOptions;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\MessengerOptions
	 */
	protected function getOptions(){
		if($this->options instanceof \BoilerAppMessenger\MessengerOptions)return $this->options;
		throw new \LogicException('Options are undefined');
	}

	/**
	 * @param \BoilerAppMessenger\Message\MessageTransporterInterface $oMessageTransporter
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\MessengerService
	 */
	public function setMessageTransporter(\BoilerAppMessenger\Message\MessageTransporterInterface $oMessageTransporter,$sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		$this->messageTransporters[$sMedia] = $oMessageTransporter;
		return $this;
	}

	/**
	 * Retrieve message transporter for given media
	 * @param string $sMedia
	 * @throws \InvalidArgumentException
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\Message\MessageTransporterInterface
	 */
	protected function getMessageTransporter($sMedia){
		if(empty($sMedia) || !is_string($sMedia))throw new \InvalidArgumentException(sprintf(
			'Media expects string not empty, "%s" given',
			is_scalar($sMedia)?$sMedia:gettype($sMedia)
		));
		if(isset($this->messageTransporters[$sMedia]) && $this->messageTransporters[$sMedia] instanceof \BoilerAppMessenger\Message\MessageTransporterInterface)return $this->messageTransporters[$sMedia];
		else throw new \LogicException('Message transporter is not defined for media "'.$sMedia.'"');
	}
}