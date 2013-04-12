<?php
namespace BoilerAppMessenger\StyleInliner\Processor;
abstract class AbstractProcessor implements \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface{

	/**
	 * @var string
	 */
	protected $baseDir;

	/**
	 * Instantiate a messenger service
	 * @param array|Traversable $aOptions
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public static function factory($aOptions = array()){
		if($aOptions instanceof \Traversable)$aOptions = \Zend\Stdlib\ArrayUtils::iteratorToArray($aOptions);
		elseif(!is_array($aOptions))throw new \InvalidArgumentException(__METHOD__.' expects an array or Traversable object; received "'.(is_object($aOptions)?get_class($aOptions):gettype($aOptions)).'"');
		$oProcessor = new static();
		if(isset($aOptions['baseDir']))$oProcessor->setBaseDir($aOptions['baseDir']);
		return $oProcessor;
	}

	/**
	 * @param string $sBaseDir
	 * @throws \InvalidArgumentException
	 * @return \BoilerAppMessenger\StyleInliner\Processor\AbstractProcessor
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