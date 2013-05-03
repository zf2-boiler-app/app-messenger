<?php
namespace BoilerAppMessengerTest\Factory;
class CssToInlineStylesProcessorFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	public function testCreateService(){
		//Set fake http host
		$_SERVER['HTTP_HOST'] = 'boiler-app-messenger-test.com';
		$oCssToInlineStylesProcessorFactory = new \BoilerAppMessenger\Factory\CssToInlineStylesProcessorFactory();
		$this->assertInstanceOf('BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor',$oCssToInlineStylesProcessorFactory->createService($this->getServiceManager()));
	}
}