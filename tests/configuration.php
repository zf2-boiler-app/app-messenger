<?php
return array(
	'asset_bundle' => array(
		'cachePath' => __DIR__.'/_files/cache',
		'assetsPath' => __DIR__
	),
	'translator' => array(
		'locale' => 'fr_FR'
	),
	'messenger' => array(
		'system_user' => array(
			'email' => 'test@test.com',
			'name' => 'test'
		),
		'view_manager' => array(
			'doctype' => 'HTML5',
			'template_map' => array(
				'email/simple-view' => __DIR__ . '/_files/views/simple-view.phtml'
			)
		)
	)
);