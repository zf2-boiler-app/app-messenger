<?php
return array(
	'assets' => array(
		'BoilerAppMessenger' => array(
			'mail' => array(
				'css' => array(__DIR__ . '/../assets/css/reset.css')
			)
		)
	),
	'rendererToStrategy' => array(
		'BoilerAppMessenger\Media\Mail\MailMessageRenderer'  => '\AssetsBundle\View\Strategy\ViewHelperStrategy'
	)
);