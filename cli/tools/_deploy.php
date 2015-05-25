<?php

/*
 *	gitlab-deploy-hooks
 *	Deploy Tool (command-line)
 */

// Change current working dir
chdir(dirname(__FILE__));

// Read configuration
require_once('../../config/common.php');
require_once('../../config/instance.php');

// Include functions
require_once('../functions/common.php');
require_once('../functions/_deploy.php');

// Don't allow non-CLI requests
if(caller() != 'cli') {
	exit('Command-line service. Please call me from the shell.');
}

try {
	// Get the script arguments
	$deploy_type = isset($argv[1]) ? $argv[1] : null;
	$project_name = isset($argv[2]) ? $argv[2] : null;
	$updated_branch = isset($argv[3]) ? $argv[3] : null;

	if($deploy_type && $project_name && $updated_branch) {
		if(isset($CONFIG_INSTANCE['environment']['deploy']['types'][$deploy_type])) {
			// Full path to project
			$project_path = $CONFIG_INSTANCE['environment']['deploy']['types'][$deploy_type]['path'].'/'.$project_name;

			// Do this branch needs to be deployed?
			$needs_deploy = needsDeploy($project_path, $updated_branch);

			if(!$needs_deploy['needed']) {
				print('[hooks:deploy:success] Branch not affected.'."\n");
			} else {
				print('[hooks:deploy] Branch affected, deploying...'."\n");

				// Execute the deploy command (redirect STDERR to STDOUT)
				$output_arr = array();
				$status_code = 0;

				$sudo_start = '';

				if($CONFIG_INSTANCE['environment']['deploy']['types'][$deploy_type]['sudo'] == true) {
					$sudo_start = 'sudo ';
				}

				exec($sudo_start.$CONFIG_INSTANCE['environment']['deploy']['bin'].' '.escapeshellarg($deploy_type).' '.escapeshellarg($project_name).' 2>&1', $output_arr, $status_code);

				// Get command output status
				$output_status = statusDeploy($output_arr, $status_code);

				switch($output_status) {
					case 'Success':
						print('[hooks:deploy:success] Update deployed.'."\n");
						break;

					case 'Fail':
						print('[hooks:deploy:fail] Update could not be deployed.'."\n");
						break;

					default:
						print('[hooks:deploy:none] Already up-to-date.'."\n");
				}

				// Notify the server admins (email)
				print('[hooks:deploy:mailer] Sending notification email(s)...'."\n");

				if(notifyDeploy($deploy_type, $project_path, $needs_deploy['branch'], $output_status, $output_arr)) {
					print('[hooks:deploy:mailer:success] Notification email(s) sent.'."\n");
				} else {
					print('[hooks:deploy:mailer:warn] Could not send one or all notification email(s).'."\n");
				}
			}
		} else {
			print('[hooks:deploy:error] No path configured for this project type. Aborted.'."\n");
		}
	} else {
		print('[hooks:deploy:error] Not enough arguments. Aborted.'."\n");
	}
} catch(exception $e) {
	print('[hooks:deploy:fatal] Script error: '.$e->getMessage()."\n");
}

print('[hooks:deploy] Done.'."\n");
print('[hooks:deploy] It was a pleasure ;)'."\n");

exit;

?>
