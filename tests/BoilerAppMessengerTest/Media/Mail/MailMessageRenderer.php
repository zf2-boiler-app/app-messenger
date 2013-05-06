<?php
namespace BoilerAppMessengerTest\Media\Mail;
class MailMessageRendererTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Media\Mail\MailMessageRenderer
	 */
	protected $mailMessageRenderer;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();
		$oMailMessageRendererFactory = new \BoilerAppMessenger\Factory\MailMessageRendererFactory();

		//Initialize messenger service
		$this->mailMessageRenderer = $oMailMessageRendererFactory->createService($this->getServiceManager());
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetTemplatingServiceUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Media\Mail\MailMessageRenderer');
		$oTemplatingService = $oReflectionClass->getProperty('templatingService');
		$oTemplatingService->setAccessible(true);
		$oTemplatingService->setValue($this->mailMessageRenderer, null);

		$this->mailMessageRenderer->getTemplatingService();
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetAssetsBundleServiceUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Media\Mail\MailMessageRenderer');
		$oAssetsBundleService = $oReflectionClass->getProperty('assetsBundleService');
		$oAssetsBundleService->setAccessible(true);
		$oAssetsBundleService->setValue($this->mailMessageRenderer, null);

		$this->mailMessageRenderer->getAssetsBundleService();
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetStyleInlinerServiceUnset(){
		$oReflectionClass = new \ReflectionClass('\BoilerAppMessenger\Media\Mail\MailMessageRenderer');
		$oStyleInlinerService = $oReflectionClass->getProperty('styleInlinerService');
		$oStyleInlinerService->setAccessible(true);
		$oStyleInlinerService->setValue($this->mailMessageRenderer, null);

		$this->mailMessageRenderer->getStyleInlinerService();
	}
}