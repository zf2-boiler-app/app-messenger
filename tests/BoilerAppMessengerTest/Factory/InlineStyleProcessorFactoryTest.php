<?php
namespace BoilerAppMessengerTest\Factory;
class InlineStyleProcessorFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	public function testCreateService(){
		//Set fake http host
		$_SERVER['HTTP_HOST'] = 'boiler-app-messenger-test.com';
		$oInlineStyleProcessorFactory = new \BoilerAppMessenger\Factory\InlineStyleProcessorFactory();
		$this->assertInstanceOf('BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor',$oInlineStyleProcessorFactory->createService($this->getServiceManager()));
	}
}