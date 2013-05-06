<?php
namespace BoilerAppMessengerTest;
class MessengerOptionsTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\MessengerOptions
	 */
	protected $messengerOptions;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$aConfiguration = $this->getServiceManager()->get('Config');
		$aConfiguration = $aConfiguration['messenger'];
		unset($aConfiguration['transporters'],$aConfiguration['tree_layout_stack']);
		$oMessageUser = new \BoilerAppMessenger\Message\MessageUser();
		$aConfiguration['system_user'] = $oMessageUser
			->setUserDisplayName($aConfiguration['system_user']['display_name'])
			->setUserEmail($aConfiguration['system_user']['email']);
		$this->messengerOptions = new \BoilerAppMessenger\MessengerOptions($aConfiguration);
	}

	public function testGetSystemUser(){
		$this->assertInstanceOf('BoilerAppMessenger\Message\MessageUser', $oSystemUser = $this->messengerOptions->getSystemUser());

		$this->assertEquals('Test System',$oSystemUser->getUserDisplayName());
		$this->assertEquals('test-system@test.com',$oSystemUser->getUserEmail());
	}


	/**
	 * @expectedException LogicException
	 */
	public function testGetSystemUserUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\MessengerOptions');
		$oSystemUser = $oReflectionClass->getProperty('systemUser');
		$oSystemUser->setAccessible(true);
		$oSystemUser->setValue($this->messengerOptions, null);

		$this->messengerOptions->getSystemUser();
	}
}