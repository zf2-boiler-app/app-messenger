<?php
namespace BoilerAppMessengerTest\StyleInliner\Processor;
class CssToInlineStylesProcessorTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{

	/**
	 * @var \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor
	 */
	protected $cssToInlineStylesProcessor;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$this->cssToInlineStylesProcessor = \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory(array('baseDir' => getcwd().DIRECTORY_SEPARATOR.'tests/_files'));
	}

	public function testSetEncoding(){
		$this->assertEquals($this->cssToInlineStylesProcessor,$this->cssToInlineStylesProcessor->setEncoding('UTF-8'));
		$this->assertEquals('UTF-8',$this->cssToInlineStylesProcessor->getEncoding());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongEncoding(){
		$this->cssToInlineStylesProcessor->setEncoding(null);
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetBaseDirUnset(){
		\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory()->getBaseDir();
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongBaseDir(){
		$this->cssToInlineStylesProcessor->setBaseDir('wrong');
	}

	public function testProcess(){
		$this->assertEquals(
			file_get_contents(getcwd().'/tests/_files/expected/styleInliner/csstoinlinestyles-simple-test.html'),
			$this->cssToInlineStylesProcessor->process(file_get_contents(getcwd().'/tests/_files/styleInliner/simple-test.html'))
		);
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testProcessWithoutString(){
		$this->cssToInlineStylesProcessor->process(array());
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetHtmlWithoutString(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor');

		$oSetHtml = $oReflectionClass->getMethod('setHtml');
		$oSetHtml->setAccessible(true);
		$oSetHtml->invokeArgs($this->cssToInlineStylesProcessor,array(null));
	}

	/**
	 * @expectedException LogicException
	 */
	public function testExtractCssWithDomDocumentUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor');

		$oExtractCss = $oReflectionClass->getMethod('extractCss');
		$oExtractCss->setAccessible(true);
		$oExtractCss->invokeArgs(\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory(),array());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testExtractGetHtmlDomDocumentUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor');

		$oGetHtml = $oReflectionClass->getMethod('getHtml');
		$oGetHtml->setAccessible(true);
		$oGetHtml->invokeArgs(\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory(),array());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testExtractGetCssDomDocumentUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor');

		$oGetCss = $oReflectionClass->getMethod('getCss');
		$oGetCss->setAccessible(true);
		$oGetCss->invokeArgs(\BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory(),array());
	}
}