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

	/**
	 * @expectedException LogicException
	 */
	public function testGetOptionsUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\StyleInliner\StyleInlinerService');
		$oOptions = $oReflectionClass->getProperty('options');
		$oOptions->setAccessible(true);
		$oOptions->setValue($this->styleInlinerService, null);

		$oGetOptions = $oReflectionClass->getMethod('getOptions');
		$oGetOptions->setAccessible(true);
		$oGetOptions->invokeArgs($this->styleInlinerService,array());
	}

	public function testProcessHtml(){
		$this->assertEquals(
			file_get_contents(getcwd().'/tests/_files/expected/styleInliner/csstoinlinestyles-simple-test.html'),
			$this->styleInlinerService->processHtml(file_get_contents(getcwd().'/tests/_files/styleInliner/simple-test.html'))
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProcessWithoutString(){
		$this->styleInlinerService->processHtml(array());
	}
}