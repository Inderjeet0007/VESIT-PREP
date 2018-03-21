<?php
/**
 * Plugin Name: WP Courses
 * Description: A robust, free learning management system (LMS) plugin for WordPress which allows you to create as many free courses as you'd like.  Integrates with any theme.
 * Version: 1.1.2
 * Author: Myles English
 * Plugin URI: http://wpcoursesplugin.com
 * Author URI: http://mylesenglish.com
 * License: GPL2
 */
defined('ABSPATH') or die("No script kiddies please!");
include 'class-wp-courses.php';
include 'class-wpc-admin.php';
include 'lesson-meta.php';
include 'course-meta.php';
include 'admin-menu.php';
include 'columns-wp-courses.php';
include 'shortcodes.php';
function wpc_enqueue_scripts(){
    wp_enqueue_style( 'wpc-style', plugins_url('css/style.css',  __FILE__ ) );
    wp_enqueue_script('wpc-script', plugins_url('js/wp-courses-js.js',  __FILE__ ), 'jQuery', null, true);
    wp_enqueue_style( 'font-awesome-icons', plugins_url( 'css/font-awesome.min.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'wpc_enqueue_scripts' );
function wpc_enqueue_admin_scripts(){
    wp_enqueue_style( 'chosen-style', plugins_url('css/chosen.min.css',  __FILE__ ) );
    wp_enqueue_script('chosen-script', plugins_url('js/chosen.jquery.min.js',  __FILE__ ), 'jQuery', null, true);
    wp_enqueue_style( 'wpc-style', plugins_url('css/admin-css.css',  __FILE__ ) );
    wp_enqueue_script( 'wpc-admin-script-js', plugins_url('js/wp-courses-admin-js.js', __FILE__));
    wp_enqueue_script('jquery-ui', plugins_url('js/jquery-ui.min.js', __FILE__), 'jquery');;
}
add_action( 'admin_enqueue_scripts', 'wpc_enqueue_admin_scripts' );
// Register Custom Post Type Course
function wpc_register_course_cp(){
  $labels = array(
    'name'               => _x( 'Courses', 'post type general name'),
    'singular_name'      => _x( 'Course', 'post type singular name'),
    'add_new'            => _x( 'Add New', 'Course'),
    'add_new_item'       => __( 'Add New Course'),
    'edit_item'          => __( 'Edit Course' ),
    'new_item'           => __( 'New Course' ),
    'all_items'          => __( 'All Courses' ),
    'view_item'          => __( 'View Course' ),
    'search_items'       => __( 'Search Courses' ),
    'not_found'          => __( 'No Courses Found' ),
    'not_found_in_trash' => __( 'No Courses Found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'Courses'
    );
  $args = array(
    'labels'              => $labels,
    'show_in_admin_bar'   => true,
    'menu_icon'           => null,
    'show_in_nav_menus'   => false,
    'publicly_queryable'  => true,
    'exclude_from_search' => true,
    'has_archive'         => false,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => true,
    'show_in_menu'        => '',
    'description'         => 'Enter a Course Description Here',
    'public'              => true,
    'show_ui'             => true,
    'hierarchical'        => false,
    'supports'            => array('title', 'editor', 'excerpt', 'page-attributes', 'thumbnail'),
    'has_archive'         => true,
    'hierarchical'        => false,
    ); 
    register_post_type('course', $args);
    flush_rewrite_rules(false);
}
add_action('init', 'wpc_register_course_cp');
//Custom Messages for Custom Post Type Course
function wpc_course_messages( $messages ) {
    global $post;
    $post_ID = get_the_id();
    $messages['course'] = array(
        0 => '', 
        1 => sprintf( __('Course updated. <a href="%s">View Course</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Course updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Course restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Course published. <a href="%s">View Course</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Course saved.'),
        8 => sprintf( __('Course submitted. <a target="_blank" href="%s">Preview Course</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Course scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Course</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Course draft updated. <a target="_blank" href="%s">Preview Course</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
  return $messages;
}
add_filter( 'post_updated_messages', 'wpc_course_messages' );
// Register Custom Post Type Lesson
function wpc_register_lesson_cp(){
  $labels = array(
    'name'               => _x( 'Lesson', 'post type general name' ),
    'singular_name'      => _x( 'Lesson', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'Lesson' ),
    'add_new_item'       => __( 'Add New Lesson' ),
    'edit_item'          => __( 'Edit Lesson' ),
    'new_item'           => __( 'New Lesson' ),
    'all_items'          => __( 'All Lessons' ),
    'view_item'          => __( 'View Lesson' ),
    'search_items'       => __( 'Search Lessons' ),
    'not_found'          => __( 'No Lessons found' ),
    'not_found_in_trash' => __( 'No lessons found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'Lessons'
  );
  $args = array(
    'show_in_admin_bar'   => true,
    'menu_icon'           => null,
    'show_in_nav_menus'   => false,
    'publicly_queryable'  => true,
    'exclude_from_search' => true,
    'has_archive'         => false,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => true,
    'show_in_menu'        => '',
    'has_archive'         => true,
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'hierarchical'        => false,
    'labels'        => $labels,
    'description'   => 'Enter a lesson description here.',
    'supports'      => array( 'title', 'editor', 'excerpt', 'comments', 'revisions', 'author', 'custom-fields', 'page-attributes'),
  );
  register_post_type( 'lesson', $args ); 
  flush_rewrite_rules( false );
}
add_action( 'init', 'wpc_register_lesson_cp' );
//Custom Messages for Custom Post Type Lesson
function wpc_lesson_messages_cp( $messages ) {
    global $post;
    $post_ID = get_the_id();
    $messages['lesson'] = array(
        0 => '', 
        1 => sprintf( __('Lesson updated. <a href="%s">View Lesson</a>'), esc_url( get_permalink($post_ID) ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Lesson updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Lesson restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Lesson published. <a href="%s">View Lesson</a>'), esc_url( get_permalink($post_ID) ) ),
        7 => __('Lesson saved.'),
        8 => sprintf( __('Lesson submitted. <a target="_blank" href="%s">Preview Lesson</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
        9 => sprintf( __('Lesson scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Lesson</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
        10 => sprintf( __('Lesson draft updated. <a target="_blank" href="%s">Preview Lesson</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'wpc_lesson_messages_cp' );
// Register Custom Post Type Teacher
function wpc_register_teacher_cp(){
  $labels = array(
    'name'               => _x( 'Teachers', 'post type general name'),
    'singular_name'      => _x( 'Teacher', 'post type singular name'),
    'add_new'            => _x( 'Add New', 'Teacher'),
    'add_new_item'       => __( 'Add New Teacher'),
    'edit_item'          => __( 'Edit Teacher' ),
    'new_item'           => __( 'New Teacher' ),
    'all_items'          => __( 'All Teachers' ),
    'view_item'          => __( 'View Teacher' ),
    'search_items'       => __( 'Search Teachers' ),
    'not_found'          => __( 'No Teachers Found' ),
    'not_found_in_trash' => __( 'No Teachers Found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'Teachers'
    );
  $args = array(
    'show_in_admin_bar'   => true,
    'menu_icon'           => null,
    'show_in_nav_menus'   => false,
    'publicly_queryable'  => true,
    'exclude_from_search' => true,
    'has_archive'         => false,
    'query_var'           => true,
    'can_export'          => true,
    'rewrite'             => true,
    'show_in_menu'        => '',
    'has_archive'         => true,
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'hierarchical'        => false,
    'labels'                => $labels,
    'description'           => 'Enter a Teacher Description Here',
    'supports'              => array('title', 'editor', 'excerpt', 'page-attributes', 'thumbnail'),
    ); 
    register_post_type('teacher', $args);
    flush_rewrite_rules(false);
}
add_action('init', 'wpc_register_teacher_cp');
//Custom Messages for Custom Post Type Teacher
function wpc_teacher_messages( $messages ) {
    $permalink = get_permalink(get_the_ID());
    $messages['course'] = array(
        0 => '', 
        1 => sprintf( __('Teacher updated. <a href="%s">View Teacher</a>'), esc_url( $permalink ) ),
        2 => __('Custom field updated.'),
        3 => __('Custom field deleted.'),
        4 => __('Teacher updated.'),
        5 => isset($_GET['revision']) ? sprintf( __('Teacher restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6 => sprintf( __('Teacher published. <a href="%s">View Teacher</a>'), esc_url( $permalink ) ),
        7 => __('Teacher saved.'),
        8 => sprintf( __('Teacher submitted. <a target="_blank" href="%s">Preview Teacher</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
        9 => sprintf( __('Teacher scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Teacher</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( get_the_ID() ) ), esc_url( $permalink ) ),
        10 => sprintf( __('Teacher draft updated. <a target="_blank" href="%s">Preview Teacher</a>'), esc_url( add_query_arg( 'preview', 'true', $permalink ) )),  
    );
    return $messages;
}
add_filter( 'post_updated_messages', 'wpc_teacher_messages' );
// Register Custom Taxonomy "lesson-difficulty"
add_action( 'init', 'wpc_lesson_difficulty_args', 0 );
// Customize taxonomy
function wpc_lesson_difficulty_args() {
  $labels = array(
    'name'              => _x( 'Difficulty', 'taxonomy general name' ),
    'singular_name'     => _x( 'Difficulty', 'taxonomy name' ),
    'search_items'      => __( 'Search Difficulty' ),
    'all_items'         => __( 'All Difficulty' ),
    'parent_item'       => __( 'Parent Term Category' ),
    'parent_item_colon' => __( 'Parent Term Category:' ),
    'edit_item'         => __( 'Edit Difficulty' ), 
    'update_item'       => __( 'Update Difficulty' ),
    'add_new_item'      => __( 'Add New Difficulty' ),
    'new_item_name'     => __( 'New Difficulty' ),
    'menu_name'         => __( 'Difficulty' ),
  );
  $args = array(
    'labels' => $labels,
    'hierarchical' => false,
    'show_admin_column' => true,
    'query_var' => true
  );
  register_taxonomy( 'course-difficulty', 'course', $args );
}
// Register Custom Taxonomy "category"
add_action( 'init', 'wpc_course_category_args', 0 );
// Customize taxonomy
function wpc_course_category_args() {
  $labels = array(
    'name'              => _x( 'Category', 'taxonomy general name' ),
    'singular_name'     => _x( 'Category', 'taxonomy name' ),
    'search_items'      => __( 'Search Category' ),
    'all_items'         => __( 'All Category' ),
    'parent_item'       => __( 'Parent Term Category' ),
    'parent_item_colon' => __( 'Parent Term Category:' ),
    'edit_item'         => __( 'Edit Category' ), 
    'update_item'       => __( 'Update Category' ),
    'add_new_item'      => __( 'Add New Category' ),
    'new_item_name'     => __( 'New Category' ),
    'menu_name'         => __( 'Category' ),
  );
  $args = array(
    'labels' => $labels,
    'hierarchical' => false,
    'show_admin_column' => true,
    'query_var' => true
  );
  register_taxonomy( 'course-category', 'course', $args );
}
/*
** Display logic for CP types
*/
// remove prev and next post links
add_filter( 'previous_post_link', 'wpc_remove_link' );
add_filter( 'next_post_link', 'wpc_remove_link' );
function wpc_remove_link( ) {
    $post_type = get_post_type();
    if($post_type == 'course' || $post_type == 'lesson'){
        return false;
    }
}
add_action('wp_head', 'wpc_toolbar');
function wpc_toolbar(){
    $pt = get_post_type();
    if($pt == 'lesson' || $pt == 'course') {
        $wpc_tools = new WPC_Tools();
        echo $wpc_tools->get_toolbar();
    }
}
add_filter( 'the_content', 'wpc_prepend_meta_to_content', 1 );
function wpc_prepend_meta_to_content($content){
    $post_type = get_post_type();
    if( $post_type == 'lesson' && is_single() ){
        $lesson_id = get_the_ID();
        $course_id = get_post_meta($lesson_id, 'wpc-connected-lesson-to-course', true);
        // init tracking
        $tracking = new WPC_Tracking();
        $tracking->lesson_tracking( $lesson_id );
        $tracking->course_tracking();
        $wp_lessons = new WPC_Lessons();
        $lesson_nav = $wp_lessons->get_lesson_navigation($course_id);
        $video = $wp_lessons->get_lesson_video($lesson_id, false);
        $restriction = get_post_meta( $lesson_id, 'wpc-lesson-restriction', true );
        $style = empty($content) ? 'style="border:0;padding:0;margin:0;"' : '';
        if($restriction == 'free-account' && !is_user_logged_in()){
            $lesson_content = '<p class="wpc-content-restricted wpc-free-account-required"><a href="' . wp_login_url( get_permalink() ) . '">Log in</a> or <a href="' . wp_registration_url() . '">Register</a> to view this lesson.</p>' . '<div id="lesson-nav-wrapper">' . $lesson_nav . '</div>';
        } else {
            $lesson_content = '<div id="video-wrapper">' . $video . '</div>' . '<div id="lesson-nav-wrapper">' . $lesson_nav . '</div>' . '<div class="wpc-lesson-content" ' . $style . '>' . $content . '</div>';
        }
        if(has_filter( 'wpc_all_lesson_content' )){
            $lesson_content = apply_filters( 'wpc_all_lesson_content', $lesson_content );
        }
        return $lesson_content;
    } elseif( $post_type == 'course' && is_single( ) ){
        $data = '';
        $wp_courses = new WPC_Courses();
        $course_id = get_the_ID();
        $course_video = get_post_meta($course_id, 'course-video', true);
        if(strpos($course_video, 'iframe') === false && !empty($course_video)){
            if(preg_match("/[a-z]/i", $course_video) || preg_match("/[A-Z]/i", $course_video)){
                $course_video = '<iframe id="video-iframe" width="560" height="315" src="https://www.youtube.com/embed/' . $course_video . '" frameborder="0" allowfullscreen></iframe>';
            } else {
                $course_video = '<iframe id="video-iframe" src="https://player.vimeo.com/video/' . $course_video . '" width="500" height="216" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
            }
        }
        if(!empty($course_video)){
            $data .= $course_video;
        }
        $teacher_id = get_post_meta( $course_id, 'wpc-connected-teacher-to-course', true );
        $data .= '<div class="course-meta-wrapper">';
            $data .= $wp_courses->get_start_course_button($course_id);
            $data .= '<div class="cm-item">';
                $data .= '<span>Level: ' . $wp_courses->get_course_difficulty($course_id) . '</span>';
            $data .= '</div>';
            $data .= '<div class="cm-item teacher-meta-wrapper">';
                $data .= '<span>Teacher: ' . get_the_title($teacher_id) . '</span>';
            $data .= '</div>';
            if(is_user_logged_in()){
                $data .= '<div class="cm-item">';
                    $data .= '<span>Progress: ' . $wp_courses->get_percent_viewed($course_id) . '%</span>';
                $data .= '</div>';
            }
        $data .= '</div>';
        $data = $data . $content;
        if(has_filter( 'wpc_single_course_content' )){
            $data = apply_filters( 'wpc_single_course_content', $data );
        }
        return $data;
    } else {
        return $content;
    }
}
