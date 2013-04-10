<?php
namespace BoilerAppMessengerTest\Factory\Transport;
class SendmailFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	public function testCreateService(){
		$oSendmailFactory = new \BoilerAppMessenger\Factory\Transport\SendmailFactory();
		$this->assertInstanceOf('BoilerAppMessenger\Mail\Transport\Sendmail',$oSendmailFactory->createService($this->getServiceManager()));
	}
}