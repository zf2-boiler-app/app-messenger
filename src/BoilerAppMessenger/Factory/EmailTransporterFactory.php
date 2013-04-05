<?php
namespace BoilerAppMessenger\Factory;
class EmailTransporterFactory implements \Zend\ServiceManager\FactoryInterface{
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
    	return new \BoilerAppMessenger\Mail\Transport\Sendmail();
    }
}