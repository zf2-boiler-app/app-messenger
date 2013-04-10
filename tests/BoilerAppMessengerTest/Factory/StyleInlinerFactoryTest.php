<?php
namespace BoilerAppMessengerTest\Factory;
class StyleInlinerFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	public function testCreateService(){
		$oStyleInlinerFactory = new \BoilerAppMessenger\Factory\StyleInlinerFactory();
    	$this->assertInstanceOf('BoilerAppMessenger\StyleInliner\StyleInlinerService',$oStyleInlinerFactory->createService($this->getServiceManager()));
    }
}