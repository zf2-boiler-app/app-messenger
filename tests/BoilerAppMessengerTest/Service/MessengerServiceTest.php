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

		//Set fake http host
		$_SERVER['HTTP_HOST'] = 'boiler-app-messenger-test.com';
		$this->messengerService = $oMessengerServiceFactory->createService($this->getServiceManager());
	}

	public function testSendMessage(){
		$sMailDir = getcwd().'/_files/mails';

		//Empty mails directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($sMailDir, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Empty cache directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(getcwd().'/_files/cache', \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Create message
		$oMessage = new \BoilerAppMessenger\Message();

		//"From" user
		$oFromUser = new \BoilerAppUser\Entity\UserEntity();
		$oFromUserAuthAccess = new \BoilerAppAccessControl\Entity\AuthAccessEntity();

		$oMessage->setSubject('test subject')->setBody('test body')->setFrom(
			$oFromUser->setUserAuthAccess(
				$oFromUserAuthAccess->setAuthAccessEmailIdentity('test-from-user@test.com')
			)
			->setUserDisplayName('Test "From" User')
		);

		//Send to system
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->sendMessage(
			$oMessage->setTo(\BoilerAppMessenger\Message::SYSTEM_USER),
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL
		));

		//Test mail content
		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/mails/test-send-message-system'),
			preg_replace('/(Date:[\S|\s]*)(From:)/', '$2', file_get_contents($sMailDir.DIRECTORY_SEPARATOR.current(array_diff(scandir($sMailDir), array('..', '.','.gitignore')))))
		);

		//Empty mails directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator($sMailDir, \RecursiveDirectoryIterator::SKIP_DOTS),
			\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Send to user
		$oToUser = new \BoilerAppUser\Entity\UserEntity();
		$oToUserAuthAccess = new \BoilerAppAccessControl\Entity\AuthAccessEntity();
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->sendMessage(
			$oMessage->setTo(
				$oToUser->setUserAuthAccess(
					$oToUserAuthAccess->setAuthAccessEmailIdentity('test-user@test.com')
				)
				->setUserDisplayName('Test "To" User')
			),
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL
		));

		//Test mail content
		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/mails/test-send-message-user'),
			preg_replace('/(Date:[\S|\s]*)(From:)/', '$2', file_get_contents($sMailDir.DIRECTORY_SEPARATOR.current(array_diff(scandir($sMailDir), array('..', '.','.gitignore')))))
		);
	}

	public function testRenderView(){
		$oView = new \Zend\View\Model\ViewModel(array(
			'testValue' => 'this is a test value'
		));
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->renderView(
			$oView->setTemplate('email/simple-view'),
			array($this,'renderViewCallback')
		));
	}

	/**
	 * Callback for "testRenderView" function
	 * @param string $sHtml
	 */
	public function renderViewCallback($sHtml){
		$this->assertEquals(file_get_contents(getcwd().'/_files/expected/simple-view.phtml'), $sHtml);
	}
}