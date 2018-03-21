<?php
defined('ABSPATH') || die();
/** @var $this NextendSocialProvider */
/** @var $view string */
?>
<div class="nsl-admin-sub-nav-bar">
    <a href="<?php echo $this->getAdminUrl(); ?>"
       class="nsl-admin-nav-tab<?php if ($view === 'getting-started'): ?> nsl-admin-nav-tab-active<?php endif; ?>"><?php _e('Getting Started', 'nextend-facebook-connect'); ?></a>
    <a href="<?php echo $this->getAdminUrl('settings'); ?>"
       class="nsl-admin-nav-tab<?php if ($view === 'settings'): ?> nsl-admin-nav-tab-active<?php endif; ?>"><?php _e('Settings', 'nextend-facebook-connect'); ?></a>
    <a href="<?php echo $this->getAdminUrl('buttons'); ?>"
       class="nsl-admin-nav-tab<?php if ($view === 'buttons'): ?> nsl-admin-nav-tab-active<?php endif; ?>"><?php _e('Buttons', 'nextend-facebook-connect'); ?></a>
    <a href="<?php echo $this->getAdminUrl('usage'); ?>"
       class="nsl-admin-nav-tab<?php if ($view === 'usage'): ?> nsl-admin-nav-tab-active<?php endif; ?>"><?php _e('Usage', 'nextend-facebook-connect'); ?></a>
</div>