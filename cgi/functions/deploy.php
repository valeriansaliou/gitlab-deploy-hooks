<?php

/*
 *	gitlab-deploy-hooks
 *	Deploy Functions (Web API)
 */

// Send deploy task to background
function backgroundDeploy($deploy_type, $project_path, $json_post) {
	// Parse incoming GitLab Hooks POST data
	if($deploy_type && $json_post && $json_post->ref) {
		exec('php ../cli/tools/_deploy.php '.escapeshellarg($deploy_type).' '.escapeshellarg($project_path).' '.escapeshellarg($json_post->ref).' > /dev/null 2>/dev/null &');
		return true;
	}

	return false;
}

?>
