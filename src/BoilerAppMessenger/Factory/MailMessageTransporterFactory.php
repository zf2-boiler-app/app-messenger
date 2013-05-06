<?php
namespace BoilerAppMessenger\Factory;
class MailMessageTransporterFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\Mail\MailMessageTransporter
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
		$oMailMessageTransporter = new \BoilerAppMessenger\Media\Mail\MailMessageTransporter();

		//Define message renderer
		$oMailMessageTransporter->setMessageRenderer($oServiceLocator->get('MailMessageRenderer'));

		//Define mail transporter
		$aConfiguration = $oServiceLocator->get('Config');
		if(isset($aConfiguration['medias']['mail']['mail_transporter'])){
			$oMailTransporter = $aConfiguration['medias']['mail']['mail_transporter'];
			if(is_callable($oMailTransporter))$oMailTransporter = call_user_func($oMailTransporter,$oServiceLocator);
        	elseif(is_string($oMailTransporter)){
        		if(class_exists($oMailTransporter))$oMailTransporter = new $oMailTransporter();
        		elseif($oServiceLocator->has($oMailTransporter))$oMailTransporter = $oServiceLocator->get($oMailTransporter);
        		else throw new \LogicException('Mail transporter "'.$oMailTransporter.'" is not an existing service or class');
        	}
        	elseif(is_array($oMailTransporter)){
				if(!isset($oMailTransporter['type']))throw new \LogicException('Mail transporter config expects "type" key, "'.join(', ',array_keys($oTransporter)).'" given');

				if($oServiceLocator->has($oMailTransporter['type']))$oMailTransporter = $oServiceLocator->get($oMailTransporter['type']);
				elseif(class_exists($sTransporterType = $oMailTransporter['type'])){
					unset($oMailTransporter['type']);
					$oMailTransporter = new $sTransporterType($oMailTransporter,$oServiceLocator);
				}
				else throw new \LogicException('Transporter "'.$sTransporterType.'" is not an existing service or class');
        	}
			$oMailMessageTransporter->setMailTransporter($oMailTransporter);
		}

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