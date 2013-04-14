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

		$oMessage->setSubject('test subject')->setBody('test body <img src="_files/images/test.gif"/>')->setFrom(
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

		$sMailContent = preg_replace(
			array('/(Date:[\S|\s]*)(From:)/','/(Content-ID: <)[a-f0-9]*(>)/','/(src="cid:)[a-f0-9]*(")/','/(href="\/cache\/62626e2b77fc1188a3f021362c2d48a8\.css\?)[0-9]*(")/'),
			array('$2','$1content-id$2','$1image-id$2','$1cache-timstamp$2'),
			file_get_contents($sMailDir.DIRECTORY_SEPARATOR.current(array_diff(scandir($sMailDir), array('..', '.','.gitignore'))))
		);

		//Retreive boundary
		$this->assertEquals(1,preg_match('/boundary="=_([a-f0-9]*)"/', $sMailContent,$aMatches));
		$this->assertArrayHasKey(1, $aMatches);

		//Replace boundary by static word
		$sMailContent = str_ireplace($aMatches[1],'boundary',$sMailContent);

		//Test mail content
		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/mails/test-send-message-system'),
			$sMailContent
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

		$sMailContent = preg_replace(
			array('/(Date:[\S|\s]*)(From:)/','/(Content-ID: <)[a-f0-9]*(>)/','/(src="cid:)[a-f0-9]*(")/','/(href="\/cache\/62626e2b77fc1188a3f021362c2d48a8\.css\?)[0-9]*(")/'),
			array('$2','$1content-id$2','$1image-id$2','$1cache-timstamp$2'),
			file_get_contents($sMailDir.DIRECTORY_SEPARATOR.current(array_diff(scandir($sMailDir), array('..', '.','.gitignore'))))
		);

		//Retreive boundary
		$this->assertEquals(1,preg_match('/boundary="=_([a-f0-9]*)"/', $sMailContent,$aMatches));
		$this->assertArrayHasKey(1, $aMatches);

		//Replace boundary by static word
		$sMailContent = str_ireplace($aMatches[1],'boundary',$sMailContent);

		//Test mail content
		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/mails/test-send-message-user'),
			$sMailContent
		);

		//Send to system
		$this->assertInstanceOf('\BoilerAppMessenger\Service\MessengerService',$this->messengerService->sendMessage(
			$oMessage
				->setFrom(\BoilerAppMessenger\Message::SYSTEM_USER)
				->setTo(\BoilerAppMessenger\Message::SYSTEM_USER),
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL
		));
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
		file_put_contents(getcwd().'/_files/expected/simple-view.phtml', $sHtml);
		$this->assertEquals(file_get_contents(getcwd().'/_files/expected/simple-view.phtml'), $sHtml);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSendMessageWithWrongTypeMedias(){
		$this->messengerService->sendMessage(new \BoilerAppMessenger\Message(), new \stdClass());
	}

	/**
	 * @expectedException DomainException
	 */
	public function testSendMessageWithUnknownMedia(){
		$this->messengerService->sendMessage(new \BoilerAppMessenger\Message(),'wrong');
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testSendMessageWithWrongFrom(){
		$oMessage = new \BoilerAppMessengerTest\WrongMessage();
		$this->messengerService->sendMessage(
			$oMessage->setFrom('wrong'),
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL
		);
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testSendMessageWithWrongTo(){
		$oMessage = new \BoilerAppMessengerTest\WrongMessage();
		$this->messengerService->sendMessage(
			$oMessage
			->setFrom(\BoilerAppMessenger\Message::SYSTEM_USER)
			->addTo('wrong'),
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL
		);
	}

	/**
	 * @expectedException DomainException
	 */
	public function testFormatMessageForMediaWithWrongMedia(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oFormatMessageForMedia = $oReflectionClass->getMethod('formatMessageForMedia');
		$oFormatMessageForMedia->setAccessible(true);
		$oFormatMessageForMedia->invokeArgs($this->messengerService,array(new \BoilerAppMessenger\Message(),'wrong'));
	}

	/**
	 * @expectedException DomainException
	 */
	public function testGetRendererWithWrongMedia(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oGetRenderer = $oReflectionClass->getMethod('getRenderer');
		$oGetRenderer->setAccessible(true);
		$oGetRenderer->invokeArgs($this->messengerService,array('wrong'));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetOptionsUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oOptions = $oReflectionClass->getProperty('options');
		$oOptions->setAccessible(true);
		$oOptions->setValue($this->messengerService, null);

		$oGetOptions = $oReflectionClass->getMethod('getOptions');
		$oGetOptions->setAccessible(true);
		$oGetOptions->invokeArgs($this->messengerService,array());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetAssetsBundleServiceUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oAssetsBundleService = $oReflectionClass->getProperty('assetsBundleService');
		$oAssetsBundleService->setAccessible(true);
		$oAssetsBundleService->setValue($this->messengerService, null);

		$oGetAssetsBundleService = $oReflectionClass->getMethod('getAssetsBundleService');
		$oGetAssetsBundleService->setAccessible(true);
		$oGetAssetsBundleService->invokeArgs($this->messengerService,array());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetTemplatingServiceUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oTemplatingService = $oReflectionClass->getProperty('templatingService');
		$oTemplatingService->setAccessible(true);
		$oTemplatingService->setValue($this->messengerService, null);

		$oGetTemplatingService = $oReflectionClass->getMethod('getTemplatingService');
		$oGetTemplatingService->setAccessible(true);
		$oGetTemplatingService->invokeArgs($this->messengerService,array());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetStyleInlinerUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oStyleInliner = $oReflectionClass->getProperty('styleInliner');
		$oStyleInliner->setAccessible(true);
		$oStyleInliner->setValue($this->messengerService, null);

		$oGetStyleInliner = $oReflectionClass->getMethod('getStyleInliner');
		$oGetStyleInliner->setAccessible(true);
		$oGetStyleInliner->invokeArgs($this->messengerService,array());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetTransporterWithWrongTypeMedia(){
		$this->messengerService->setTransporter(new \BoilerAppMessenger\Mail\Transport\File(),new \stdClass());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testGetTransporterWithWrongTypeMedia(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oGetTransporter = $oReflectionClass->getMethod('getTransporter');
		$oGetTransporter->setAccessible(true);
		$oGetTransporter->invokeArgs($this->messengerService,array(new \stdClass()));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetTransporterWithUnknownMedia(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oGetTransporter = $oReflectionClass->getMethod('getTransporter');
		$oGetTransporter->setAccessible(true);
		$oGetTransporter->invokeArgs($this->messengerService,array('wrong'));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetRouterUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Service\MessengerService');
		$oRouter = $oReflectionClass->getProperty('router');
		$oRouter->setAccessible(true);
		$oRouter->setValue($this->messengerService, null);

		$oGetRouter = $oReflectionClass->getMethod('getRouter');
		$oGetRouter->setAccessible(true);
		$oGetRouter->invokeArgs($this->messengerService,array());
	}
}