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

		//Set fake request uri
		$_SERVER['HTTP_HOST'] = 'boiler-app-messenger-test.com';
		$this->messengerService = $oMessengerServiceFactory->createService($this->getServiceManager());
	}

	public function testSendMessage(){
		$oMessage = new \BoilerAppMessenger\Message();
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->sendMessage($oMessage
			->setTo(\BoilerAppMessenger\Message::SYSTEM_USER)
			->setFrom(\BoilerAppMessenger\Message::SYSTEM_USER)
			->setSubject('test subject')
			->setBody('test body'),
			array(\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL)
		));
	}

	public function testRenderView(){
		$oView = new \Zend\View\Model\ViewModel();
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->renderView(
			$oView->setTemplate('email/simple-view'),
			array($this,'renderViewCallback')
		));
	}

	public function renderViewCallback($sHtml){
		$this->assertEquals(file_get_contents(getcwd().'/_files/expected/simple-view.phtml'), $sHtml);
	}

	public function getSharedManager(){
		$this->assertInstanceOf('\Zend\EventManager\SharedEventManagerInterface', $this->messengerService->getSharedManager());
	}

	public function unsetSharedManager(){
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService', $this->messengerService->unsetSharedManager());
	}
}