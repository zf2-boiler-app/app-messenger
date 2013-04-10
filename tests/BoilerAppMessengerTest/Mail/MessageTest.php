<?php
namespace BoilerAppMessengerTest\Mail;
class Message extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Mail\Message
	 */
	protected $message;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$this->message = new \BoilerAppMessenger\Mail\Message();
		$this->message->addAttachment(getcwd().'/_files/attachments/attachment-test.txt');
	}

	public function testGetAttachments(){
		$this->assertEquals(array(getcwd().'/_files/attachments/attachment-test.txt'), $this->message->getAttachments());
	}

	public function testHasAttachments(){
		$this->assertTrue($this->message->hasAttachments());
		$this->assertInstanceOf('BoilerAppMessenger\Mail\Message', $this->message->removeAttachment(getcwd().'/_files/attachments/attachment-test.txt'));
		$this->assertFalse($this->message->hasAttachments());
	}
}

