<?php
/*
Plugin Name: Nextend Social Login
Plugin URI: https://nextendweb.com/
Description: Nextend Social Login displays social login buttons for Facebook, Google and Twitter.
Version: 3.0.4
Author: Nextendweb
License: GPL2
Text Domain: nextend-facebook-connect
Domain Path: /languages
*/

if (!defined('NSL_PATH_FILE')) {
    define('NSL_PATH_FILE', __FILE__);
}

if (!defined('NSL_PATH')) {
    define('NSL_PATH', dirname(NSL_PATH_FILE));
}
if (!defined('NSL_PLUGIN_BASENAME')) {
    define('NSL_PLUGIN_BASENAME', plugin_basename(NSL_PATH_FILE));
}

require_once(NSL_PATH . '/nextend-social-login.php');