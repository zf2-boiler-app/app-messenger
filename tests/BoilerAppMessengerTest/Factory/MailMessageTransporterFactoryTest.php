<?php
namespace BoilerAppMessengerTest\Factory;
class MailMessageTransporterFactoryTest extends \BoilerAppTest\PHPUnit\TestCase\AbstractTestCase{


	/**
	 * @var \BoilerAppMessenger\Factory\MailMessageTransporterFactory
	 */
	protected $mailMessageTransporterFactory;

	/**
	 * @var array
	 */
	protected $originalConfig;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp(){
		$this->originalConfig = $this->getServiceManager()->setAllowOverride(true)->get('Config');
		$this->mailMessageTransporterFactory = new \BoilerAppMessenger\Factory\MailMessageTransporterFactory();
	}

	public function testCreateService(){
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter', $this->mailMessageTransporterFactory->createService($this->getServiceManager()));
    }

    public function testCreateServiceWithClassnameMailTransporter(){
    	$aConfiguration = $this->originalConfig;
    	$aConfiguration['medias']['mail']['mail_transporter'] = 'Zend\Mail\Transport\Sendmail';

    	$oMailMessageTransporter = $this->mailMessageTransporterFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter',$oMailMessageTransporter);
    	$this->assertInstanceOf('Zend\Mail\Transport\Sendmail',$oMailMessageTransporter->getMailTransporter());
    }

    public function testCreateServiceWithServiceNameMailTransporter(){
    	$aConfiguration = $this->originalConfig;
    	$aConfiguration['medias']['mail']['mail_transporter'] = 'TestMailTransporter';

    	$oMailMessageTransporter = $this->mailMessageTransporterFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter',$oMailMessageTransporter);
    	$this->assertInstanceOf('Zend\Mail\Transport\Sendmail',$oMailMessageTransporter->getMailTransporter());
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateServiceWithWrongNameMailTransporter(){
    	$aConfiguration = $this->originalConfig;
    	$aConfiguration['medias']['mail']['mail_transporter'] = 'Wrong';

    	$oMailMessageTransporter = $this->mailMessageTransporterFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter',$oMailMessageTransporter);
    	$this->assertInstanceOf('Zend\Mail\Transport\Sendmail',$oMailMessageTransporter->getMailTransporter());
    }

    public function testCreateServiceWithArrayMailTransporter(){
    	$aConfiguration = $this->originalConfig;
    	$aConfiguration['medias']['mail']['mail_transporter'] = array('type' => 'Zend\Mail\Transport\Sendmail');

    	$oMailMessageTransporter = $this->mailMessageTransporterFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter',$oMailMessageTransporter);
    	$this->assertInstanceOf('Zend\Mail\Transport\Sendmail',$oMailMessageTransporter->getMailTransporter());

    	$aConfiguration['medias']['mail']['mail_transporter'] = array('type' => 'TestMailTransporter');

    	$oMailMessageTransporter = $this->mailMessageTransporterFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter',$oMailMessageTransporter);
    	$this->assertInstanceOf('Zend\Mail\Transport\Sendmail',$oMailMessageTransporter->getMailTransporter());
    }

    /**
     * @expectedException LogicException
     */
    public function testCreateServiceWithWrongArrayMailTransporter(){
    	$aConfiguration = $this->originalConfig;
    	$aConfiguration['medias']['mail']['mail_transporter'] = array('type' => 'Wrong');

    	$oMailMessageTransporter = $this->mailMessageTransporterFactory->createService($this->getServiceManager()->setService('Config', $aConfiguration));
    	$this->assertInstanceOf('BoilerAppMessenger\Media\Mail\MailMessageTransporter',$oMailMessageTransporter);
    	$this->assertInstanceOf('Zend\Mail\Transport\Sendmail',$oMailMessageTransporter->getMailTransporter());
    }

    public function tearDown(){
    	//Reset configuration
    	$this->getServiceManager()->setService('Config', $this->originalConfig)->setAllowOverride(false);
    }
}