<?php
namespace BoilerAppMessengerTest\Factory;
class MessengerServiceFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	public function testCreateService(){
		$oMessengerServiceFactory = new \BoilerAppMessenger\Factory\MessengerServiceFactory();
		$this->assertInstanceOf('BoilerAppMessenger\Service\MessengerService',$oMessengerServiceFactory->createService($this->getServiceManager()));
	}
}