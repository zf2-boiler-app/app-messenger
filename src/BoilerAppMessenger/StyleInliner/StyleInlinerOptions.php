<?php
namespace BoilerAppMessenger\StyleInliner;
class StyleInlinerOptions extends \Zend\Stdlib\AbstractOptions{
	/**
	 * @var \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface
	 */
	protected $processor;

	/**
	 * @param \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface $oProcessor
	 * @return \BoilerAppMessenger\StyleInliner\StyleInlinerOptions
	 */
	public function setProcessor(\BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface $oProcessor){
		$this->processor = $oProcessor;
		return $this;
	}

	/**
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface
	 */
	public function getProcessor(){
		if($this->processor instanceof \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface)return $this->processor;
		throw new \LogicException('Processor option is undefined');
	}
}