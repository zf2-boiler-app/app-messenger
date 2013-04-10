<?php
namespace BoilerAppMessengerTest\Mail\Transport;
class FileTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{
	/**
	 * @var \BoilerAppMessenger\Mail\Transport\File
	 */
	protected $fileTransport;

	/**
	 * @var \BoilerAppMessenger\Mail\Message
	 */
	protected $message;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		parent::setUp();

		$sMailDir = getcwd().'/_files/mails';

		//Empty mails directory except .gitignore
		foreach(new \RecursiveIteratorIterator(
				new \RecursiveDirectoryIterator($sMailDir, \RecursiveDirectoryIterator::SKIP_DOTS),
				\RecursiveIteratorIterator::CHILD_FIRST
		) as $oFileinfo){
			if($oFileinfo->isDir())rmdir($oFileinfo->getRealPath());
			elseif($oFileinfo->getBasename() !== '.gitignore')unlink($oFileinfo->getRealPath());
		}

		//Create transporter
		$this->fileTransport = new \BoilerAppMessenger\Mail\Transport\File(
			new \Zend\Mail\Transport\FileOptions(array('path' => $sMailDir))
		);

		//Create message
		$this->message = new \BoilerAppMessenger\Mail\Message();
		$this->message->setEncoding('UTF-8')
		->setFrom('test-system@test.com','Test System')
		->addTo('test-from-user@test.com','Test "From" User')
		->setSubject('test subject')
		->setBody('test body')
		->addAttachment(getcwd().'/_files/attachments/attachment-test.txt');
	}

	public function testSend(){

		//Send message
		$this->fileTransport->send($this->message);

		//Test mail content
		$sMailDir = getcwd().'/_files/mails';
		$sMailContent = preg_replace(
			array('/(Date:[\S|\s]*)(From:)/','/Content-ID: <([a-f0-9]*)>/'),
			array('$2','content-id'),
			file_get_contents($sMailDir.DIRECTORY_SEPARATOR.current(array_diff(scandir($sMailDir), array('..', '.','.gitignore'))))
		);

		//Retreive boundary
		$this->assertEquals(1,preg_match('/boundary="=_([a-f0-9]*)"/', $sMailContent,$aMatches));
		$this->assertArrayHasKey(1, $aMatches);

		//Replace boundary by static word
		$sMailContent = str_ireplace($aMatches[1],'boundary',$sMailContent);

		$this->assertEquals(
			file_get_contents(getcwd().'/_files/expected/mails/test-send-message'),
			$sMailContent
		);
	}
}