<?php

/*
 *	gitlab-deploy-hooks
 *	Common Functions
 */

// Checks if path is safe (no ../ and ./)
function pathSafe($path) {
    return !preg_match('/(^\.\.)|(\.\.\/)|(\.\.$)/', $path);
}

?>
