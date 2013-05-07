<?php
return array(
	'assets' => array(
		'BoilerAppMessenger' => array(
			\BoilerAppMessenger\Media\Mail\MailMessageRenderer::MEDIA => array(
				'css' => array(__DIR__ . '/../assets/css/reset.css')
			)
		)
	),
	'rendererToStrategy' => array(
		'BoilerAppMessenger\Media\Mail\MailMessageRenderer'  => '\AssetsBundle\View\Strategy\ViewHelperStrategy'
	)
);