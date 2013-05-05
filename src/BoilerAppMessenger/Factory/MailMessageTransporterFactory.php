<?php
namespace BoilerAppMessenger\Factory\Transport;
class MailMessageTransporterFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\Mail\MailMessageTransporter
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oMailMessageTransporter = new \BoilerAppMessenger\Mail\MailMessageTransporter();

		//Define mail transporter
		$oMailMessageTransporter->setTransporter(new \BoilerAppMessenger\Mail\Transport\Sendmail());

		//Retrieve base dir
		if(
			$oServiceLocator->has('ViewHelperManager')
			&& ($oViewHelperManager = $oServiceLocator->get('ViewHelperManager')) instanceof \Zend\View\HelperPluginManager
			&& $oViewHelperManager->has('ServerUrl')
			&& ($oServerUrl = $oViewHelperManager->get('ServerUrl')) instanceof \Zend\View\Helper\ServerUrl
			&& $sServerUrl = $oServerUrl()
		)$oMailMessageTransporter->setBaseDir($sServerUrl);
    	return $oMailMessageTransporter;
    }
}