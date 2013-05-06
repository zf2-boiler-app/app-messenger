<?php
namespace BoilerAppMessengerTest\Media\Mail;
class MailMessageTransporterTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Media\Mail\MailMessageTransporter
	 */
	protected $mailMessageTransporter;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oMailMessageTransporterFactory = new \BoilerAppMessenger\Factory\MailMessageTransporterFactory();

		//Initialize messenger service
		$this->mailMessageTransporter = $oMailMessageTransporterFactory->createService($this->getServiceManager());
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testSendMessageWithWrongFrom(){
		$oMessage = new \BoilerAppMessenger\Message\Message();
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Message\Message');
		$oFrom = $oReflectionClass->getProperty('from');
		$oFrom->setAccessible(true);
		$oFrom->setValue($oMessage, null);
		$this->mailMessageTransporter->sendMessage($oMessage);
	}

	/**
	 * @expectedException UnexpectedValueException
	 */
	public function testSendMessageWithWrongTo(){
		$oMessage = new \BoilerAppMessenger\Message\Message();
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Message\Message');
		$oTo = $oReflectionClass->getProperty('to');
		$oTo->setAccessible(true);
		$oTo->setValue($oMessage, array('wrong'));

		$oMessage->setFrom($this->getServiceManager()->get('MessengerService')->getSystemUser());
		$this->mailMessageTransporter->sendMessage($oMessage);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetMailTransporterUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Media\Mail\MailMessageTransporter');
		$oMailTransporter = $oReflectionClass->getProperty('mailTransporter');
		$oMailTransporter->setAccessible(true);
		$oMailTransporter->setValue($this->mailMessageTransporter, null);

		$this->mailMessageTransporter->getMailTransporter();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongBaseDir(){
		$this->mailMessageTransporter->setBaseDir('Wrong');
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetBaseDirUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Media\Mail\MailMessageTransporter');
		$oBaseDir = $oReflectionClass->getProperty('baseDir');
		$oBaseDir->setAccessible(true);
		$oBaseDir->setValue($this->mailMessageTransporter, null);

		$this->mailMessageTransporter->getBaseDir();
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetMessageRendererUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Media\Mail\MailMessageTransporter');
		$oMessageRenderer = $oReflectionClass->getProperty('messageRenderer');
		$oMessageRenderer->setAccessible(true);
		$oMessageRenderer->setValue($this->mailMessageTransporter, null);

		$this->mailMessageTransporter->getMessageRenderer();
	}
}