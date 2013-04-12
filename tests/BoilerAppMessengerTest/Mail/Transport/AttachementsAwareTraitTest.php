<?php
namespace BoilerAppMessengerTest\Mail\Transport;
class AttachementsAwareTraitTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Mail\Transport\AttachementsAwareTrait
	 */
	protected $traitObject;


	protected function setUp(){
		$this->traitObject = $this->getObjectForTrait('\BoilerAppMessenger\Mail\Transport\AttachementsAwareTrait');
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testSetWrongBaseDir(){
		$this->traitObject->setBaseDir('wrong');
	}

	/**
	 * @expectedException LogicException
	 */
	public function testGetBaseDirUnset(){
		$this->traitObject->getBaseDir();
	}
}