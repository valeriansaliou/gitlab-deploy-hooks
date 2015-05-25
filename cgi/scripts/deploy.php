<?php

/*
 *	gitlab-deploy-hooks
 *	Deploy Script (Web API)
 */

// Include functions
require_once('../cgi/functions/deploy.php');

// Current context
$CONTEXT_RESPONSE['status'] 	= 'error';
$CONTEXT_RESPONSE['type'] 		= 'deploy';
$CONTEXT_RESPONSE['message'] 	= 'Deploy Error';

// Sanitize context vars
if(!pathSafe($CONTEXT_GET_TYPE))  $CONTEXT_GET_TYPE = null;
if(!pathSafe($CONTEXT_GET_DATA))  $CONTEXT_GET_DATA = null;

try {
	// Process deploy request
	if(!$CONTEXT_GET_TYPE) {
		$CONTEXT_RESPONSE['message'] = 'No Deploy Type';
	} else if(!$CONTEXT_GET_DATA) {
		$CONTEXT_RESPONSE['message'] = 'No Path Data';
	} else {
		$raw_post_data = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : null;

		// Validate POST data
		if(!$raw_post_data) {
			$CONTEXT_RESPONSE['message'] = 'No POST data';
		} else {
			// Send next tasks to background
			if(backgroundDeploy($CONTEXT_GET_TYPE, $CONTEXT_GET_DATA, json_decode($raw_post_data))) {
				$CONTEXT_RESPONSE['status']  = 'success';
				$CONTEXT_RESPONSE['message'] = 'Deploy Started';
			} else {
				$CONTEXT_RESPONSE['message'] = 'Could Not Start Deploy';
			}
		}
	}
} catch(exception $e) {
	$CONTEXT_RESPONSE['message'] = 'Server Error: '.$e->getMessage();
}

?>
