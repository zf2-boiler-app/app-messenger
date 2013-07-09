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
        unset($aConfiguration['messenger']['transporters']);

        //Define message system user
        if(isset($aConfiguration['messenger']['system_user']) && is_array($aConfiguration['messenger']['system_user'])){
        	$oMessageUser = new \BoilerAppMessenger\Message\MessageUser();

        	if(isset($aConfiguration['messenger']['system_user']['display_name'])){
        		if(is_string($aConfiguration['messenger']['system_user']['display_name']))$oMessageUser->setUserDisplayName($aConfiguration['messenger']['system_user']['display_name']);
        		else throw new \InvalidArgumentException('system user display name expects string, "'.gettype($aConfiguration['messenger']['system_user']['display_name']).'" given');
        	}
        	if(isset($aConfiguration['messenger']['system_user']['email'])){
        		if(($sEmail = filter_var($aConfiguration['messenger']['system_user']['email'],FILTER_VALIDATE_EMAIL)))$oMessageUser->setUserEmail($sEmail);
        		else throw new \InvalidArgumentException(sprintf(
        			'system user email expects valid email adress, "%s" given',
        			is_scalar($aConfiguration['messenger']['system_user']['email'])
        				?$aConfiguration['messenger']['system_user']['email']
        				:(is_object($aConfiguration['messenger']['system_user']['email'])?get_class($aConfiguration['messenger']['system_user']['email']):gettype($aConfiguration['messenger']['system_user']['email']))
        		));
        	}
        	$aConfiguration['messenger']['system_user'] = $oMessageUser;
        }

        $oMessengerService = \BoilerAppMessenger\MessengerService::factory(
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
        	$oMessengerService->setMessageTransporter($oTransporter, $sMedia);
        }
        return $oMessengerService;
    }
}