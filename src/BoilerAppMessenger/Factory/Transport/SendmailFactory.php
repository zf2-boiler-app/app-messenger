<?php
namespace BoilerAppMessenger\Factory\Transport;
class SendmailFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\Mail\Transport\Sendmail
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oSendmailTransport = new \BoilerAppMessenger\Mail\Transport\Sendmail();
		//Retrieve base dir
		if(
			$oServiceLocator->has('ViewHelperManager')
			&& ($oViewHelperManager = $oServiceLocator->get('ViewHelperManager')) instanceof \Zend\View\HelperPluginManager
			&& $oViewHelperManager->has('ServerUrl')
			&& ($oServerUrl = $oViewHelperManager->get('ServerUrl')) instanceof \Zend\View\Helper\ServerUrl
			&& $sServerUrl = $oServerUrl()
		)$oSendmailTransport->setBaseDir($sServerUrl);
    	return $oSendmailTransport;
    }
}