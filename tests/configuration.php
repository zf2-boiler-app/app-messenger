<?php
return array(
	'asset_bundle' => array(
		'cachePath' => __DIR__.'/_files/cache',
		'cacheUrl' => '@zfBaseUrl/cache/',
		'assetsPath' => null
	),
	'translator' => array(
		'locale' => 'fr_FR'
	),
	'messenger' => array(
		'system_user' => array(
			'display_name' => 'Test System',
			'email' => 'test-system@test.com'
		),
		'transporters' => array(
			\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA => function($oServiceLocator){
				$oMailMessageTransporter = new \BoilerAppMessenger\Media\Mail\MailMessageTransporter();
				return $oMailMessageTransporter
					->setMessageRenderer($oServiceLocator->get('MailMessageRenderer'))
					->setBaseDir(__DIR__)
					->setMailTransporter(new \Zend\Mail\Transport\File(new \Zend\Mail\Transport\FileOptions(array(
						'path' => __DIR__ . '/_files/mails'
					))));
			},
			'test' => 'TestTransporter',
			'test1' => array(
				'type' => 'TestTransporter'
			)
		)
	),
	'medias' => array(
		\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA => array(
			'mail_transporter' => function(){
				return new \Zend\Mail\Transport\File(new \Zend\Mail\Transport\FileOptions(array(
					'path' => __DIR__ . '/_files/mails'
				)));
			},
			'template_map' => array(
				'email/simple-view' => __DIR__ . '/_files/views/simple-view.phtml'
			)
		)
	),
	'service_manager' => array(
		'factories' => array(
			'InlineStyleProcessor' => function(){
				return \BoilerAppMessenger\StyleInliner\Processor\InlineStyleProcessor::factory(array('baseDir' => __DIR__.DIRECTORY_SEPARATOR.'_files'));
			},
			'CssToInlineStylesProcessor' => function(){
				return \BoilerAppMessenger\StyleInliner\Processor\CssToInlineStylesProcessor::factory(array('baseDir' => __DIR__.DIRECTORY_SEPARATOR.'_files'));
			},
			'TestTransporter' => function(){
				return new \BoilerAppMessenger\Media\Mail\MailMessageTransporter();
			},
			'TestMailTransporter' => function(){
				return new \Zend\Mail\Transport\Sendmail();
			}
		)
	)
);