<?php
return array(
	'asset_bundle' => include 'module.config.assets.php',
	'messenger' => array(
		'system_user' => array(
			'email' => '',
			'name' => ''
		),
		'view_manager' => array(
			'doctype' => 'HTML5',
			'template_map' => array(
				'email/layout' => __DIR__ . '/../view/email/layout.phtml'
			)
		),
		'transporters' => array(
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL => 'EmailTransporter'
		),
		'templating' => array(
			'template_map' => array(
				'default' => array(
					'template' => 'email/layout'
				)
			)
		)
	),
	'style_inliner' => array(
		'processor' => 'InlineStyleProcessor'
	),
	'service_manager' => array(
		'factories' => array(
			'MessengerService' => 'BoilerAppMessenger\Factory\MessengerServiceFactory',
			'EmailTransporter' => 'BoilerAppMessenger\Factory\EmailTransporterFactory',
			'StyleInliner' => 'BoilerAppMessenger\Factory\StyleInlinerFactory',
			'InlineStyleProcessor' => 'BoilerAppMessenger\Factory\InlineStyleProcessorFactory'
		)
	)
);