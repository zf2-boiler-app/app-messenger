<?php
namespace BoilerAppMessenger\Factory;
class CssToInlineStylesProcessorFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$aOptions = array();

		//Retrieve base dir
		if(
			$oServiceLocator->has('ViewHelperManager')
			&& ($oViewHelperManager = $oServiceLocator->get('ViewHelperManager')) instanceof \Zend\View\HelperPluginManager
			&& $oViewHelperManager->has('ServerUrl')
			&& ($oServerUrl = $oViewHelperManager->get('ServerUrl')) instanceof \Zend\View\Helper\ServerUrl
			&& $sServerUrl = $oServerUrl()
		)$aOptions['baseDir'] = $sServerUrl;
		return \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory($aOptions);
	}
}