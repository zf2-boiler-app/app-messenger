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
		$this->inlineStyleProcessor = \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor::factory(array('baseDir' => getcwd().DIRECTORY_SEPARATOR.'_files'));
	}

	public function testGetBaseDir(){
		$this->assertEquals(getcwd().DIRECTORY_SEPARATOR.'_files', $this->inlineStyleProcessor->getBaseDir());
	}

	public function testProcess(){
		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/styleInliner/simple-test.html'),
			$this->inlineStyleProcessor->process(file_get_contents(getcwd().'/_files/styleInliner/simple-test.html'))
		);
	}
}