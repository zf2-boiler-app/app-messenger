<?php
namespace BoilerAppMessengerTest\StyleInliner;
class StyleInlinerServiceTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\StyleInliner\StyleInlinerService
	 */
	protected $styleInlinerService;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oStyleInlinerFactory = new \BoilerAppMessenger\Factory\StyleInlinerFactory();
		$this->styleInlinerService = $oStyleInlinerFactory->createService($this->getServiceManager());
	}

	public function testGetOptions(){
		$this->assertInstanceOf('BoilerAppMessenger\StyleInliner\StyleInlinerOptions',$this->styleInlinerService->getOptions());
	}

	public function testProcessHtml(){
		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/styleInliner/simple-test.html'),
			$this->styleInlinerService->processHtml(file_get_contents(getcwd().'/_files/styleInliner/simple-test.html'))
		);
	}
}