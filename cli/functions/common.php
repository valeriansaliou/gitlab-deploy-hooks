<?php

/*
 *	gitlab-deploy-hooks
 *	Common Functions (command-line)
 */

// Returns from where script is invoked
function caller() {
	if((php_sapi_name() == 'cli') || empty($_SERVER['REMOTE_ADDR'])) {
		return 'cli';
	}

	return 'cgi';
}

// Returns whether a match exists from passed array or not
function matchExists($search_string, $list_array) {
    $exists = false;

    foreach($list_array as $cur_match) {
        if(strpos($search_string, $cur_match) !== false) {
            $exists = true;
            break;
        }
    }

    return $exists;
}

?>
