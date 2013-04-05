<?php
namespace BoilerAppMessenger\Factory;
class InlineStyleProcessorFactory implements \Zend\ServiceManager\FactoryInterface{
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oInlineStyleProcessor = new \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor();
		return $oInlineStyleProcessor->setServerUrl($oServiceLocator->get('ViewHelperManager')->get('ServerUrl')->__invoke());
	}
}