<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
class User_Login extends WP_Widget {
	function __construct() {
		parent::__construct(
			'User_login', // Base ID
			__('User Login Widget', "emerico"), // Name
			array( 'description' => __( 'Shows Login Box', "emerico" ), ) // Args
		);
                if (!is_user_logged_in()) {
                    add_action('init', array($this,'ajax_login_init'));
                }
	}
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $args['before_widget'];
		if ( ! empty( $title ) )
			//echo $args['before_title'] . $title . $args['after_title'];
                echo $this->_cusrrentLoginHtml();
		echo $args['after_widget'];
	}
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'Title Will Not Show In Front End', "emerico" );
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
        public function _cusrrentLoginHtml(){
                $html ='<form id="login">';
                    $html .='<p class="status"></p>';
                    $html .='<input class="field" id="username" type="text" placeholder="User Name" />';
                    $html .='<input class="field" id="password" type="password" placeholder="Password"/>';
                    $html .='<input class="btngo" type="submit" value="GO" />';
                    $html .= wp_nonce_field( 'ajax-login-nonce', 'security' );
                $html .='</form>';
                return $html;
        }
        public function _student_login($username){
            global $current_user;
            // log in automatically
            if ( !is_user_logged_in() ) {
                $user = get_userdatabylogin( $username );
                $user_id = $user->ID;
                wp_set_current_user( $user_id, $user_login );
                wp_set_auth_cookie( $user_id );
                do_action( 'wp_login', $user_login );
            }
        }
        function ajax_login_init(){
            // Enable the user with no privileges to run ajax_login() in AJAX
            add_action( 'wp_ajax_nopriv_ajaxlogin', array($this,'ajax_login') );
        }
        function ajax_login(){
            // First check the nonce, if it fails the function will break
            check_ajax_referer( 'ajax-login-nonce', 'security' );
            // Nonce is checked, get the POST data and sign user on
            $info = array();
            $info['user_login'] = $_POST['username'];
            $info['user_password'] = $_POST['password'];
            $info['remember'] = true;
            $user_signon = wp_signon( $info, false );
            if ( is_wp_error($user_signon) ){
                echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.')));
            } else {
                echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...')));
            }
            die();
        }
} // class User_Login