<?php
namespace BoilerAppMessengerTest\StyleInliner\Processor;
class InlineStyleProcessorTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{

	/**
	 * @var \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor
	 */
	protected $inlineStyleProcessor;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$this->inlineStyleProcessor = \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor::factory(array('baseDir' => getcwd().DIRECTORY_SEPARATOR.'tests/_files'));
	}

	public function testGetBaseDir(){
		$this->assertEquals(getcwd().DIRECTORY_SEPARATOR.'tests\\_files', $this->inlineStyleProcessor->getBaseDir());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetBaseDirUnset(){
		\BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor::factory()->getBaseDir();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongBaseDir(){
		$this->inlineStyleProcessor->setBaseDir('wrong');
	}

	public function testProcess(){
		$this->assertEquals(
			file_get_contents(getcwd().'/tests/_files/expected/styleInliner/inlinestyle-simple-test.html'),
			$this->inlineStyleProcessor->process(file_get_contents(getcwd().'/tests/_files/styleInliner/simple-test.html'))
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProcessWithoutString(){
		$this->inlineStyleProcessor->process(array());
	}
}