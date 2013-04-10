<?php
namespace BoilerAppMessenger\Factory;
class MessengerServiceFactory implements \Zend\ServiceManager\FactoryInterface{
	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @throws \LogicException
	 * @return \BoilerAppMessenger\Service\MessengerService
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
        //Configure the messenger
        $aConfiguration = $oServiceLocator->get('Config');

        $aTransporters = isset($aConfiguration['messenger']['transporters']) && is_array($aConfiguration['messenger']['transporters'])?$aConfiguration['messenger']['transporters']:array();
        $aTemplatingConfig = isset($aConfiguration['messenger']['tree_layout_stack'])?$aConfiguration['messenger']['tree_layout_stack']:array();
        unset($aConfiguration['messenger']['transporters'],$aConfiguration['messenger']['tree_layout_stack']);

        $oMessengerService = \BoilerAppMessenger\Service\MessengerService::factory(
        	isset($aConfiguration['messenger'])?$aConfiguration['messenger']:array()
        );

        //Define transporters
        if($aTransporters)foreach($aTransporters as $sMedia => $oTransporter){
        	if(is_callable($oTransporter))$oTransporter = call_user_func($oTransporter,$oServiceLocator);
        	elseif(is_string($oTransporter)){
        		if(class_exists($oTransporter))$oTransporter = new $oTransporter($oServiceLocator);
        		elseif($oServiceLocator->has($oTransporter))$oTransporter = $oServiceLocator->get($oTransporter);
        		else throw new \LogicException('Transporter "'.$oTransporter.'" is not an existing service or class');
        	}
        	elseif(is_array($oTransporter)){
				if(!isset($oTransporter['type']))throw new \LogicException('Transporter config expects "type" key, "'.array_keys($oTransporter).'" given');

				$sTransporterType = $oTransporter['type'];
				unset($oTransporter['type']);

				if(class_exists($sTransporterType))$oTransporter = new $sTransporterType($oServiceLocator,$oTransporter);
				elseif($oServiceLocator->has($sTransporterType))$oTransporter = $oServiceLocator->get($sTransporterType);
				else throw new \LogicException('Transporter "'.$sTransporterType.'" is not an existing service or class');
        	}
        	$oMessengerService->setTransporter($oTransporter, $sMedia);
        }

        //Define services
        if($oServiceLocator->has('AssetsBundleService'))$oMessengerService->setAssetsBundleService($oServiceLocator->get('AssetsBundleService'));
        if($oServiceLocator->has('StyleInliner'))$oMessengerService->setStyleInliner($oServiceLocator->get('StyleInliner'));
        if($oServiceLocator->has('translator'))$oMessengerService->setTranslator($oServiceLocator->get('translator'));
        if($oServiceLocator->has('router'))$oMessengerService->setRouter($oServiceLocator->get('router'));
        $oMessengerService->setTemplatingService(\TreeLayoutStack\TemplatingService::factory($aTemplatingConfig));

        return $oMessengerService;
    }
}