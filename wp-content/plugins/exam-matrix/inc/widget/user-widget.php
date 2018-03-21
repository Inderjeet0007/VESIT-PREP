<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
class User_Widget extends WP_Widget {
	function __construct() {
		parent::__construct(
			'User_widget', // Base ID
			__('User Profile', "emerico"), // Name
			array( 'description' => __( 'Shows User Options', "emerico" ), ) // Args
		);
	}
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			//echo $args['before_title'] . $title . $args['after_title'];
                echo $this->_cusrrentUserHtml();
		echo $args['after_widget'];
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', "emerico" );
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php 
	}
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		return $instance;
	}
        function get_avatar_url($get_avatar){
            preg_match("/src='(.*?)'/i", $get_avatar, $matches);
            return $matches[1];
        }
        public function _cusrrentUserHtml(){
            global $current_user;
            get_currentuserinfo();
            if(isset($_REQUEST['action']) && $_REQUEST['action'] != 'upload'){
                $this->addAvatar();
            }
            if ( is_user_logged_in() ) {
                $html = '<div class="user-widget">';
                    $html .= '<div class="profile-pic-container-style" id="pro-profile-pic-container">';
                        $html .= '<div class="profile-pic-style" id="profile-pic">';
                            $html .= get_avatar( $current_user->user_email , 170 );
                        $html .='</div>';
                    $html .= '</div>';
                $html .= '</div>';
                $html .= '<div id="pro-info">';
                $html .= '<div id="pro-info-top">';
                  $html .= '<p class="" id="pro-name">';
                    $html .= '<span id="first-name">'.$current_user->user_firstname.' '.$current_user->user_lastname.'</span>';
                  $html .= '</p>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '<div id="pro-info-bottom">';
                  $html .= '<p class="pro-info-detail" id="location">'.get_user_meta($current_user->ID, '_address', true).'</p>';
                $html .= '</div>';
              $html .= '</div>';
              $html .= $this->getTestLinks();
          } else {
              $html = '<div class="user_registration_form" style="display:block;">';
              		$html .= '<div class="form-wrapper">';
                            $html .= '<h3> Sign Up !! </h3>';
                            $html .= '<div id="error-message"></div>';
                            $html .= '<form method="post" enctype="multipart/form-data" name="st-register-form">';
                                $html .= '<div class="form-label"><label for="st-fname">First Name</label></div>';
                                $html .= '<div class="field"><input type="text" autocomplete="off" name="fname" id="st-fname" /></div>';
                                $html .= '<div class="form-label"><label for="st-lname">Last Name</label></div>';
                                $html .= '<div class="field"><input type="text" autocomplete="off" name="lname" id="st-lname" /></div>';
                                $html .= '<div class="form-label"><label for="st-address">Address</label></div>';
                                $html .= '<div class="field"><input type="text" autocomplete="off" name="address" id="st-address" /></div>';
                                $html .= '<div class="form-label"><label for="st-username">User Name</label></div>';
                                $html .= '<div class="field"><input type="text" autocomplete="off" name="username" id="st-username" /></div>';
                                $html .= '<div class="form-label"><label for="st-email">Email ID</label></div>';
                                $html .= '<div class="field"><input type="text" autocomplete="off" name="mail" id="st-email" /></div>';
                                $html .= '<div class="form-label"><label for="st-psw">Password</label></div>';
                                $html .= '<div class="field"><input type="password" name="password" id="st-psw" /></div>';
                               $html .= '<div class="frm-button"><input class="eme_btn btn_signup" type="button" id="register-me" value="Register Me" /></div>';
                            $html .= '</form>';
                        $html .= '</div>';
              $html .='</div>';
              $html .='<div class="avtar_upload" style="display:none;">';
                 $html .= '<div class="profile-pic-container-style" id="pro-profile-pic-container">';
                      $html .= '<div class="profile-pic-style" id="profile-pic" style="background-image: url("">';
                          $html .= '<img src="'.plugins_url( '/exam-matrix/images/noimage.png').'" alt="No Image" />';
                      $html .='</div>';
                  $html .= '</div>';
                  $html .='<form method="POST" enctype="multipart/form-data" action="?action=upload">';
                    $html .='<input type="file" name="user_avatar" />';
                    $html .='<input type="hidden" name="action" value="wp_handle_upload" />';
                    $act = "javascript:location.href='".site_url()."';";
                    $html .='<input type="button" class="eme_btn_de btnSkip" onclick="'.$act.'" value="Skip" />';
                    $html .='<input type="submit" class="eme_btn btnUpload" value="Upload" />';
                  $html .='</form>';
              $html .='</div>';
          }
            return $html;
        }
        function addAvatar(){
            if(is_user_logged_in() && isset($_FILES['user_avatar'])){
            require_once ABSPATH.'wp-admin/includes/file.php';
            $current_user_id = get_current_user_id();
            $allowed_image_types = array(
              'jpg|jpeg|jpe' => 'image/jpeg',
              'png'          => 'image/png',
              'gif'          => 'image/gif',
            );
            $status = wp_handle_upload($_FILES['user_avatar'], array('mimes' => $allowed_image_types));
            if(empty($status['error'])){
              $resized = image_resize($status['file'], 170, 170, $crop = true);
              if(is_wp_error($resized))
                wp_die($resized->get_error_message());
              $uploads = wp_upload_dir();
              $resized_url = $uploads['url'].'/'.basename($resized);
              update_user_meta($current_user_id, 'custom_avatar', $resized_url);
            }else{
              wp_die(sprintf(__('Upload Error: %s'), $status['error']));
            }
          }
        }
        function getTestLinks(){
            global $post;
            $html = '<div class="menu_test"><ul>';
            query_posts( 'post_type=ex_test&order=DSC&posts_per_page=5' );
            if (have_posts()) :
                while (have_posts()) : the_post();
                    $html .='<li><a href="'.get_permalink(get_the_ID()).'">'.get_the_title().'</a></li>';
                endwhile;
            endif;
            $html .= '</ul></div>';
            wp_reset_query();
            return $html;
        }

} // class User_Widget