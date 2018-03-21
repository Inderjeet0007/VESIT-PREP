<?php
add_filter( 'manage_edit-lesson_columns', 'wpc_lesson_columns' ) ;
function wpc_lesson_columns( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'title' => __( 'Form' ),
		'course' => __( 'Connected Course' ),
		'restriction'	=> __( 'Restriction'),
		'date' => __( 'Date' )
	);
	return $columns;
}
add_action( 'manage_lesson_posts_custom_column', 'wpc_manage_lesson_columns', 10, 2 );
function wpc_manage_lesson_columns( $column, $post_id ) {
	global $post;
	switch( $column ) {
		case 'course' :
			$lesson_id = get_the_ID();
			$course_id = get_post_meta($lesson_id, 'wpc-connected-lesson-to-course', true);
			echo '<a href="' . get_edit_post_link($course_id) . '">' . get_the_title($course_id) . '</a>';
		break;
		case 'restriction' :
			$lesson_id = get_the_ID();
			$lesson_restriction = get_post_meta($lesson_id, 'wpc-lesson-restriction', true);
			echo ucwords( str_replace('-', ' ', $lesson_restriction) );
		break;
	}
}
?>