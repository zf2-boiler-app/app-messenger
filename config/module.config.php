<?php
return array(
	'asset_bundle' => include 'module.config.assets.php',
	'messenger' => array(
		'transporters' => array(
			\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA => 'MailMessageTransporter'
		)
	),
	'medias' => array(
		\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA => array(
			'mail_transporter' => 'Zend\Mail\Transport\Sendmail',
			'template_map' => array(
				'email/layout' => __DIR__ . '/../view/email/layout.phtml'
			),
			'tree_layout_stack' => array(
				'layout_tree' => array(
					'default' => array(
						'template' => 'email/layout'
					)
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
			'MailMessageTransporter' => 'BoilerAppMessenger\Factory\MailMessageTransporterFactory',
			'MailMessageRenderer' => 'BoilerAppMessenger\Factory\MailMessageRendererFactory',
			'StyleInlinerService' => 'BoilerAppMessenger\Factory\StyleInlinerFactory',
			'InlineStyleProcessor' => 'BoilerAppMessenger\Factory\InlineStyleProcessorFactory',
			'CssToInlineStylesProcessor' => 'BoilerAppMessenger\Factory\CssToInlineStylesProcessorFactory'
		)
	)
);