<?php

if (!function_exists('is_nextend_facebook_login')) {
    /**
     * Returns true if Nextend Facebook Connect plugin is activated
     * Used by Flatsome theme
     *
     * @deprecated
     * @return bool
     */
    function is_nextend_facebook_login() {
        if (class_exists('NextendSocialLogin', false)) {
            return NextendSocialLogin::isProviderEnabled('facebook');
        }

        return defined('NEW_FB_LOGIN');
    }
}

if (!function_exists('is_nextend_google_login')) {
    /**
     * Returns true if Nextend Google Connect plugin is activated
     * Used by Flatsome theme
     *
     * @deprecated
     * @return bool
     */
    function is_nextend_google_login() {
        if (class_exists('NextendSocialLogin', false)) {
            return NextendSocialLogin::isProviderEnabled('google');
        }

        return defined('NEW_GOOGLE_LOGIN');
    }
}
