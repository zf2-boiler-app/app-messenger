<?php
namespace BoilerAppMessengerTest\Factory;
class MessengerServiceFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{

	/**
	 * @var \BoilerAppMessenger\Factory\MessengerServiceFactory
	 */
	protected $messengerServiceFactory;

	/**
	 * @var array
	 */
	protected $originalConfig;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->originalConfig = $this->getServiceManager()->setAllowOverride(true)->get('Config');
		$this->messengerServiceFactory = new \BoilerAppMessenger\Factory\MessengerServiceFactory();
	}

	public function testCreateService(){
		$this->assertInstanceOf('BoilerAppMessenger\MessengerService',$this->messengerServiceFactory->createService($this->getServiceManager()));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testCreationTransporterWithUnknownService(){
		$aConfiguration = $this->originalConfig;

		//Override transporters config
		$aConfiguration['messenger']['transporters'] = array('test' => 'WrongTransporter');
		$this->assertInstanceOf('BoilerAppMessenger\MessengerService',$this->messengerServiceFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration)));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testCreationTransporterWithWrongArray(){
		$aConfiguration = $this->originalConfig;
		//Override transporters config
		$aConfiguration['messenger']['transporters'] = array('test' => array('type' => 'WrongTransporter'));
		$this->messengerServiceFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testCreationTransporterWithWrongSystemUserDisplayName(){
		$aConfiguration = $this->originalConfig;

		//Override system user config
		$aConfiguration['messenger']['system_user']['display_name'] = array();
		$this->messengerServiceFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testCreationTransporterWithWrongSystemUserEmail(){
		$aConfiguration = $this->originalConfig;

		//Override system user config
		$aConfiguration['messenger']['system_user']['email'] = 'wrong';
		$this->messengerServiceFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
	}

	public function tearDown(){
		//Reset configuration
    	$this->getServiceManager()->setService('Config', $this->originalConfig)->setAllowOverride(false);
	}
}