<?php
/*
Plugin Name: Exam Matrix
Plugin URI: Not Available
Description: Emerico Web Solution
Author: Udit Rawat
Version: 1.5
Author URI: http://udi.trawat.zxq
*/
/**
* Description of exam-matrix
 *
 * @package Exam Matrix
 * @version 1.5
 * @author Udit Rawat
 */

namespace ExamMatrix;
session_start();
class examMatrix {
    private $UserRegID;
    // Constructor
    function __construct() {
        register_activation_hook( __FILE__, array($this,'_installDB') );
        add_action( 'init', array($this, '_includeLib'), 0 );
        add_action( 'init', array($this, '_addCustomPost'), 0 );
        add_action('save_post', array($this, '_saveMetaBox'), 1, 2); // save the custom fields
        add_action( 'admin_enqueue_scripts', array($this,'_addAdminScripts'));
        add_action( 'wp_enqueue_scripts', array($this, '_addTemplateScripts') );
        add_action( 'admin_menu', array($this,'_addAdminPage'));
        add_filter('get_avatar', array($this,'_custom_avatars'), 10, 3);
        add_filter('the_content', array($this,'_startTest'),1);
        add_filter( 'template_include', array($this, '_includeTemplate'), 1 );
        add_action( 'widgets_init', array($this,'_register_user_widget' ),1);
        add_action( 'widgets_init', array($this,'_register_user_login' ),1);
        add_action('wp_head',array($this,'_ajaxurl'));
        add_action( 'wp_ajax_register_action',array($this,'_handle_registration'));
        add_action( 'wp_ajax_nopriv_register_action',array($this,'_handle_registration'));
        add_action( 'wp_ajax_saveoption', array($this,'_saveOption') );
        add_action( 'wp_ajax_nopriv_saveoption', array($this,'_saveOption') );
    }
    // include library 
    function _includeLib(){
        require_once(plugin_dir_path( __FILE__ ).'lib/php-formhelper/php-form-helper.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/csv.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/Database.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/Test.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/Result.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/DigitalClock.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/widget/user-widget.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/widget/user-login.php');
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/ImportExport.php');
    }
    function _installDB(){
        require_once(plugin_dir_path( __FILE__ ).'inc/classes/InstallDb.php');
        $tables = new InstallDb();
    }
    // content hook
    function _startTest($content){
        global $post;
        if ( $post->post_type == 'ex_test') {
            $content .= '<p class="startTest"> <a class="eme_btn" href="?testId='.get_post_meta($post->ID,'_eme_selected_set',true).'">Start Test</a> </p>';
        }
        return $content;
    }
    // including template
    function _includeTemplate($template_path){
        global $post;
        if ( get_post_type() == 'ex_test' ) {
	        if ( is_single() ) {
	            // checks if the file exists in the theme first,
	            // otherwise serve the file from the plugin
	            if ( $theme_file = locate_template( array ( 'single-ex_test.php' ) ) ) {
	                $template_path = $theme_file;
	            } else {
	                $template_path = plugin_dir_path( __FILE__ ) . '/templates/single-ex_test.php';
	            }
	        }
	    }
	    return $template_path;
    }
    // WP Admin Script
    function _addAdminScripts($hook) {
        wp_register_style( 'exam_engine_admin_css', plugin_dir_url( __FILE__ ) . '/css/exam-engine-admin-style.css', false, '1.0.0' );
        wp_enqueue_style( 'exam_engine_admin_css' );
        //if( get_post_type() != 'ex_test' )
        //    return;
        wp_enqueue_script( 'exam_engine_admin_script', plugin_dir_url( __FILE__ ) . '/js/exam-engine-admin.js' );
    }
    // template scripts
    function _addTemplateScripts($hook){
        global $post;
        if ( is_single() && get_post_type() == 'ex_test' ) {
            wp_enqueue_style(
                'bx-slider-css',
                plugins_url( '/css/jquery.bxslider.css' , __FILE__ )
            );
            wp_enqueue_script( 'bx-slider-js', plugin_dir_url( __FILE__ ) . '/js/jquery.bxslider.js',array( 'jquery' ) );
        }
        wp_enqueue_style(
            'template-css',
            plugins_url( '/css/template-css.css' , __FILE__ )
        );
        wp_enqueue_script( 'exam_engine_admin_script', plugin_dir_url( __FILE__ ) . '/js/template-js.js',array( 'jquery' ) );
    }
    // adding admin ajax
    function _ajaxurl(){
        echo '<script> var ajaxurl = "'.admin_url('admin-ajax.php') .'"</script>';
    }
    // Custom Post Type
    function _addCustomPost(){
            $labels = array(
                'name'                => _x( 'Test', 'Post Type General Name', 'emerico' ),
                'singular_name'       => _x( 'Test', 'Post Type Singular Name', 'emerico' ),
                'menu_name'           => __( 'Tests', 'emerico' ),
                'parent_item_colon'   => __( 'Parent Test', 'emerico' ),
                'all_items'           => __( 'All Test', 'emerico' ),
                'view_item'           => __( 'View Test', 'emerico' ),
                'add_new_item'        => __( 'Add New Test', 'emerico' ),
                'add_new'             => __( 'New Test', 'emerico' ),
                'edit_item'           => __( 'Edit Test', 'emerico' ),
                'update_item'         => __( 'Update Test', 'emerico' ),
                'search_items'        => __( 'Search Tests', 'emerico' ),
                'not_found'           => __( 'No Tests found', 'emerico' ),
                'not_found_in_trash'  => __( 'No Tests found in Trash', 'emerico' ),
            );

            $capabilities = array(
                    'edit_post'           => 'edit_post',
                    'read_post'           => 'read_post',
                    'delete_post'         => 'delete_post',
                    'edit_posts'          => 'edit_posts',
                    'edit_others_posts'   => 'edit_others_posts',
                    'publish_posts'       => 'publish_posts',
                    'read_private_posts'  => 'read_private_posts',
            );

            $args = array(
                    'label'               => __( 'ex_test', 'emerico' ),
                    'description'         => __( 'Geo Test', 'emerico' ),
                    'labels'              => $labels,
                    'supports'            => array( 'title', 'editor', 'thumbnail', 'excerpt', 'comments', 'revisions'),
                    'hierarchical'        => false,
                    'public'              => true,
                    'show_ui'             => true,
                    'show_in_menu'        => true,
                    'show_in_nav_menus'   => true,
                    'show_in_admin_bar'   => true,
                    'menu_position'       => 4,
                    'menu_icon'           => plugins_url( 'images/exam.png' , __FILE__ ),
                    'can_export'          => true,
                    'has_archive'         => 'vacation-test',
                    'exclude_from_search' => false,
                    'publicly_queryable'  => true,
                    //'rewrite'             => $rewrite,
                    //'capabilities'        => $capabilities,
                    'register_meta_box_cb' => array($this, '_addMetaBox')
            );
            register_post_type( 'ex_test', $args );
            // adding category 
            $labels = array(
			'name'                       => _x( 'Category', 'Taxonomy General Name', 'emerico' ),
			'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'emerico' ),
			'menu_name'                  => __( 'Category', 'emerico' ),
			'all_items'                  => __( 'All Category', 'emerico' ),
			'parent_item'                => __( 'Parent Category', 'emerico' ),
			'parent_item_colon'          => __( 'Parent Category:', 'emerico' ),
			'new_item_name'              => __( 'New Category Name', 'emerico' ),
			'add_new_item'               => __( 'Add New Category', 'emerico' ),
			'edit_item'                  => __( 'Edit Category', 'emerico' ),
			'update_item'                => __( 'Update Category', 'emerico' ),
			'separate_items_with_commas' => __( 'Separate Category with commas', 'emerico' ),
			'search_items'               => __( 'Search Category', 'emerico' ),
			'add_or_remove_items'        => __( 'Add or remove Category', 'emerico' ),
			'choose_from_most_used'      => __( 'Choose from the most used Category', 'emerico' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
		);
		register_taxonomy( 'ex_categories', 'ex_test', $args );
    }
    // Metabox Operation
    function _addMetaBox(){
        add_meta_box('ex_test_question', 'Test Settings', array($this, '_testSettings'), 'ex_test', 'normal', 'high');        
    }
    function _testSettings(){
        global $post;
        $dba = new Database();
        // Noncename needed to verify where the data originated
        echo '<input type="hidden" name="_eme_noncename" id="_eme_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

        // Echo out the field
        $form_options = array 	(
                '_eme_estimated_time'	=> array('type' => 'text', 'required' => true, 'label' => 'Time', 'max' => 10),
                '_eme_total_marks'		=> array('type' => 'text', 'required' => true, 'label' => 'Total Marks', 'max' => 10),
                '_eme_marks_per_question'=> array('type' => 'text', 'required' => true, 'label' => 'Marks Per Question', 'max' => 10),
        );

        try {
            $form = new \PHPFormHelper($form_options, false);
        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
            exit;
        }

        $data = array(
                        '_eme_estimated_time' => get_post_meta($post->ID, '_eme_estimated_time', true),
                        '_eme_total_marks' => get_post_meta($post->ID, '_eme_total_marks', true),
                        '_eme_marks_per_question' => get_post_meta($post->ID, '_eme_marks_per_question', true),
                        '_eme_show_random' => get_post_meta($post->ID, '_eme_show_random', true),
                        '_eme_negative_marking' => get_post_meta($post->ID, '_eme_negative_marking', true),
                        '_eme_selected_set' => get_post_meta($post->ID, '_eme_selected_set', true),
                );

        $form->display($data);
        require_once('inc/pages/setting-meta.php');
    }
    function _saveMetaBox($post_id,$post){
        if ( isset($_POST['_eme_noncename']) && !wp_verify_nonce( $_POST['_eme_noncename'], plugin_basename(__FILE__) )) {
            return $post->ID;
           }
           // Is the user allowed to edit the post or page?
        if ( !current_user_can( 'edit_post', $post->ID ))
            return $post->ID;
        
        $static = array(
                    '_eme_negative_marking' => 'N',
                    '_eme_show_random' => 'N',
                );
        foreach($static as $k => $v){
            if ( substr($k, 0, 5) == '_eme_' ) {
                    update_post_meta($post->ID, $k, $v);
            }
        }
        foreach($_POST as $k => $v) {
            if ( substr($k, 0, 5) == '_eme_' ) {
                    update_post_meta($post->ID, $k, $v);
            }
        }
    }
    // Admin Page
    function _addAdminPage(){
        add_menu_page('Q Set', 'Q Set', 'manage_options', 'ex_qset', array($this,'_addQuesSet'),plugins_url( 'images/exam.png' , __FILE__ ),7);
        add_submenu_page( 'ex_qset', 'Add Question', 'Add Question', 'manage_options', 'ex_add_ques', array($this,'_addQues'));
        add_submenu_page( 'ex_qset', 'Manage Question', 'Manage Question', 'manage_options', 'ex_manage_ques', array($this,'_manageQues'));
        add_submenu_page( 'ex_qset', 'Search Results', 'Search Results', 'manage_options', 'ex_search_results', array($this,'_searchResults'));
        add_submenu_page( 'ex_qset', 'Import Export', 'Import Export', 'manage_options', 'ex_import_export', array($this,'_importExport'));
    }
    function _addQuesSet(){
        $db = new Database();
        require_once(plugin_dir_path( __FILE__ ).'inc/pages/qtest.php');
    }
    function _addQues(){
        $db = new Database();
        require_once(plugin_dir_path( __FILE__ ).'inc/pages/addq.php');
    }
    function _manageQues(){
        $db = new Database();
        require_once(plugin_dir_path( __FILE__ ).'inc/pages/manageq.php');
    }
    function _searchResults(){
        $db = new Database();
        require_once(plugin_dir_path( __FILE__ ).'inc/pages/search-results.php');
    }
    function _importExport(){
        $db = new Database();
        $ie = new ImportExport();
        require_once(plugin_dir_path( __FILE__ ).'inc/pages/import-export.php');
    }
    // register User Widget widget
    function _register_user_widget() {
        register_widget( 'User_Widget' );
    }
    function _register_user_login() {
        register_widget( 'User_Login' );
    }
    // user registration function 
    function _handle_registration(){
        global $wpdb, $table_prefix;
        $tblUser = $table_prefix.'users';
        if( $_POST['action'] == 'register_action' ) {
        $error = '';
         $uname = trim( $_POST['username'] );
         $email = trim( $_POST['mail_id'] );
         $fname = trim( $_POST['firname'] );
         $lname = trim( $_POST['lasname'] );
         $address = trim( $_POST['address'] );
         $pswrd = $_POST['passwrd'];
        if( empty( $_POST['address'] ) )
         $error .= 'Address is empty, ';
        if( empty( $_POST['username'] ) )
         $error .= 'Enter Username,  ';
        if( empty( $_POST['mail_id'] ) )
         $error .= 'Enter Email Id,  ';
         elseif( !filter_var($email, FILTER_VALIDATE_EMAIL) )
         $error .= 'Enter Valid Email,  ';
        if( empty( $_POST['passwrd'] ) )
         $error .= 'Password should not be blank,  ';
        if( empty( $_POST['firname'] ) )
         $error .= 'Enter First Name,  ';
         elseif( !preg_match("/^[a-zA-Z'-]+$/",$fname) )
         $error .= 'Enter Valid First Name,  ';
        if( empty( $_POST['lasname'] ) )
         $error .= 'Enter Last Name,  ';
         elseif( !preg_match("/^[a-zA-Z'-]+$/",$lname) )
         $error .= 'Enter Valid Last Name,  ';
        if( empty( $error ) ){
        $status = wp_create_user( $uname, $pswrd ,$email );
        $uid = $wpdb->get_var("SELECT id FROM $tblUser WHERE user_email='$email'");
        update_user_meta( $uid, 'first_name', $fname );
        update_user_meta( $uid, 'last_name', $lname);
        update_user_meta( $uid, '_address', $address);
        $this->_auto_login( $uname );
        if( is_wp_error($status) ){
        $msg = '';
         foreach( $status->errors as $key=>$val ){
           foreach( $val as $k=>$v ){
           $msg = $v;
         }
         }
        echo $msg;
         }else{
          $msg = 'goToImageUpload';
           echo $msg;
         }
        }
         else{
        echo $error;
         }
         die(1);
         }
      }
   function _auto_login( $user ) {
       global $current_user;
        $username   = $user;
        // log in automatically
        if ( !is_user_logged_in() ) {
            $user = get_userdatabylogin( $username );
            $user_id = $user->ID;
            wp_set_current_user( $user_id, $user_login );
            wp_set_auth_cookie( $user_id );
            do_action( 'wp_login', $user_login );
        }     
    }
    function _custom_avatars($avatar, $id_or_email, $size){
        global $current_user;
        if(is_user_logged_in()){
          $current_user = wp_get_current_user();
          if(get_user_meta($current_user->ID, 'custom_avatar', true)){
            $image_url = get_user_meta($current_user->ID, 'custom_avatar', true);
            if($user_avatar !== false)
              return '<img src="'.$image_url.'" class="avatar photo" width="'.$size.'" height="'.$size.'" alt="'.$current_user->display_name .'" />';
          } else{
              return '<img src="'.plugins_url( 'images/noimage.png' , __FILE__ ).'" class="avatar photo" width="'.$size.'" height="'.$size.'" alt="'.$current_user->display_name .'" />';
          }
        }
        return $avatar;
  }
  public function _saveOption(){
      if(isset($_POST['action']) && $_POST['action'] != ''){
          Test::SaveOption($_POST);
      }
  }
  // External
  public function DigiClock($frmCls){
      $clock = new DigitalClock();
      $clock->_showClock($frmCls);
  }
  public function ETest($testID){
      $test = new Test($testID);
      $this->UserRegID = $test->_getUserRegID();
      $data = $test->_startTest($this->UserRegID,$testID);
      return $data;
  }
  public function GetUserRegID(){
      return $this->UserRegID;
  }
  public function Result($REGID){
      $result = new Result();
      return $result->CalculateResult($REGID);
  }
}
$em = new examMatrix();
?>
