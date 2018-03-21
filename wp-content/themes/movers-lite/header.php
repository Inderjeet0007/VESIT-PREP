 <?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Movers Lite
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php if(get_theme_mod('top-txt') && get_theme_mod('time-txt') != '') { ?>
<div class="header-top">
  <div class="container">
     <div class="left">
     	<?php if(get_theme_mod('top-txt') != '') { ?>
     		<span><?php echo esc_html(get_theme_mod('top-txt')); ?></span>
        <?php } ?>
     </div>
     <div class="right">
     		<?php if(get_theme_mod('time-txt') != '') { ?>
     			<span class="hours"><i class="fa fa-clock-o"></i><?php echo esc_html(get_theme_mod('time-txt')); ?></span>
            <?php } ?>
     </div>
     <div class="clear"></div>
  </div>
</div><!--end header-top--> 
<?php } ?>

<div class="header">
	<div class="header-inner">
      <div class="logo">
       <?php movers_lite_the_custom_logo(); ?>
						<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_attr(bloginfo( 'name' )); ?></a></h1>

					<?php $description = get_bloginfo( 'description', 'display' );
					if ( $description || is_customize_preview() ) : ?>
						<p><?php echo esc_attr($description); ?></p>
					<?php endif; ?>
    </div><!-- .logo -->                 
    <div class="header_right"> 
    	<div class="right-box last">  
        	<?php if(get_theme_mod('address-txt') && (get_theme_mod('street-txt') != '')) { ?>       	
            	<i class="fa fa-map-marker"></i>  
             <?php } ?>          
            <div class="bx-text">
            	 <?php if(get_theme_mod('address-txt') != '') { ?>
            		<h5><?php echo esc_html(get_theme_mod('address-txt')); ?></h5>
                <?php } ?>
                <?php if(get_theme_mod('street-txt') != '') { ?>
            	<span><?php echo esc_html(get_theme_mod('street-txt')); ?></span>
                <?php } ?>
            </div><!-- bx-text --><div class="clear"></div>
        </div><!-- right-box --> 
    	<div class="right-box">    
        	<?php if(get_theme_mod('phone-txt') && (get_theme_mod('email-txt') != '')) { ?>    	
            	<i class="fa fa-phone"></i>   
                <?php } ?>        
            <div class="bx-text">
            	<?php if(get_theme_mod('phone-txt') != '') { ?>
                	<h5><?php echo esc_html(get_theme_mod('phone-txt')); ?></h5>
                <?php } ?>
                <?php if(get_theme_mod('email-txt') != '') { ?>
                <span><a href="<?php echo esc_url('mailto:'.get_theme_mod('email-txt')); ?>"><?php echo esc_html(get_theme_mod('email-txt')); ?></a></span>
                <?php } ?>
            </div><!-- bx-text --><div class="clear"></div>
        </div><!-- right-box -->
        
        
    </div><!--header_right-->    
 <div class="clear"></div>
</div><!-- .header-inner-->
</div><!-- .header -->

<div id="navigation">
	<div class="container">
    	<div class="toggle">
            <a class="toggleMenu" href="#">
                <?php esc_attr_e('Menu','movers-lite'); ?>                
            </a>
    	</div><!-- toggle -->    
    <div class="sitenav">                   
   	 	<?php wp_nav_menu( array('theme_location' => 'primary') ); ?>   
    </div><!--.sitenav -->
    <div class="clear"></div>
    </div><!-- container -->    
</div><!-- navigation -->