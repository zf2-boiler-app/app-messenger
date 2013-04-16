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
		$this->message->setSubject('Test subject')->setBody('Test body');
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
	public function testAddWrongTo(){
		$this->message->addTo('wrong');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddWrongToArray(){
		$this->message->addTo(array('wrong'));
	}

	public function testToString(){
		$this->assertEquals('Test subject'.PHP_EOL.PHP_EOL.'Test body', $this->message->toString());
	}
}