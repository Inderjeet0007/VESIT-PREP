<?php
function wpc_remove_category_meta_box() {
    remove_meta_box( 'tagsdiv-course-category', 'course', 'side' );
}
add_action('add_meta_boxes', 'wpc_remove_category_meta_box');
function wpc_remove_difficulty_meta_box() {
    remove_meta_box( 'tagsdiv-course-difficulty', 'course', 'side' );
}
add_action('add_meta_boxes', 'wpc_remove_difficulty_meta_box');
function wpc_add_course_meta_box() {
	$screens = array( 'course' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'wpc_sectionid',
			__( 'Course Video', 'wpc_textdomain' ),
			'wpc_course_meta_box_callback',
			$screen,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'wpc_add_course_meta_box' );
/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function wpc_course_meta_box_callback( $post ) {
	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'wpc_save_meta_box_data', 'wpc_meta_box_nonce' );
	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, 'course-video', true ); ?>
	<label for="wpc_new_field"><?php _e( 'Video Embed Code (iframe)', 'wpc_textdomain' ); ?></label>
	<textarea style="width:100%;" id="wpc_video_id" name="wpc_video_id"><?php echo $value; ?></textarea>
	<br><label>Course Teacher:</label><br>
	<?php
	global $post;
	$post_old = $post;
	$lesson_id = get_the_ID();
	$args = array(
		'post_type'		=> 'teacher',
		'orderby'		=> 'title',
		'post_status'		=> 'publish',
	);
	$query = new WP_Query($args);
	echo '<select id="teacher-select" name="teacher-selection">';
	echo '<option value="-1">Select Teacher</option>';
	while($query->have_posts()){
		$query->the_post();
		$teacher_id = get_the_ID();
		$course_id = get_post_meta($lesson_id, 'wpc-connected-teacher-to-course', true);
		if( $course_id == $teacher_id){
			$selected = 'selected="selected"';
		} else{
			$selected = '';
		}
		echo '<option value="' . $teacher_id . '"' . $selected . '>' . get_the_title() . '</option>';
	}
	wp_reset_postdata();
	echo '</select>';
	$admin_url = admin_url();
	echo ' <a href="' . $admin_url . 'post-new.php?post_type=teacher" class="page-title-action add-new">Add New</a>';
	// fixes strange issue with wrong slug being used
	$post = $post_old;
  	setup_postdata( $post );
  	// Course Category Dropdown
  	$tax_name = 'course-category';
    $taxonomy = get_taxonomy($tax_name);
	?>
	<br><label>Course Category:</label>
	<div class="tagsdiv" id="<?php echo $tax_name; ?>">
	    <div class="jaxtag">
	    <?php 
	    // Use nonce for verification
	    wp_nonce_field( plugin_basename( __FILE__ ), 'course-category_noncename' );
	    $type_IDs = wp_get_object_terms( $post->ID, 'course-category', array('fields' => 'ids') );
	    if(!empty($type_IDs[0])){
	    	$id = $type_IDs[0];
	    } else {
	    	$id = '';
	    }
	    wp_dropdown_categories('taxonomy=course-category&hide_empty=0&orderby=name&name=course-category&show_option_none=Select Category&selected='. $id); ?>
	    <?php echo ' <a href="' . $admin_url . 'edit-tags.php?taxonomy=course-category&post_type=course" class="page-title-action add-new">Add New</a>'; ?>
	    </div>
	</div>
	<?php 
	$tax_name = 'course-difficulty';
    $taxonomy = get_taxonomy($tax_name);
	?>
	<label>Course Difficulty:</label>
	<div class="tagsdiv" id="<?php echo $tax_name; ?>">
	    <div class="jaxtag">
	    <?php 
	    // Use nonce for verification
	    wp_nonce_field( plugin_basename( __FILE__ ), 'course-difficulty_noncename' );
	    $type_IDs = wp_get_object_terms( $post->ID, 'course-difficulty', array('fields' => 'ids') );
	    if(!empty($type_IDs[0])){
	    	$id = $type_IDs[0];
	    } else {
	    	$id = '';
	    }
	    wp_dropdown_categories('taxonomy=course-difficulty&hide_empty=0&orderby=name&name=course-difficulty&show_option_none=Select Difficulty&selected='. $id); ?>
	   	<?php echo ' <a href="' . $admin_url . 'edit-tags.php?taxonomy=course-difficulty&post_type=course" class="page-title-action add-new">Add New</a>'; ?>
	    </div>
	</div>
<?php }
/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wpc_course_save_meta_box_data( $post_id ) {
	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */
	// Check if our nonce is set.
	if ( ! isset( $_POST['wpc_meta_box_nonce'] ) ) {
		return;
	}
	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['wpc_meta_box_nonce'], 'wpc_save_meta_box_data' ) ) {
		return;
	}
	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'course' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	// Sanitize user input.
	if(isset($_POST['teacher-selection'])){
		$teacher = sanitize_text_field( $_POST['teacher-selection'] );
		update_post_meta( $post_id, 'wpc-connected-teacher-to-course', $teacher);
	}
	if(isset($_POST['wpc_video_id'])){
		$my_data = strip_tags($_POST['wpc_video_id'], '<iframe>');
		update_post_meta( $post_id, 'course-video', $my_data );
	}
	if(isset($_POST['course-category'])){
		$type_ID = sanitize_text_field($_POST['course-category']);
	  	$type = ( $type_ID > 0 ) ? get_term( $type_ID, 'course-category' )->slug : NULL;
	  	wp_set_object_terms(  $post_id , $type, 'course-category' );
	}
	if(isset($_POST['course-difficulty'])){
		$type_ID = sanitize_text_field($_POST['course-difficulty']);
	  	$type = ( $type_ID > 0 ) ? get_term( $type_ID, 'course-difficulty' )->slug : NULL;
	  	wp_set_object_terms(  $post_id , $type, 'course-difficulty' );
	}
}
add_action( 'save_post', 'wpc_course_save_meta_box_data' );