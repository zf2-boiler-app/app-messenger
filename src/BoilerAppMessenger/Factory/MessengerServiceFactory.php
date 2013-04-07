<?php
namespace BoilerAppMessenger\Factory;
class MessengerServiceFactory implements \Zend\ServiceManager\FactoryInterface{
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
        //Configure the messenger
        $aConfiguration = $oServiceLocator->get('Config');
        if(isset($aConfiguration['messenger']['transporters'])
        && is_array($aConfiguration['messenger']['transporters']))foreach($aConfiguration['messenger']['transporters'] as $sMedia => $oTransporter){
        	if(is_string($oTransporter)){
        		if(class_exists($oTransporter))$aConfiguration['messenger']['transporters'][$sMedia] = new $oTransporter();
        		elseif($oServiceLocator->has($oTransporter))$aConfiguration['messenger']['transporters'][$sMedia] = $oServiceLocator->get($oTransporter);
        	}
        }

        $oMessengerService = \BoilerAppMessenger\Service\MessengerService::factory(
        	isset($aConfiguration['messenger'])?$aConfiguration['messenger']:array()
        );

        if($oServiceLocator->has('AssetsBundleService'))$oMessengerService->setAssetsBundleService($oServiceLocator->get('AssetsBundleService'));
        if($oServiceLocator->has('StyleInliner'))$oMessengerService->setStyleInliner($oServiceLocator->get('StyleInliner'));
        if($oServiceLocator->has('translator'))$oMessengerService->setTranslator($oServiceLocator->get('translator'));
        if($oServiceLocator->has('router'))$oMessengerService->setRouter($oServiceLocator->get('router'));
        return $oMessengerService;

    }
}