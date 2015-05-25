<?php

/*
 *	gitlab-deploy-hooks
 *	Deploy Functions (command-line)
 */

// Checks if a deploy is needed (current branch updated)
function needsDeploy($project_path, $updated_branch) {
	$response_arr = array(
		'needed' => false,
		'branch' => 'none'
	);

	// Parse provided GitLab Hooks data
	if($project_path && $updated_branch) {
		$updated_branch = preg_replace('/^refs\/heads\//', '', $updated_branch);
		$current_branch = null;

		if($updated_branch) {
			$git_branches = array();

			exec('cd '.$project_path.'; git branch', $git_branches);

			foreach($git_branches as $cur_line) {
				$cur_line    = trim($cur_line);
				$cur_matches = array();

				if(preg_match('/^\* (\S+)/', $cur_line, $cur_matches)) {
					$current_branch = $cur_matches[1]; break;
				}
			}

			$updated_branch = trim($updated_branch);
			$current_branch = trim($current_branch);

			if($updated_branch == $current_branch) {
				$response_arr['needed'] = true;
				$response_arr['branch'] = $updated_branch;
			}
		}
	}

	return $response_arr;
}

// Reads the deploy status
function statusDeploy($command_output, $status_code) {
	$deploy_status = null;

	// Matches array
	$matches = array(
		'None' => array(
			'Already up-to-date.'
		),

		'Fail' => array(
			'error: git ',
			'fatal: The remote end hung up unexpectedly'
		)
	);

	// Find for a match
	foreach($command_output as $cur_line) {
		// Loop on matches
		foreach($matches as $cur_status => $cur_match) {
			if(matchExists($cur_line, $cur_match) === true) {
				$deploy_status = $cur_status; break;
			}
		}

		// All done?
		if($deploy_status) {
			break;
		}
	}

	// Filter deploy status
	if($status_code > 0) {
		$deploy_status = 'Fail';
	}

	if(!$deploy_status) {
		$deploy_status = 'Success';
	}

	return $deploy_status;
}

// Sends a deploy notification email
function notifyDeploy($deploy_type, $deploy_path, $deploy_branch, $deploy_status, $command_output) {
	global $CONFIG_INSTANCE;

	$mailer_success = true;

	// Notifications enabled?
	if($CONFIG_INSTANCE['notifications']['email']['enabled']) {
		// Build email contents
		$email_subject = $CONFIG_INSTANCE['host']['name'].' - Hook > Deploy > '.$deploy_status;

		$email_body    = 'Deploy hook executed on '.date('d-m-Y H:i:s').' on '.$CONFIG_INSTANCE['host']['name'].' server hosted at '.$CONFIG_INSTANCE['host']['datacenter'].'.';
		$email_body   .= "\n\n";

		$email_body   .= 'Status: '.$deploy_status."\n";
		$email_body   .= 'Type: '.$deploy_type."\n";
		$email_body   .= 'Path: '.$deploy_path."\n";
		$email_body   .= 'Branch: '.$deploy_branch."\n";
		$email_body   .= "\n\n";

		$email_body   .= "--\n";

		foreach($command_output as $cur_line) {
			$email_body .= "\n".$cur_line;
		}

		$email_headers = "From: ".$CONFIG_INSTANCE['host']['email']['name']." <".$CONFIG_INSTANCE['host']['email']['address'].">\nReply-to: ".$CONFIG_INSTANCE['host']['email']['address']."\n";

		// Send e-mail
		foreach($CONFIG_INSTANCE['notifications']['email']['recipients'] as $cur_email) {
			if(!mail($cur_email, utf8_decode($email_subject), utf8_decode($email_body), utf8_decode($email_headers))) {
				$mailer_success = false;
			}
		}
	}

	return $mailer_success;
}

?>
