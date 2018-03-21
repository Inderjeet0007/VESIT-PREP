<?php
function wpc_add_meta_box() {
	$screens = array( 'lesson' );
	foreach ( $screens as $screen ) {
		add_meta_box(
			'wpc_sectionid',
			__( 'Lesson Video', 'wpc_textdomain' ),
			'wpc_meta_box_callback',
			$screen,
			'side',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'wpc_add_meta_box' );
/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function wpc_meta_box_callback( $post ) {
	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'wpc_save_meta_box_data', 'wpc_meta_box_nonce' );
	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, 'lesson-video', true );
  	do_action('wpc-before-lesson-meta');
	?>
	<label>Video Embed Code (iframe)</label><br>
	<textarea style="width:100%;" id="wpc_new_field" name="wpc_new_field"><?php echo $value; ?></textarea>
	<br>
	<div id="wpc-lesson-restriction-container" style="position:relative;">
		<div id="wpc-lesson-restriction-overlay">Lesson Restriction Options Disabled if Membership Level(s) Checked</div>
		<label>Lesson Restriction</label>
		<br><br>
		<?php
			$wpc_admin = new WPC_Admin();
			echo $wpc_admin->lesson_restriction_radio_buttons($post->ID, 'wpc-lesson-restriction', '');
		?>
	</div>
	<br><label>Connected Course</label><br>
	<?php
	global $post;
	$post_old = $post;
	echo $wpc_admin->get_course_dropdown('chosen-select');
	echo ' <a href="' . admin_url() . 'post-new.php?post_type=course" class="page-title-action add-new">Add New</a>';
	// fixes issue with wrong slug being used
	$post = $post_old;
  	setup_postdata( $post ); ?>
	  	<?php 
		$sections = get_post_meta($post->ID, 'wpc-num-sections', true); ?>
	<br><br><button class="button info-button" data-content="Click this button to add additional attachments.">i</button>
		<button class="button" id="wpc-add-section">+ Additional Attachment</button><br><br>
		<?php
		if( $sections <= 1 || $sections == ''){
	echo '<div id="wpc-add-media-wrapper" class="wpc-add-media-wrapper">
		  	<button style="padding:3px;" id="wpc-btn-1" class="wpc-media-button button wp-menu-image dashicons-before dashicons-admin-media"></button>
		  	<input id="image-url-wpc-btn-1" type="text" name="wpc-lesson-attachments-1" value="' . get_post_meta( $post->ID, 'wpc-media-sections-1', true ) . '" />
	  	</div>
	 	<input name="wpc-num-sections" id="wpc-num-sections" type="hidden" value="1" />';
		}else{
			$count = 1;
			for($i = 1; $i<=$sections; $i++){
				$section = get_post_meta( $post->ID, 'wpc-media-sections-' . $count, true );
				if(!empty($section)){
		 	echo '<div id="wpc-add-media-wrapper" class="wpc-add-media-wrapper">
			  	<button id="wpc-btn-' . $count . '" class="wpc-media-button button">Attach Media</button>
			  	<input id="image-url-wpc-btn-' . $count . '" type="text" name="wpc-lesson-attachments-' . $count . '" value="' . get_post_meta( $post->ID, 'wpc-media-sections-' . $count, true ) . '" />
		  	</div>';
				}
		  	$count++;
		}
		echo '<input name="wpc-num-sections" id="wpc-num-sections" type="hidden" value="' . $sections . '" />';
		}
		do_action('wpc-after-lesson-meta'); ?>
<?php }
/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function wpc_save_meta_box_data( $post_id ) {
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
	if ( isset( $_POST['post_type'] ) && 'lesson' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}
	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if(isset($_POST['wpc-lesson-restriction'])){
		$restriction = sanitize_text_field( $_POST['wpc-lesson-restriction'] );
		update_post_meta( $post_id, 'wpc-lesson-restriction', $restriction );
	}
	if ( isset( $_POST['wpc_new_field'] ) ) {
		$my_data = strip_tags($_POST['wpc_new_field'], '<iframe>');
		update_post_meta( $post_id, 'lesson-video', $my_data );
	}
	if(isset($_POST['course-selection'])){
		$course_id = sanitize_text_field( $_POST['course-selection'] );
		$wpc_courses = new WPC_Courses();
		$difficulty = $wpc_courses->get_course_difficulty($course_id);
		$difficulty = strip_tags($difficulty);
		update_post_meta( $post_id, 'wpc-connected-lesson-to-course', $course_id);
		update_post_meta($post_id, 'lesson-difficulty', $difficulty);
	}
	
	if( isset( $_POST['wpc-num-sections'] ) ){
		$section_count = $_POST['wpc-num-sections'];
		update_post_meta( $post_id, 'wpc-num-sections', $section_count );
		for( $i = 1; $i <= $section_count;  $i++ ){
			if(isset( $_POST['wpc-lesson-attachments-' . $i] ) && $_POST['wpc-lesson-attachments-' . $i] != ''){
				update_post_meta( $post_id, 'wpc-media-sections-' . $i, $_POST['wpc-lesson-attachments-' . $i] );
			} else{
				delete_post_meta($post_id, 'wpc-media-sections-' . $i);
			}
		}
	}
}
add_action( 'save_post', 'wpc_save_meta_box_data' );