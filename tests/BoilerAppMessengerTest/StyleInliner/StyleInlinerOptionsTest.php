<?php
namespace BoilerAppMessengerTest\StyleInliner;
class StyleInlinerOptionsTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\StyleInliner\StyleInlinerOptions
	 */
	protected $styleInlinerOptions;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oOptions = new \BoilerAppMessenger\StyleInliner\StyleInlinerOptions();
    	$aConfiguration = $this->getServiceManager()->get('Config');
    	if(isset($aConfiguration['style_inliner']['processor'])){
    		$oProcessor = $aConfiguration['style_inliner']['processor'];
    		if($oProcessor instanceof \BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface)$oOptions->setProcessor($oProcessor);
    		elseif(is_string($oProcessor) && $this->getServiceManager()->has($oProcessor))$oOptions->setProcessor($this->getServiceManager()->get($oProcessor));
    	}
		$this->styleInlinerOptions = $oOptions;
	}

	public function testGetProcessor(){
		$this->assertInstanceOf('BoilerAppMessenger\StyleInliner\Processor\StyleInlinerProcessorInterface', $this->styleInlinerOptions->getProcessor());
	}
}