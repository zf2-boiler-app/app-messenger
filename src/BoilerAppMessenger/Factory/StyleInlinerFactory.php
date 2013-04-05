<?php
namespace BoilerAppMessenger\Factory;
class StyleInlinerFactory implements \Zend\ServiceManager\FactoryInterface{
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
    	$oOptions = new \BoilerAppMessenger\StyleInliner\StyleInlinerOptions();
    	$aConfiguration = $oServiceLocator->get('Config');
    	if(isset($aConfiguration['style_inliner']['processor'])){
    		$oProcessor = $aConfiguration['style_inliner']['processor'];
    		if($oProcessor instanceof \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface)$oOptions->setProcessor($oProcessor);
    		elseif(is_string($oProcessor) && $oServiceLocator->has($oProcessor))$oOptions->setProcessor($oServiceLocator->get($oProcessor));
    	}
    	return new \BoilerAppMessenger\StyleInliner\StyleInlinerService($oOptions);
    }
}