<?php
namespace BoilerAppMessengerTest;
class MessengerServiceSendMessageTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\MessengerService
	 */
	protected $messengerService;

	/**
	 * @var \BoilerAppMessenger\Message\Message
	 */
	protected $message;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oMessengerServiceFactory = new \BoilerAppMessenger\Factory\MessengerServiceFactory();

		//Empty mails directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(getcwd().'/tests/_files/mails', \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Empty cache directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator(getcwd().'/tests/_files/cache', \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Set fake http host
		$_SERVER['HTTP_HOST'] = 'boiler-app-messenger-test.com';

		//Initialize messenger service
		$this->messengerService = $oMessengerServiceFactory->createService($this->getServiceManager());

		//Create a test message
		$this->message = new \BoilerAppMessenger\Message\Message();
		$oBody = new \Zend\View\Model\ViewModel(array('testValue' => 'test body <img src="_files/images/test.gif"/>'));
		$this->message
			->setSubject('Test subject')
			->setBody($oBody->setTemplate('mail/simple-view'))
			->addAttachment(getcwd().'/tests/_files/attachments/attachment-test.txt');
	}

	public function testSendMessageFromUserToSystem(){
		//"From" user
		$oFromUser = new \BoilerAppMessenger\Message\MessageUser();
		$oFromUser->setUserDisplayName('Test "From" User')->setUserEmail('test-from-user@test.com');

		//Set from "User" to "System"
		$this->message->setFrom($oFromUser)->setTo($this->messengerService->getSystemUser());

		//Send message
		$this->assertEquals($this->messengerService,$this->messengerService->sendMessage($this->message,'mail'));

		//Test mail content
		$this->assertMessageContent('test-send-message-user-to-system');
	}

	public function testSendMessageFromUserToUser(){
		//"From" user
		$oFromUser = new \BoilerAppMessenger\Message\MessageUser();
		$oFromUser->setUserDisplayName('Test "From" User')->setUserEmail('test-from-user@test.com');

		//Send to user
		$oToUser = new \BoilerAppMessenger\Message\MessageUser();
		$oToUser->setUserDisplayName('Test "To" User')->setUserEmail('test-user@test.com');

		//Set from "User" to "User"
		$this->message->setFrom($oFromUser)->setTo($oToUser);

		//Send message
		$this->assertEquals($this->messengerService,$this->messengerService->sendMessage(
			$this->message,
			'mail'
		));

		//Test mail content
		$this->assertMessageContent('test-send-message-user-to-user');
	}


	public function testSendMessageFromSystemToUser(){
		//Send to user
		$oToUser = new \BoilerAppMessenger\Message\MessageUser();
		$oToUser->setUserDisplayName('Test "To" User')->setUserEmail('test-user@test.com');

		//Set from "System" to "User"
		$this->message->setFrom($this->messengerService->getSystemUser())->setTo($oToUser);

		//Send message
		$this->assertEquals($this->messengerService,$this->messengerService->sendMessage(
			$this->message,
			'mail'
		));

		//Test mail content
		$this->assertMessageContent('test-send-message-system-to-user');
	}

	public function testSendMessageFromSystemToSystem(){
		//Set from "System" to "System"
		$this->message->setFrom($this->messengerService->getSystemUser())->setTo($this->messengerService->getSystemUser());

		//Send message
		$this->assertEquals($this->messengerService,$this->messengerService->sendMessage(
			$this->message,
			'mail'
		));

		//Test mail content
		$this->assertMessageContent('test-send-message-system-to-system');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSendMessageWithWrongTypeMedias(){
		$this->messengerService->sendMessage(new \BoilerAppMessenger\Message\Message(), new \stdClass());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testSendMessageWithUnknownMedia(){
		$this->messengerService->sendMessage(new \BoilerAppMessenger\Message\Message(),'wrong');
	}

	/**
	 * Test message content
	 * @param string $sExpectedFile
	 */
	public function assertMessageContent($sExpectedFile){
		$sMailDir = getcwd().'/tests/_files/mails';

		$sMailContent = preg_replace(
			array('/(Date:[\S|\s]*)(From:)/','/(Content-ID: <)[a-f0-9]*(>)/','/(src="cid:)[a-f0-9]*(")/'),
			array('$2','$1content-id$2','$1image-id$2'),
			file_get_contents($sMailDir.DIRECTORY_SEPARATOR.current(array_diff(scandir($sMailDir), array('..', '.','.gitignore'))))
		);

		//Retreive boundary
		$this->assertEquals(1,preg_match('/boundary="=_([a-f0-9]*)"/', $sMailContent,$aMatches));
		$this->assertArrayHasKey(1, $aMatches);

		//Replace boundary by static word
		$sMailContent = str_ireplace($aMatches[1],'boundary',$sMailContent);

		//Test mail content
		$this->assertStringEqualsFile(
			getcwd().'/tests/_files/expected/mails/'.$sExpectedFile,
			str_replace(PHP_EOL,"\n",$sMailContent)
		);
	}
}