<?php
namespace BoilerAppMessengerTest\Message;
class MessageTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Message\Message
	 */
	protected $message;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$this->message = new \BoilerAppMessenger\Message\Message();
		$oBody = new \Zend\View\Model\ViewModel(array('testValue' => 'Test body'));
		$this->message->setSubject('Test subject')->setBody($oBody->setTemplate('email/simple-view'));
	}

	public function testAddAttachment(){
		$this->assertEquals($this->message,$this->message->addAttachment(getcwd().'/tests/_files/attachments/attachment-test.txt'));
		$this->assertEquals(array(realpath(getcwd().'/tests/_files/attachments/attachment-test.txt')),$this->message->getAttachments());
	}

	public function testHasAttachment(){
		$this->assertEquals($this->message,$this->message->addAttachment(getcwd().'/tests/_files/attachments/attachment-test.txt'));
		$this->assertTrue($this->message->hasAttachments());
		$this->assertEquals($this->message,$this->message->removeAttachment());
		$this->assertFalse($this->message->hasAttachments());
	}

	public function testRemoveAttachment(){
		$this->assertEquals($this->message,$this->message->addAttachment(getcwd().'/tests/_files/attachments/attachment-test.txt'));
		$this->assertEquals($this->message,$this->message->removeAttachment(getcwd().'/tests/_files/attachments/attachment-test.txt'));
		$this->assertEquals(array(),$this->message->getAttachments());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddWrongTo(){
		$this->message->addTo('wrong');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddWrongToArray(){
		$this->message->addTo(array('wrong'));
	}
}