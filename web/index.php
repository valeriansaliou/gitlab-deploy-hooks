<?php

/*
 *  gitlab-deploy-hooks
 *  Root Manager
 */

// Read configuration
require_once('../config/common.php');
require_once('../config/instance.php');

// Include functions
require_once('../cgi/functions/common.php');

// Prevents user from aborting script
ignore_user_abort(true);

// Current context
$CONTEXT_VERSION      = $CONFIG_COMMON['version'];
$CONTEXT_SECURITY_KEY = $CONFIG_INSTANCE['security']['key'];
$CONTEXT_TOKEN_KEY    = $CONFIG_INSTANCE['security']['token'];

$CONTEXT_GET_KEY    = isset($_GET['key'])     ? trim($_GET['key'])    : null;
$CONTEXT_GET_TOKEN  = isset($_GET['token'])   ? trim($_GET['token'])    : null;
$CONTEXT_GET_SCRIPT = isset($_GET['script'])  ? trim($_GET['script'])   : null;
$CONTEXT_GET_TYPE   = isset($_GET['type'])    ? trim($_GET['type'])     : null;
$CONTEXT_GET_DATA   = isset($_GET['data'])    ? trim($_GET['data'])     : null;

// Sanitize context vars
if(!pathSafe($CONTEXT_GET_SCRIPT))  $CONTEXT_GET_SCRIPT = null;

// Common HTTP headers
header('X-Powered-By: gitlab-deploy-hooks');
header('Content-Type: application/json');

// Common response array
$CONTEXT_RESPONSE = array(
  'version' => $CONTEXT_VERSION,

  'status'  => 'error',
  'type'    => 'general',
  'message' => 'General Error',

  'context' => array(
    'key'   => $CONTEXT_GET_KEY,
    'token'   => $CONTEXT_GET_TOKEN,
    'script'  => $CONTEXT_GET_SCRIPT,
    'type'    => $CONTEXT_GET_TYPE,
    'data'    => $CONTEXT_GET_DATA
  )
);

// Route request
if(($CONTEXT_GET_KEY != $CONTEXT_SECURITY_KEY) || ($CONTEXT_GET_TOKEN != $CONTEXT_TOKEN_KEY))
  include_once('../cgi/scripts/not_authenticated.php');
else if($CONTEXT_GET_SCRIPT && file_exists('../cgi/scripts/'.$CONTEXT_GET_SCRIPT.'.php'))
  include_once('../cgi/scripts/'.$CONTEXT_GET_SCRIPT.'.php');
else
  include_once('../cgi/scripts/not_found.php');

// Print JSON response
exit(json_encode($CONTEXT_RESPONSE));

?>
