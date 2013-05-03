<?php
return array(
	'asset_bundle' => include 'module.config.assets.php',
	'messenger' => array(
		'view_manager' => array(
			'template_map' => array(
				'email/layout' => __DIR__ . '/../view/email/layout.phtml'
			)
		),
		'transporters' => array(
			\BoilerAppMessenger\Service\MessengerService::MEDIA_EMAIL => 'SendmailTransport'
		),
		'tree_layout_stack' => array(
			'layout_tree' => array(
				'default' => array(
					'template' => 'email/layout'
				)
			)
		)
	),
	'style_inliner' => array(
		'processor' => 'CssToInlineStylesProcessor'
	),
	'service_manager' => array(
		'factories' => array(
			'MessengerService' => 'BoilerAppMessenger\Factory\MessengerServiceFactory',
			'SendmailTransport' => 'BoilerAppMessenger\Factory\Transport\SendmailFactory',
			'StyleInliner' => 'BoilerAppMessenger\Factory\StyleInlinerFactory',
			'InlineStyleProcessor' => 'BoilerAppMessenger\Factory\InlineStyleProcessorFactory',
			'CssToInlineStylesProcessor' => 'BoilerAppMessenger\Factory\CssToInlineStylesProcessorFactory'
		)
	)
);