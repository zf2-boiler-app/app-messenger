<?php
namespace BoilerAppMessengerTest;
class MessengerServiceTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\MessengerService
	 */
	protected $messengerService;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oMessengerServiceFactory = new \BoilerAppMessenger\Factory\MessengerServiceFactory();

		//Initialize messenger service
		$this->messengerService = $oMessengerServiceFactory->createService($this->getServiceManager());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetOptionsUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\MessengerService');
		$oOptions = $oReflectionClass->getProperty('options');
		$oOptions->setAccessible(true);
		$oOptions->setValue($this->messengerService, null);

		$oGetOptions = $oReflectionClass->getMethod('getOptions');
		$oGetOptions->setAccessible(true);
		$oGetOptions->invokeArgs($this->messengerService,array());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetTransporterWithWrongTypeMedia(){
		$this->messengerService->setMessageTransporter(new \BoilerAppMessenger\Media\Mail\MailMessageTransporter(),new \stdClass());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetTransporterWithWrongTypeMedia(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\MessengerService');
		$oGetMessageTransporter = $oReflectionClass->getMethod('getMessageTransporter');
		$oGetMessageTransporter->setAccessible(true);
		$oGetMessageTransporter->invokeArgs($this->messengerService,array(new \stdClass()));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetTransporterWithUnknownMedia(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\MessengerService');
		$oGetMessageTransporter = $oReflectionClass->getMethod('getMessageTransporter');
		$oGetMessageTransporter->setAccessible(true);
		$oGetMessageTransporter->invokeArgs($this->messengerService,array('wrong'));
	}
}