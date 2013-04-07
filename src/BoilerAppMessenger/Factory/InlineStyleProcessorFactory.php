<?php
namespace BoilerAppMessenger\Factory;
class InlineStyleProcessorFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oInlineStyleProcessor = new \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor();
		if($sServerUrl = $oServiceLocator->get('ViewHelperManager')->get('ServerUrl')->__invoke())$oInlineStyleProcessor->setServerUrl($sServerUrl);
		return $oInlineStyleProcessor;
	}
}