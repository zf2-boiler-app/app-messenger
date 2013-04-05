<?php
namespace BoilerAppMessengerTest\Service;
class MessengerServiceTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Service\MessengerService
	 */
	protected $messengerService;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oMessengerServiceFactory = new \BoilerAppMessenger\Factory\MessengerServiceFactory();
		$this->messengerService = $oMessengerServiceFactory->createService($this->getServiceManager());
	}

	public function testSendMessage(){
		$oMessage = new \BoilerAppMessenger\Message();
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->sendMessage(
			$oMessage->setTo(\BoilerAppMessenger\Message::SYSTEM_USER),
			$oMessage->setFrom(\BoilerAppMessenger\Message::SYSTEM_USER),
			$oMessage->setSubject('test subject'),
			$oMessage->setBody('test body'),
			array(\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL)
		));
	}

	public function testRenderView(){
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->renderView($oView, array($this,'renderViewCallback')));
	}

	public function renderViewCallback($sHtml){
		$this->assertEquals('', $sHtml);
	}

	public function getSharedManager(){
		$this->assertInstanceOf('\Zend\EventManager\SharedEventManagerInterface', $this->messengerService->getSharedManager());
	}

	public function unsetSharedManager(){
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService', $this->messengerService->unsetSharedManager());
	}
}