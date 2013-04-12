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
	protected $transportersConfig;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->messengerServiceFactory = new \BoilerAppMessenger\Factory\MessengerServiceFactory();
	}

	public function testCreateService(){
		$this->assertInstanceOf('BoilerAppMessenger\Service\MessengerService',$this->messengerServiceFactory->createService($this->getServiceManager()));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testCreationTransporterWithUnknownService(){
		$oServiceManager = $this->getServiceManager();

		$aConfiguration = $oServiceManager->get('Config');
		$this->transportersConfig = isset($aConfiguration['messenger']['transporters'])?$aConfiguration['messenger']['transporters']:null;

		//Override transporters config
		$aConfiguration['messenger']['transporters'] = array(
			'test' => 'WrongTransporter'
		);
		$oServiceManager->setAllowOverride(true);
		$oServiceManager->setService('Config', $aConfiguration);

		$this->messengerServiceFactory->createService($oServiceManager);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testCreationTransporterWithWrongArray(){
		$oServiceManager = $this->getServiceManager();

		$aConfiguration = $oServiceManager->get('Config');
		$this->transportersConfig = isset($aConfiguration['messenger']['transporters'])?$aConfiguration['messenger']['transporters']:null;

		//Override transporters config
		$aConfiguration['messenger']['transporters'] = array(
			'test' => array(
				'type' => 'WrongTransporter'
			)
		);
		$oServiceManager->setAllowOverride(true);
		$oServiceManager->setService('Config', $aConfiguration);

		$this->messengerServiceFactory->createService($oServiceManager);
	}

	public function tearDown(){
		//Reset configuration if needed
		if(isset($this->transportersConfig)){
			$oServiceManager = $this->getServiceManager();
			$aConfiguration = $oServiceManager->get('Config');
			$aConfiguration['messenger']['transporters'] = $this->transportersConfig;
			$oServiceManager->setService('Config', $aConfiguration);
			$oServiceManager->setAllowOverride(false);
		}
	}
}