<?php

/*
 *	gitlab-deploy-hooks
 *	Instance Configuration (secured)
 */

// Instance configuration container
$CONFIG_INSTANCE = array(
	// Host
	'host' 			=> array(
		'name' 				=> 'HOST_NAME',
		'datacenter' 		=> 'DATACENTER_NAME',

		'email'		=> array(
			'address'		=> 'NAME@HOST.TLD',
			'name'			=> 'EMAIL_NAME'
		)
	),

	// Environment
	'environment' 	=> array(
		'deploy' 			=> array(
			'bin' 	=> '/usr/local/bin/deploy',

			'types' 	=> array(
				'sys' => array(
					'path' => '/',
					'sudo' => true
				),

				'web' => array(
					'path' => '/PATH/TO/WEB/DIR',
					'sudo' => false
				),

				'svc' => array(
					'path' => '/PATH/TO/SVC/DIR',
					'sudo' => true
				)
			)
		)
	),

	// Notifications
	'notifications' => array(
		'email'		=> array(
			'enabled'		=> false,
			'recipients' 	=> array()
		)
	),

	// Security
	'security' 		=> array(
		'key' 				=> 'PRIVATE_KEY',
		'token' 			=> 'PRIVATE_TOKEN'
	)
);

?>
