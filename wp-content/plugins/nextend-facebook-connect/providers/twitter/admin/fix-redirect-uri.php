<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProvider */
?>
<ol>
    <li><?php printf(__('Navigate to %s', 'nextend-facebook-connect'), '<a href="https://apps.twitter.com/" target="_blank">https://apps.twitter.com/</a>'); ?></li>
    <li><?php printf(__('Log in with your %s credentials if you are not logged in', 'nextend-facebook-connect'), 'Twitter'); ?></li>
    <li><?php _e('Click on the App', 'nextend-facebook-connect'); ?></li>
    <li><?php _e('Click on the "Settings" tab', 'nextend-facebook-connect'); ?></li>
    <li><?php printf(__('Add the following URL to the "Callback URL" field: <b>%s</b>', 'nextend-facebook-connect'), $this->getLoginUrl()); ?></li>
    <li><?php _e('Click on "Update Settings"', 'nextend-facebook-connect'); ?></li>
</ol>