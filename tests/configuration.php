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
			'email' => 'test-system@test.com',
			'name' => 'Test System'
		),
		'view_manager' => array(
			'template_map' => array(
				'email/simple-view' => __DIR__ . '/_files/views/simple-view.phtml'
			)
		),
		'transporters' => array(
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL => function(){
				$oFileTransport = new \BoilerAppMessenger\Mail\Transport\File(new \Zend\Mail\Transport\FileOptions(array(
					'path' => __DIR__ . '/_files/mails'
				)));
				return $oFileTransport->setBaseDir(__DIR__);
			},
			'test' => 'TestTransporter',
			'test1' => array(
				'type' => 'TestTransporter'
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
				return new \BoilerAppMessenger\Mail\Transport\File(new \Zend\Mail\Transport\FileOptions(array(
					'path' => __DIR__ . '/_files/mails'
				)));
			}
		)
	)
);