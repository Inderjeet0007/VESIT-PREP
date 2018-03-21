<?php

class Nextend_Social_Login_Widget extends WP_Widget {

    public static function register() {
        register_widget('Nextend_Social_Login_Widget');
    }

    public function __construct() {
        parent::__construct('nextend_social_login', sprintf(__('%s Buttons', 'nextend-facebook-connect'), 'Nextend Social Login'));
    }

    public function form($instance) {
        $instance = wp_parse_args((array)$instance, array('title' => ''));
        $title    = $instance['title'];

        $style = isset($instance['style']) ? $instance['style'] : 'default';

        $isPRO = apply_filters('nsl-pro', false);

        ?>
        <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                       name="<?php echo $this->get_field_name('title'); ?>" type="text"
                       value="<?php echo esc_attr($title); ?>"/></label></p>

        <?php if ($isPRO): ?>

            <p>
                <label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Button style:', 'nextend-facebook-connect'); ?></label><br>
                <input class="widefat" id="<?php echo $this->get_field_id('style_default'); ?>"
                       name="<?php echo $this->get_field_name('style'); ?>" type="radio" value="default"
                       <?php if ($style == 'default'): ?>checked<?php endif; ?>/>
                <label for="<?php echo $this->get_field_id('style_default'); ?>"><?php _e('Default', 'nextend-facebook-connect'); ?></label>
                <br>
                <input class="widefat" id="<?php echo $this->get_field_id('style_icon'); ?>"
                       name="<?php echo $this->get_field_name('style'); ?>" type="radio" value="icon"
                       <?php if ($style == 'icon'): ?>checked<?php endif; ?>/>
                <label for="<?php echo $this->get_field_id('style_icon'); ?>"><?php _e('Icon', 'nextend-facebook-connect'); ?></label>
                <br>
            </p>
        <?php endif; ?>
        <?php
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';

        $title = apply_filters('widget_title', $title, $instance, $this->id_base);

        $style = !empty($instance['style']) ? $instance['style'] : 'default';

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        echo do_shortcode('[nextend_social_login style="' . $style . '"]');

        echo $args['after_widget'];
    }
}

add_action('widgets_init', 'Nextend_Social_Login_Widget::register');
