<?php
namespace BoilerAppMessengerTest\Service;
class MessengerOptionsTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Service\MessengerOptions
	 */
	protected $messengerOptions;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$aConfiguration = $this->getServiceManager()->get('Config');
		unset($aConfiguration['messenger']['transporters'],$aConfiguration['messenger']['tree_layout_stack']);
		$this->messengerOptions = new \BoilerAppMessenger\Service\MessengerOptions(isset($aConfiguration['messenger'])?$aConfiguration['messenger']:array());
	}

	public function testGetSystemUserEmail(){
		$this->assertEquals('test-system@test.com',$this->messengerOptions->getSystemUserEmail());
	}

	public function testGetSystemUserName(){
		$this->assertEquals('Test System',$this->messengerOptions->getSystemUserName());
	}

	public function testGetTemplateMap(){
		$this->assertArrayHasKey('email/simple-view', $this->messengerOptions->getTemplateMap());
	}

	public function testHasTemplateMap(){
		$this->assertTrue($this->messengerOptions->hasTemplateMap());
	}
}