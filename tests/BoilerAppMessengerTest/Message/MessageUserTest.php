<?php
namespace BoilerAppMessengerTest\Message;
class MessageUserTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Message\MessageUser
	 */
	protected $messageUser;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		//Initialize message user
		$this->messageUser = new \BoilerAppMessenger\Message\MessageUser();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongUserDisplayName(){
		$this->messageUser->setUserDisplayName(null);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetUserDisplayNameUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Message\MessageUser');
		$oUserDisplayName = $oReflectionClass->getProperty('userDisplayName');
		$oUserDisplayName->setAccessible(true);
		$oUserDisplayName->setValue($this->messageUser, null);

		$this->messageUser->getUserDisplayName();
	}


	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongUserEmail(){
		$this->messageUser->setUserEmail('wrong');
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetUserEmailUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Message\MessageUser');
		$oUserEmail = $oReflectionClass->getProperty('userEmail');
		$oUserEmail->setAccessible(true);
		$oUserEmail->setValue($this->messageUser, null);

		$this->messageUser->getUserEmail();
	}
}