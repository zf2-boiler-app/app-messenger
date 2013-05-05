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

        //Define message system user
        if(isset($aOptions['messenger']['system_user']) && is_array($aOptions['messenger']['system_user'])){
        	$oMessageUser = new \BoilerAppMessenger\MessageUser();

        	if(isset($aOptions['messenger']['system_user']['display_name'])){
        		if(is_string($aOptions['messenger']['system_user']['display_name']))$oMessageUser->setUserDisplayName($aOptions['messenger']['system_user']['display_name']);
        		else throw new \InvalidArgumentException('system user display name expects string, "'.gettype($aOptions['messenger']['system_user']['display_name']).'" given');
        	}
        	if(isset($aOptions['messenger']['system_user']['email'])){
        		if(($sEmail = filter_var($aOptions['messenger']['system_user']['email'],FILTER_VALIDATE_EMAIL)))$oMessageUser->setEmail($sEmail);
        		else throw new \InvalidArgumentException(sprintf(
        			'system user email expects valid email adress, "%s" given',
        			is_scalar($aOptions['messenger']['system_user']['email'])
        				?$aOptions['messenger']['system_user']['email']
        				:(is_object($aOptions['messenger']['system_user']['email'])?get_class($aOptions['messenger']['system_user']['email']):gettype($aOptions['messenger']['system_user']['email']))
        		));
        	}
        	$aOptions['messenger']['system_user'] = $oMessageUser;
        }

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
				if(!isset($oTransporter['type']))throw new \LogicException('Transporter config expects "type" key, "'.join(', ',array_keys($oTransporter)).'" given');

				$sTransporterType = $oTransporter['type'];
				unset($oTransporter['type']);

				if(class_exists($sTransporterType))$oTransporter = new $sTransporterType($oServiceLocator,$oTransporter);
				elseif($oServiceLocator->has($sTransporterType))$oTransporter = $oServiceLocator->get($sTransporterType);
				else throw new \LogicException('Transporter "'.$sTransporterType.'" is not an existing service or class');
        	}
        	$oMessengerService->setTransporter($oTransporter, $sMedia);
        }



        return $oMessengerService;
    }
}