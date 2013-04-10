<?php
namespace BoilerAppMessenger\Factory\Transport;
class SendmailFactory implements \Zend\ServiceManager\FactoryInterface{

	/**
	 * @see \Zend\ServiceManager\FactoryInterface::createService()
	 * @param \Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator
	 * @return \BoilerAppMessenger\Mail\Transport\Sendmail
	 */
	public function createService(\Zend\ServiceManager\ServiceLocatorInterface $oServiceLocator){
    	return new \BoilerAppMessenger\Mail\Transport\Sendmail();
    }
}