<?php
/** @var $currentProvider string */

$provider = NextendSocialLogin::$providers[$currentProvider];

$provider->adminSettingsForm();