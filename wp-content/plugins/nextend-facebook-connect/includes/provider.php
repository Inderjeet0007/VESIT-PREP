<?php

require_once dirname(__FILE__) . '/provider-dummy.php';

abstract class NextendSocialProvider extends NextendSocialProviderDummy {

    protected $dbID;
    protected $optionKey;

    protected $enabled = false;

    /** @var NextendSocialAuth */
    protected $client;

    protected $authUserData = array();

    protected $requiredFields = array();

    protected $svg = '';

    private $registrationContext = array();

    public function __construct($defaultSettings) {

        if (empty($this->dbID)) {
            $this->dbID = $this->id;
        }

        $this->optionKey = 'nsl_' . $this->id;

        $this->settings = new NextendSocialLoginSettings($this->optionKey, array_merge(array(
            'settings_saved'        => '0',
            'tested'                => '0',
            'custom_default_button' => '',
            'custom_icon_button'    => '',
            'login_label'           => '',
            'link_label'            => '',
            'unlink_label'          => '',
            'user_prefix'           => '',
            'user_fallback'         => '',
            'oauth_redirect_url'    => '',
        ), array(
            'ask_email'      => 'when-empty',
            'ask_user'       => 'never',
            'auto_link'      => 'email',
            'disabled_roles' => array(),
            'register_roles' => array(
                'default'
            )
        ), $defaultSettings));

        add_filter('nsl_update_settings_validate_' . $this->optionKey, array(
            $this,
            'validateSettings'
        ), 10, 2);

    }

    public function getOptionKey() {
        return $this->optionKey;
    }

    public function adminSettingsForm() {
        $subview = !empty($_REQUEST['subview']) ? $_REQUEST['subview'] : '';
        $this->adminDisplaySubView($subview);
    }

    public function adminDisplaySubView($subview) {
        switch ($subview) {
            case 'settings':
                $this->renderAdmin('settings');
                break;
            case 'buttons':
                $this->renderAdmin('buttons');
                break;
            case 'usage':
                $this->renderAdmin('usage');
                break;
            default:
                $this->renderAdmin('getting-started');
                break;
        }
    }

    public function renderAdmin($view, $showMenu = true) {
        include(NSL_PATH . '/admin/templates/header.php');
        $_view = $view;
        $view  = 'providers';
        include(NSL_PATH . '/admin/templates/menu.php');
        $view = $_view;
        echo '<div class="nsl-admin-content">';
        echo '<h1>' . $this->getLabel() . '</h1>';
        if ($showMenu) {
            include(NSL_PATH . '/admin/templates-provider/menu.php');
        }

        NextendSocialLoginAdminNotices::displayNotices();

        if ($view == 'buttons') {
            include(NSL_PATH . '/admin/templates-provider/buttons.php');
        } else if ($view == 'usage') {
            include(NSL_PATH . '/admin/templates-provider/usage.php');
        } else {
            include($this->path . '/admin/' . $view . '.php');
        }
        echo '</div>';
        include(NSL_PATH . '/admin/templates/footer.php');
    }

    public function renderOtherSettings() {
        include(NSL_PATH . '/admin/templates-provider/settings-other.php');
    }

    public function renderProSettings() {
        include(NSL_PATH . '/admin/templates-provider/settings-pro.php');
    }

    public function getRawDefaultButton() {
        return '<span class="nsl-button nsl-button-default nsl-button-' . $this->id . '" style="background-color:' . $this->color . ';">' . $this->svg . '<span>{{label}}</span></span>';
    }

    public function getRawIconButton() {
        return '<span class="nsl-button nsl-button-icon nsl-button-' . $this->id . '" style="background-color:' . $this->color . ';">' . $this->svg . '</span>';
    }

    public function getDefaultButton($label) {
        $button = $this->settings->get('custom_default_button');
        if (!empty($button)) {
            return str_replace('{{label}}', __($label, 'nextend-facebook-connect'), $button);
        }

        return str_replace('{{label}}', __($label, 'nextend-facebook-connect'), $this->getRawDefaultButton());
    }

    public function getIconButton() {
        $button = $this->settings->get('custom_icon_button');
        if (!empty($button)) {
            return $button;
        }

        return $this->getRawIconButton();
    }

    public function getAdminUrl($subview = '') {
        return add_query_arg(array(
            'subview' => $subview
        ), NextendSocialLoginAdmin::getAdminUrl('provider-' . $this->getId()));
    }

    public function getLoginUrl() {
        $args = array('loginSocial' => $this->getId());

        if (isset($_REQUEST['interim-login'])) {
            $args['interim-login'] = 1;
        }

        return add_query_arg($args, site_url('wp-login.php'));
    }

    public function validateSettings($newData, $postedData) {

        if (isset($postedData['custom_default_button'])) {
            if (isset($postedData['custom_default_button_enabled']) && $postedData['custom_default_button_enabled'] == '1') {
                $newData['custom_default_button'] = $postedData['custom_default_button'];
            } else {
                if ($postedData['custom_default_button'] != '') {
                    $newData['custom_default_button'] = '';
                }
            }
        }

        if (isset($postedData['custom_icon_button'])) {
            if (isset($postedData['custom_icon_button_enabled']) && $postedData['custom_icon_button_enabled'] == '1') {
                $newData['custom_icon_button'] = $postedData['custom_icon_button'];
            } else {
                if ($postedData['custom_icon_button'] != '') {
                    $newData['custom_icon_button'] = '';
                }
            }
        }

        foreach ($postedData AS $key => $value) {

            switch ($key) {
                case 'login_label':
                case 'link_label':
                case 'unlink_label':
                    $newData[$key] = wp_kses_post($value);
                    break;
                case 'user_prefix':
                case 'user_fallback':
                    $newData[$key] = preg_replace("/[^A-Za-z0-9\-_ ]/", '', $value);
                    break;
                case 'settings_saved':
                    $newData[$key] = intval($value) ? 1 : 0;
                    break;
                case 'oauth_redirect_url':
                    $newData[$key] = $value;
                    break;
            }
        }

        return $newData;
    }

    public function needPro() {
        return false;
    }

    public function enable() {
        $this->enabled = true;

        $this->onEnabled();

        return true;
    }

    protected function onEnabled() {

    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function isTested() {
        return !!$this->settings->get('tested');
    }

    public function checkOauthRedirectUrl() {
        $oauth_redirect_url = $this->settings->get('oauth_redirect_url');
        if (empty($oauth_redirect_url) || $oauth_redirect_url == $this->getLoginUrl()) {
            return true;
        }

        return false;
    }

    public function updateOauthRedirectUrl() {
        $this->settings->update(array(
            'oauth_redirect_url' => $this->getLoginUrl()
        ));
    }


    public function getState() {
        foreach ($this->requiredFields AS $name => $label) {
            $value = $this->settings->get($name);
            if (empty($value)) {
                return 'not-configured';
            }
        }
        if (!$this->isTested()) {
            return 'not-tested';
        }

        if (!$this->isEnabled()) {
            return 'disabled';
        }

        return 'enabled';
    }

    public function connect() {
        try {
            $this->doAuthenticate();
        } catch (NSLContinuePageRenderException $e) {
            // This is not an error. We allow the page to continue the normal display flow and later we inject our things.
            // Used by Theme my login function where we override the shortcode and we display our email request.
        } catch (Exception $e) {
            $this->onError($e);
        }
    }

    /**
     * @return NextendSocialAuth
     */
    protected abstract function getClient();

    /**
     * @throws NSLContinuePageRenderException
     */
    protected function doAuthenticate() {

        if (!headers_sent()) {
            //All In One WP Security sets a LOCATION header, so we need to remove it to do a successful test.
            if (function_exists('header_remove')) {
                header_remove("LOCATION");
            } else {
                header('LOCATION:', true); //Under PHP 5.3
            }
        }

        if (!$this->isTest()) {
            add_action($this->id . '_login_action_before', array(
                $this,
                'liveConnectBefore'
            ));
            add_action($this->id . '_login_action_redirect', array(
                $this,
                'liveConnectRedirect'
            ));
            add_action($this->id . '_login_action_get_user_profile', array(
                $this,
                'liveConnectGetUserProfile'
            ), 10, 3);

            $interim_login = isset($_REQUEST['interim-login']);
            if ($interim_login) {
                NextendSocialLoginPersistentAnonymous::set($this->id . '_interim_login', 1);
            }

            $display = isset($_REQUEST['display']);
            if ($display && $_REQUEST['display'] == 'popup') {
                NextendSocialLoginPersistentAnonymous::set($this->id . '_display', 'popup');
            }
        } else {
            add_action($this->id . '_login_action_get_user_profile', array(
                $this,
                'testConnectGetUserProfile'
            ));
        }


        do_action($this->id . '_login_action_before', $this);

        $client = $this->getClient();

        $accessTokenData = $this->getAnonymousAccessToken();

        $client->checkError();

        do_action($this->id . '_login_action_redirect', $this);

        if (!$accessTokenData && !$client->hasAuthenticateData()) {

            header('LOCATION: ' . $client->createAuthUrl());
            exit;

        } else {

            if (!$accessTokenData) {

                $accessTokenData = $client->authenticate();

                $accessTokenData = $this->requestLongLivedToken($accessTokenData);

                $this->setAnonymousAccessToken($accessTokenData);
            } else {
                $client->setAccessTokenData($accessTokenData);
            }
            if (NextendSocialLoginPersistentAnonymous::get($this->id . '_display') == 'popup') {
                NextendSocialLoginPersistentAnonymous::delete($this->id . '_display');
                ?>
                <!doctype html>
                <html lang=en>
                <head>
                    <meta charset=utf-8>
                    <title><?php _e('Authentication successful', 'nextend-facebook-connect'); ?></title>
                    <script type="text/javascript">
						try {
                            if (window.opener !== null) {
                                window.opener.location = <?php echo wp_json_encode($this->getLoginUrl()); ?>;
                                window.close();
                            }
                        }
                        catch (e) {
                        }
                        window.location.reload(true);
                    </script>
                    <meta http-equiv="refresh" content="0">
                </head>
                </html>
                <?php
                exit;
            }

            $this->authUserData = $this->getCurrentUserInfo();

            do_action($this->id . '_login_action_get_user_profile', $accessTokenData);
        }
    }

    public function liveConnectGetUserProfile($accessToken) {

        $ID = $this->getUserIDByProviderIdentifier($this->getAuthUserData('id'));
        if ($ID && !get_user_by('id', $ID)) {
            $this->removeConnectionByUserID($ID);
            $ID = null;
        }
        if (!is_user_logged_in()) {

            if ($ID == null) {
                $this->prepareRegister($accessToken);
            } else {
                $this->login($ID, $accessToken);
            }
        } else {
            $current_user = wp_get_current_user();
            if ($ID === null) {
                // Let's connect the account to the current user!

                if ($this->linkUserToProviderIdentifier($current_user->ID, $this->getAuthUserData('id'))) {

                    $this->saveUserData($current_user->ID, 'access_token', $accessToken);

                    NextendSocialLoginAdminNotices::addSuccess(sprintf(__('Your %1$s account is successfully linked with your account. Now you can sign in with %2$s easily.', 'nextend-facebook-connect'), $this->getLabel(), $this->getLabel()));
                } else {

                    NextendSocialLoginAdminNotices::addError(sprintf(__('You have already linked a(n) %s account. Please unlink the current and then you can link other %s account.', 'nextend-facebook-connect'), $this->getLabel(), $this->getLabel()));
                }

            } else if ($current_user->ID != $ID) {

                NextendSocialLoginAdminNotices::addError(sprintf(__('This %s account is already linked to other user.', 'nextend-facebook-connect'), $this->getLabel()));
            }
        }
        $this->redirectToLastLocation();
    }

    /**
     * @param $user_id
     * @param $providerIdentifier
     *
     * @return bool
     */
    protected function linkUserToProviderIdentifier($user_id, $providerIdentifier) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $connectedProviderID = $this->getProviderIdentifierByUserID($user_id);

        if ($connectedProviderID !== null) {
            if ($connectedProviderID == $providerIdentifier) {
                // This provider already linked to this user
                return true;
            }

            // User already have this provider attached to his account with different provider id.
            return false;
        }

        $wpdb->insert($wpdb->prefix . 'social_users', array(
            'ID'         => $user_id,
            'type'       => $this->dbID,
            'identifier' => $providerIdentifier
        ), array(
            '%d',
            '%s',
            '%s'
        ));

        do_action('nsl_' . $this->getId() . '_link_user', $user_id, $this->getId());

        return true;
    }

    protected function getUserIDByProviderIdentifier($identifier) {
        /** @var $wpdb WPDB */
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare('SELECT ID FROM `' . $wpdb->prefix . 'social_users` WHERE type = %s AND identifier = %s', array(
            $this->dbID,
            $identifier
        )));
    }

    protected function getProviderIdentifierByUserID($user_id) {
        /** @var $wpdb WPDB */
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare('SELECT identifier FROM `' . $wpdb->prefix . 'social_users` WHERE type = %s AND ID = %s', array(
            $this->dbID,
            $user_id
        )));
    }

    protected function removeConnectionByUserID($user_id) {
        /** @var $wpdb WPDB */
        global $wpdb;

        $wpdb->query($wpdb->prepare('DELETE FROM `' . $wpdb->prefix . 'social_users` WHERE type = %s AND ID = %d', array(
            $this->dbID,
            $user_id
        )));
    }

    protected function unlinkUser() {
        $user_info = wp_get_current_user();
        if ($user_info->ID) {
            $this->removeConnectionByUserID($user_info->ID);

            return true;
        }

        return false;
    }

    public function isCurrentUserConnected() {
        /** @var $wpdb WPDB */
        global $wpdb;

        $current_user = wp_get_current_user();
        $ID           = $wpdb->get_var($wpdb->prepare('SELECT identifier FROM `' . $wpdb->prefix . 'social_users` WHERE type LIKE %s AND ID = %d', array(
            $this->dbID,
            $current_user->ID
        )));
        if ($ID === null) {
            return false;
        }

        return $ID;
    }

    public function getConnectButton($buttonStyle = 'default', $redirectTo = null, $trackerData = false) {
        $arg = array();
        if ($redirectTo != null) {
            $arg['redirect'] = urlencode($redirectTo);
        } else if (isset($_GET['redirect_to'])) {
            $arg['redirect'] = urlencode($_GET['redirect_to']);
        }

        if ($trackerData !== false) {
            $arg['trackerdata']      = urlencode($trackerData);
            $arg['trackerdata_hash'] = urlencode(wp_hash($trackerData));

        }

        switch ($buttonStyle) {
            case 'icon':

                $button = $this->getIconButton();
                break;
            default:

                $button = $this->getDefaultButton($this->settings->get('login_label'));
                break;
        }

        return '<a href="' . esc_url(add_query_arg($arg, $this->getLoginUrl())) . '" rel="nofollow" aria-label="' . esc_attr__($this->settings->get('login_label')) . '" data-plugin="nsl" data-action="connect" data-provider="' . esc_attr($this->getId()) . '" data-popupwidth="' . $this->getPopupWidth() . '" data-popupheight="' . $this->getPopupHeight() . '">' . $button . '</a>';
    }

    public function getLinkButton() {

        return '<a href="' . esc_url(add_query_arg('redirect', urlencode(NextendSocialLogin::getCurrentPageURL()), $this->getLoginUrl())) . '" style="text-decoration:none;display:inline-block;box-shadow:none;" data-plugin="nsl" data-action="link" data-provider="' . esc_attr($this->getId()) . '" data-popupwidth="' . $this->getPopupWidth() . '" data-popupheight="' . $this->getPopupHeight() . '" aria-label="' . esc_attr__($this->settings->get('link_label')) . '">' . $this->getDefaultButton($this->settings->get('link_label')) . '</a>';
    }

    public function getUnLinkButton() {

        return '<a href="' . esc_url(add_query_arg(array(
                'action'   => 'unlink',
                'redirect' => urlencode(NextendSocialLogin::getCurrentPageURL())
            ), $this->getLoginUrl())) . '" style="text-decoration:none;display:inline-block;box-shadow:none;" data-plugin="nsl" data-action="unlink" data-provider="' . esc_attr($this->getId()) . '" aria-label="' . esc_attr__($this->settings->get('unlink_label')) . '">' . $this->getDefaultButton($this->settings->get('unlink_label')) . '</a>';
    }

    protected function redirectToLoginForm() {
        self::safeRedirect(__('Authentication error', 'nextend-facebook-connect'), site_url('wp-login.php'));
    }

    public function liveConnectBefore() {

        if (is_user_logged_in() && $this->isCurrentUserConnected()) {

            if (isset($_GET['action']) && $_GET['action'] == 'unlink') {
                if ($this->unlinkUser()) {
                    NextendSocialLoginAdminNotices::addSuccess(__('Unlink successful.', 'nextend-facebook-connect'));
                }
            }

            $this->redirectToLastLocation();
            exit;
        }
    }

    public function liveConnectRedirect() {
        if (!empty($_GET['trackerdata']) && !empty($_GET['trackerdata_hash'])) {
            if (wp_hash($_GET['trackerdata']) === $_GET['trackerdata_hash']) {
                NextendSocialLoginPersistentAnonymous::set('trackerdata', $_GET['trackerdata']);
            }
        }

        if (!is_user_logged_in()) {
            $redirectToLogin = NextendSocialLogin::$settings->get('redirect');
            if (!empty($redirectToLogin)) {
                $_GET['redirect'] = $redirectToLogin;
            }
        }

        if (isset($_GET['redirect'])) {
            NextendSocialLoginPersistentAnonymous::set('_redirect', $_GET['redirect']);
            $redirect = $_GET['redirect'];
        } else {
            $redirect = NextendSocialLoginPersistentAnonymous::get('_redirect');
        }

        $redirect = apply_filters($this->id . '_login_redirect_url', $redirect, $this);

        if ($redirect == '' || $redirect == $this->getLoginUrl()) {
            NextendSocialLoginPersistentAnonymous::set('_redirect', site_url());
        }
    }

    protected function updateRedirectOnRegister() {
        $redirect = NextendSocialLogin::$settings->get('redirect_reg');

        $redirect = apply_filters($this->id . '_register_redirect_url', $redirect, $this);

        if (!empty($redirect)) {
            NextendSocialLoginPersistentAnonymous::set('_redirect', $redirect);
        }
    }

    protected function redirectToLastLocation() {

        if (NextendSocialLoginPersistentAnonymous::get($this->id . '_interim_login') == 1) {
            $this->deleteLoginPersistentData();

            $url = add_query_arg('interim_login', 'nsl', site_url('wp-login.php', 'login'));
            ?>
            <!doctype html>
            <html lang=en>
            <head>
                <meta charset=utf-8>
                <title><?php _e('Authentication successful', 'nextend-facebook-connect'); ?></title>
                <script type="text/javascript">
					window.location = <?php echo wp_json_encode($url); ?>;
                </script>
                <meta http-equiv="refresh" content="0;<?php echo esc_attr($url); ?>">
            </head>
            </html>
            <?php
            exit;
        }

        self::safeRedirect(__('Authentication successful', 'nextend-facebook-connect'), $this->getLastLocationRedirectTo());
    }

    protected function getLastLocationRedirectTo() {
        $redirect = NextendSocialLoginPersistentAnonymous::get('_redirect');

        if (!$redirect || $redirect == '' || $redirect == $this->getLoginUrl()) {
            if (isset($_GET['redirect'])) {
                $redirect = $_GET['redirect'];
            } else {
                $redirect = site_url();
            }
        }
        $redirect = wp_sanitize_redirect($redirect);
        $redirect = wp_validate_redirect($redirect, site_url());

        NextendSocialLoginPersistentAnonymous::delete('_redirect');

        return $redirect;
    }

    protected function login($user_id, $access_token) {

        add_action('nsl_' . $this->getId() . '_login', array(
            $this,
            'syncProfile'
        ), 10, 3);

        $isLoginAllowed = apply_filters('nsl_' . $this->getId() . '_is_login_allowed', true, $this, $user_id);

        if ($isLoginAllowed) {
            $secure_cookie = is_ssl();
            $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
            global $auth_secure_cookie; // XXX ugly hack to pass this to wp_authenticate_cookie

            $auth_secure_cookie = $secure_cookie;
            wp_set_auth_cookie($user_id, true, $secure_cookie);
            $user_info = get_userdata($user_id);
            do_action('wp_login', $user_info->user_login, $user_info);

            do_action('nsl_login', $user_id, $this);
            do_action('nsl_' . $this->getId() . '_login', $user_id, $this, $access_token);

            $this->redirectToLastLocation();

        }

        $this->redirectToLoginForm();
    }

    protected function prepareRegister($accessToken) {

        $user_id = false;

        $email          = $this->getAuthUserData('email');
        $providerUserID = $this->getAuthUserData('id');

        if (empty($email)) {
            $email = '';
        } else {
            $user_id = email_exists($email);
        }
        if ($user_id === false) { // Real register
            if (apply_filters('nsl_is_register_allowed', true, $this)) {
                $this->register($providerUserID, $accessToken, $email, $this->getAuthUserData('name'), $this->getAuthUserData('first_name'), $this->getAuthUserData('last_name'));
            } else {
                self::safeRedirect(__('Authentication error', 'nextend-facebook-connect'), site_url('wp-login.php?registration=disabled'));
                exit;
            }

        } else if ($this->autoLink($user_id, $providerUserID)) {
            $this->login($user_id, $accessToken);
        }

        $this->redirectToLoginForm();
    }

    /**
     * @param $user_id
     * @param $provider     NextendSocialProvider
     * @param $access_token string
     */
    public function syncProfile($user_id, $provider, $access_token) {
    }

    protected function register($providerID, $access_token, $email, $name = '', $first_name = '', $last_name = '') {

        $username = strtolower($first_name . $last_name);
        if (empty($username)) {
            $username = strtolower($name);
        }

        $username = preg_replace('/\s+/', '', $username);

        $sanitized_user_login = sanitize_user($this->settings->get('user_prefix') . $username, true);
        if (empty($username) || !validate_username($sanitized_user_login)) {

            //@TODO If username is not valid, we should ask the user to enter custom username with Pro Addon
            $sanitized_user_login = sanitize_user($this->settings->get('user_fallback') . $providerID, true);
        }
        $default_user_name = $sanitized_user_login;

        $i = 1;
        while (username_exists($sanitized_user_login)) {
            $sanitized_user_login = $default_user_name . $i;
            $i++;
        }

        $userData = array(
            'email'    => $email,
            'username' => $sanitized_user_login
        );

        $userData = apply_filters('nsl_' . $this->getId() . '_register_user_data', $userData);

        if (empty($userData['email'])) {
            $userData['email'] = $providerID . '@' . $this->getId() . '.unknown';
        }

        $user_pass = wp_generate_password(12, false);

        do_action('nsl_pre_register_new_user', $userData, $this);

        add_action('user_register', array(
            $this,
            'registerComplete'
        ), -1);

        $this->registrationContext['name']         = $name;
        $this->registrationContext['first_name']   = $first_name;
        $this->registrationContext['last_name']    = $last_name;
        $this->registrationContext['access_token'] = $access_token;

        wp_create_user($userData['username'], $user_pass, $userData['email']);

        //registerComplete will log in user and redirects. If we reach here, the user creation failed.
        return false;
    }

    public function registerComplete($user_id) {

        $user_data = array();
        if (!empty($this->registrationContext['name'])) {
            $user_data['display_name'] = $this->registrationContext['name'];
        }
        if (!empty($this->registrationContext['first_name'])) {
            $user_data['first_name'] = $this->registrationContext['first_name'];
            if (class_exists('WooCommerce', false)) {
                add_user_meta($user_id, 'billing_first_name', $this->registrationContext['first_name']);
            }
        }
        if (!empty($this->registrationContext['last_name'])) {
            $user_data['last_name'] = $this->registrationContext['last_name'];
            if (class_exists('WooCommerce', false)) {
                add_user_meta($user_id, 'billing_last_name', $this->registrationContext['last_name']);
            }
        }
        if (!empty($user_data)) {
            $user_data['ID'] = $user_id;
            wp_update_user($user_data);
        }

        update_user_option($user_id, 'default_password_nag', true, true);

        $this->linkUserToProviderIdentifier($user_id, $this->getAuthUserData('id'));

        do_action('nsl_register_new_user', $user_id, $this);
        do_action('nsl_' . $this->getId() . '_register_new_user', $user_id, $this);

        $this->updateRedirectOnRegister();

        $this->deleteLoginPersistentData();

        do_action('register_new_user', $user_id);

        $this->login($user_id, $this->registrationContext['access_token']);
    }

    protected function autoLink($user_id, $providerUserID) {

        $isAutoLinkAllowed = true;
        $isAutoLinkAllowed = apply_filters('nsl_' . $this->getId() . '_auto_link_allowed', $isAutoLinkAllowed, $this, $user_id);
        if ($isAutoLinkAllowed) {
            return $this->linkUserToProviderIdentifier($user_id, $providerUserID);
        }

        return false;
    }

    public function isTest() {
        if (is_user_logged_in() && current_user_can('manage_options')) {
            if (isset($_REQUEST['test'])) {
                NextendSocialLoginPersistentUser::set('_test', 1);

                return true;
            } else if (NextendSocialLoginPersistentUser::get('_test') == 1) {
                return true;
            }
        }

        return false;
    }

    public function testConnectGetUserProfile() {

        $this->deleteLoginPersistentData();

        $this->settings->update(array(
            'tested'             => 1,
            'oauth_redirect_url' => $this->getLoginUrl()
        ));

        NextendSocialLoginAdminNotices::addSuccess(__('The test was successful', 'nextend-facebook-connect'));

        ?>
        <!doctype html>
        <html lang=en>
        <head>
            <meta charset=utf-8>
            <title><?php _e('The test was successful', 'nextend-facebook-connect'); ?></title>
            <script type="text/javascript">
				window.opener.location.reload(true);
                window.close();
            </script>
        </head>
        </html>
        <?php
        exit;
    }

    protected function setAnonymousAccessToken($accessToken) {
        NextendSocialLoginPersistentAnonymous::set($this->id . '_at', $accessToken);
    }

    protected function getAnonymousAccessToken() {
        return NextendSocialLoginPersistentAnonymous::get($this->id . '_at');
    }

    protected function deleteLoginPersistentData() {
        NextendSocialLoginPersistentAnonymous::delete($this->id . '_at');
        NextendSocialLoginPersistentAnonymous::delete($this->id . '_interim_login');
        NextendSocialLoginPersistentAnonymous::delete($this->id . '_display');
        NextendSocialLoginPersistentUser::delete('_test');
    }

    /**
     * @param $e Exception
     */
    protected function onError($e) {
        if (NextendSocialLogin::$settings->get('debug') == 1 || $this->isTest()) {
            header('HTTP/1.0 401 Unauthorized');
            echo "Error: " . $e->getMessage() . "\n";
        } else {
            //@TODO we might need to make difference between user cancelled auth and error and redirect the user based on that.
            $url = $this->getLastLocationRedirectTo();
            ?>
            <!doctype html>
            <html lang=en>
            <head>
                <meta charset=utf-8>
                <title><?php echo __('Authentication failed', 'nextend-facebook-connect'); ?></title>
                <script type="text/javascript">
					try {
                        if (window.opener !== null) {
                            window.close();
                        }
                    }
                    catch (e) {
                    }
                    window.location = <?php echo wp_json_encode($url); ?>;
                </script>
                <meta http-equiv="refresh" content="0;<?php echo esc_attr($url); ?>">
            </head>
            <body>
            </body>
            </html>
            <?php
        }
        $this->deleteLoginPersistentData();
        exit;
    }

    protected function saveUserData($user_id, $key, $data) {
        update_user_meta($user_id, $this->id . '_' . $key, $data);
    }

    protected function getUserData($user_id, $key) {
        return get_user_meta($user_id, $this->id . '_' . $key, true);
    }

    public function getAccessToken($user_id) {
        return $this->getUserData($user_id, 'access_token');
    }

    public function getAvatar($user_id) {
        $picture = $this->getUserData($user_id, 'profile_picture');
        if (!$picture || $picture == '') {
            return false;
        }

        return $picture;
    }

    /**
     * @return array
     */
    protected function getCurrentUserInfo() {
        return array();
    }

    protected function requestLongLivedToken($accessTokenData) {
        return $accessTokenData;
    }

    /**
     * @param $key
     *
     * @throws Exception
     * @return string
     */
    protected function getAuthUserData($key) {

        throw new Exception('getAuthUserData ' . $key . ' is not supported.');
    }

    public function renderSettingsHeader() {

        $state = $this->getState();
        ?>
        <?php if ($state == 'not-tested') : ?>
            <div class="nsl-box nsl-box-blue">
                <h2 class="title"><?php _e('Your configuration needs to be verified', 'nextend-facebook-connect'); ?></h2>
                <p><?php _e('Before you can start letting your users register with your app it needs to be tested. This test makes sure that no users will have troubles with the login and registration process. <br> If you see error message in the popup check the copied ID and secret or the app itself. Otherwise your settings are fine.', 'nextend-facebook-connect'); ?></p>

                <p id="nsl-test-configuration">
                    <a id="nsl-test-button" href="#"
                       onclick="NSLPopupCenter('<?php echo add_query_arg('test', '1', $this->getLoginUrl()); ?>', 'test-window', <?php echo $this->getPopupWidth(); ?>, <?php echo $this->getPopupHeight(); ?>); return false;"
                       class="button button-primary"><?php _e('Verify Settings', 'nextend-facebook-connect'); ?></a>
                    <span id="nsl-test-please-save"><?php _e('Please save your changes to verify settings.', 'nextend-facebook-connect'); ?></span>
                </p>
            </div>
        <?php endif; ?>


        <?php if ($this->settings->get('tested') == '1') : ?>
            <div class="nsl-box <?php if ($state == 'enabled'): ?>nsl-box-green<?php else: ?> nsl-box-yellow nsl-box-exclamation-mark<?php endif; ?>">
                <h2 class="title"><?php _e('Works Fine', 'nextend-facebook-connect'); ?> -
                    <?php
                    switch ($state) {
                        case 'disabled':
                            _e('Disabled', 'nextend-facebook-connect');
                            break;
                        case 'enabled':
                            _e('Enabled', 'nextend-facebook-connect');
                            break;
                    }
                    ?></h2>
                <p><?php
                    switch ($state) {
                        case 'disabled':
                            printf(__('This provider is currently disabled, which means that users can’t register or login via their %s account.', 'nextend-facebook-connect'), $this->getLabel());
                            break;
                        case 'enabled':
                            printf(__('This provider works fine, but you can test it again. If you don’t want to let users register or login with %s anymore you can disable it.', 'nextend-facebook-connect'), $this->getLabel());
                            echo '</p>';
                            echo '<p>';
                            printf(__('This provider is currently enabled, which means that users can register or login via their %s account.', 'nextend-facebook-connect'), $this->getLabel());
                            break;
                    }
                    ?></p>

                <p id="nsl-test-configuration">
                    <a id="nsl-test-button" href="#"
                       onclick="NSLPopupCenter('<?php echo add_query_arg('test', '1', $this->getLoginUrl()); ?>', 'test-window', <?php echo $this->getPopupWidth(); ?>, <?php echo $this->getPopupHeight(); ?>); return false"
                       class="button button-secondary"><?php _e('Verify Settings Again', 'nextend-facebook-connect'); ?></a>
                    <span id="nsl-test-please-save"><?php _e('Please save your changes before testing.', 'nextend-facebook-connect'); ?></span>
                    <?php
                    switch ($state) {
                        case 'disabled':
                            ?>
                            <a href="<?php echo wp_nonce_url(add_query_arg('provider', $this->getId(), NextendSocialLoginAdmin::getAdminUrl('sub-enable')), 'nextend-social-login_enable_' . $this->getId()); ?>"
                               class="button button-primary">
								<?php _e('Enable', 'nextend-facebook-connect'); ?>
                            </a>
                            <?php
                            break;
                        case 'enabled':
                            ?>
                            <a href="<?php echo wp_nonce_url(add_query_arg('provider', $this->getId(), NextendSocialLoginAdmin::getAdminUrl('sub-disable')), 'nextend-social-login_disable_' . $this->getId()); ?>"
                               class="button button-secondary">
								<?php _e('Disable', 'nextend-facebook-connect'); ?>
                            </a>
                            <?php
                            break;
                    }
                    ?>
                </p>
            </div>
        <?php endif; ?>


        <script type="text/javascript">

			jQuery(document).on('ready', function () {
                var $test = jQuery('#nsl-test-configuration');
                if ($test.length) {
                    jQuery(<?php echo wp_json_encode('#' . implode(',#', array_keys($this->requiredFields))); ?>)
                        .on('keyup.test', function () {
                            jQuery('#nsl-test-button').remove();
                            jQuery('#nsl-test-please-save').css('display', 'inline');
                            jQuery('input').off('keyup.test');
                        });
                }
            });
        </script>
        <?php
    }

    private static function safeRedirect($title, $url) {
        ?>
        <!doctype html>
        <html lang=en>
        <head>
            <meta charset=utf-8>
            <title><?php echo $title; ?></title>
            <script type="text/javascript">
				try {
                    if (window.opener !== null) {
                        window.opener.location = <?php echo wp_json_encode($url); ?>;
                        window.close();
                    }
                }
                catch (e) {
                }
                window.location = <?php echo wp_json_encode($url); ?>;
            </script>
            <meta http-equiv="refresh" content="0;<?php echo esc_attr($url); ?>">
        </head>
        <body>
        </body>
        </html>
        <?php
        exit;
    }

    public function renderOauthChangedInstruction() {
        echo '<h2>' . $this->getLabel() . '</h2>';

        include($this->path . '/admin/fix-redirect-uri.php');
    }
}