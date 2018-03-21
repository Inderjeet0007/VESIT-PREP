<?php
/**
 * Movers Lite Theme Customizer
 *
 * @package Movers Lite
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function movers_lite_customize_register( $wp_customize ) {
	
function movers_lite_sanitize_checkbox( $checked ) {
	// Boolean check.
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
	
	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';
		
	$wp_customize->add_setting('color_scheme', array(
		'default' => '#4d4d4d',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'color_scheme',array(
			'label' => __('Color Scheme','movers-lite'),
			'description'	=> __('Select color from here.','movers-lite'),
			'section' => 'colors',
			'settings' => 'color_scheme'
		))
	);
	
	$wp_customize->add_setting('topbar-color', array(
		'default' => '#1b273d',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'topbar-color',array(
			'description'	=> __('Select background color for topbar.','movers-lite'),
			'section' => 'colors',
			'settings' => 'topbar-color'
		))
	);
	
	$wp_customize->add_setting('headerbg-color', array(
		'default' => '#263247',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'headerbg-color',array(
			'description'	=> __('Select background color for header.','movers-lite'),
			'section' => 'colors',
			'settings' => 'headerbg-color'
		))
	);
	
	$wp_customize->add_setting('navbg-color', array(
		'default' => '#ffb600',
		'sanitize_callback'	=> 'sanitize_hex_color',
	));
	
	$wp_customize->add_control(
		new WP_Customize_Color_Control($wp_customize,'navbg-color',array(
			'description'	=> __('Select background color for navigation.','movers-lite'),
			'section' => 'colors',
			'settings' => 'navbg-color'
		))
	);
	
	// Slider Section Start		
	$wp_customize->add_section(
        'slider_section',
        array(
            'title' => __('Slider Settings', 'movers-lite'),
            'priority' => null,
			'description'	=> __('Recommended image size (1420x567). Slider will work only when you select the static front page.','movers-lite'),	
        )
    );
	
	$wp_customize->add_setting('page-setting7',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting7',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide one:','movers-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting8',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting8',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide two:','movers-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('page-setting9',array(
			'default' => '0',
			'capability' => 'edit_theme_options',	
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting9',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for slide three:','movers-lite'),
			'section'	=> 'slider_section'
	));	
	
	$wp_customize->add_setting('slide_text',array(
		'default'	=> __('Read More','movers-lite'),
		'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('slide_text',array(
		'label'	=> __('Add slider link button text.','movers-lite'),
		'section'	=> 'slider_section',
		'setting'	=> 'slide_text',
		'type'	=> 'text'
	));
	
	$wp_customize->add_setting('hide_slider',array(
			'default' => true,
			'sanitize_callback' => 'movers_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_slider', array(
		   'settings' => 'hide_slider',
    	   'section'   => 'slider_section',
    	   'label'     => __('Check this to hide slider.','movers-lite'),
    	   'type'      => 'checkbox'
     ));	
	
	// Slider Section End
	
	// Homepage Section Start		
	$wp_customize->add_section(
        'homepage_section',
        array(
            'title' => __('Homepage Section', 'movers-lite'),
            'priority' => null,
			'description'	=> __('Select page for homepage section. This section will be displayed only when you select the static front page.','movers-lite'),	
        )
    );	
	
	$wp_customize->add_setting('page-setting1',array(
			'default' => '0',
			'capability' => 'edit_theme_options',
			'sanitize_callback'	=> 'absint'
	));
	
	$wp_customize->add_control('page-setting1',array(
			'type'	=> 'dropdown-pages',
			'label'	=> __('Select page for section:','movers-lite'),
			'section'	=> 'homepage_section'
	));	
	
	$wp_customize->add_setting('hide_section',array(
			'default' => true,
			'sanitize_callback' => 'movers_lite_sanitize_checkbox',
			'capability' => 'edit_theme_options',
	));	 

	$wp_customize->add_control( 'hide_section', array(
		   'settings' => 'hide_section',
    	   'section'   => 'homepage_section',
    	   'label'     => __('Check this to hide section.','movers-lite'),
    	   'type'      => 'checkbox'
     ));
	 
// Contact Section

	$wp_customize->add_section(
        'contact_section',
        array(
            'title' => __('Contact Info', 'movers-lite'),
            'priority' => null,
			'description'	=> __('Add your contact info here.','movers-lite'),	
        )
    );	
	
	$wp_customize->add_setting('phone-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('phone-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add phone number here.','movers-lite'),
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('email-txt',array(
			'sanitize_callback'	=> 'sanitize_email'
	));
	
	$wp_customize->add_control('email-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add email address here.','movers-lite'),
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('address-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('address-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add address here.','movers-lite'),
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('street-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('street-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add street name here.','movers-lite'),
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('top-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('top-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add top bar left text here.','movers-lite'),
			'section'	=> 'contact_section'
	));
	
	$wp_customize->add_setting('time-txt',array(
			'sanitize_callback'	=> 'sanitize_text_field'
	));
	
	$wp_customize->add_control('time-txt',array(
			'type'	=> 'text',
			'label'	=> __('Add top bar right text here.','movers-lite'),
			'section'	=> 'contact_section'
	));
	
}
add_action( 'customize_register', 'movers_lite_customize_register' );	

function movers_lite_css(){
		?>
        <style>
				a, 
				.tm_client strong,
				.postmeta a:hover,
				#sidebar ul li a:hover,
				.blog-post h3.entry-title,
				.sitenav ul li a:hover,
				.sitenav ul li:hover > ul li a:hover{
					color:<?php echo esc_html(get_theme_mod('color_scheme','#4d4d4d')); ?>;
				}
				a.blog-more:hover,
				.nav-links .current, 
				.nav-links a:hover,
				#commentform input#submit,
				input.search-submit,
				.nivo-controlNav a.active,
				.blog-date .date,
				a.read-more,
				.copyright-wrapper{
					background-color:<?php echo esc_html(get_theme_mod('color_scheme','#4d4d4d')); ?>;
				}
				.header-top{
					background-color:<?php echo esc_html(get_theme_mod('topbar-color','#1b273d')); ?>;
				}
				.header{
					background-color:<?php echo esc_html(get_theme_mod('headerbg-color','#263247')); ?>;
				}
				#navigation,
				.sitenav ul li ul{
					background-color:<?php echo esc_html(get_theme_mod('navbg-color','#ffb600')); ?>;
				}
				.section-box .sec-left a{
					border:2px solid <?php echo esc_html(get_theme_mod('color_scheme','#4d4d4d')); ?>;
				}
				
		</style>
	<?php }
add_action('wp_head','movers_lite_css');

function movers_lite_customize_preview_js() {
	wp_enqueue_script( 'movers-customize-preview', get_template_directory_uri() . '/js/customize-preview.js', array( 'customize-preview' ), '20141216', true );
}
add_action( 'customize_preview_init', 'movers_lite_customize_preview_js' );