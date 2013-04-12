<?php
namespace BoilerAppMessengerTest;
class MessageTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Message
	 */
	protected $message;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$this->message = new \BoilerAppMessenger\Message();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongFrom(){
		$this->message->setFrom('wrong');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddTo(){
		$this->message->addTo('wrong');
	}

	public function testToString(){
		$this->assertEquals('', $this->toString());
	}
}