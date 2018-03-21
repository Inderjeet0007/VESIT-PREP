<?php
class WPC_Admin{
	public function lesson_restriction_radio_buttons($lesson_id, $name = 'wpc-lesson-restriction', $class = 'lesson-restriction-radio'){
		$data = '';
		$lesson_restriction = get_post_meta($lesson_id, 'wpc-lesson-restriction', true);
		if(empty($lesson_restriction)){
			$lesson_restriction = 'none';
		}
		$data .= '<input id="wpc-none-radio" class="' . $class . '" type="radio" name="' . $name . '" value="none" ';
		$data .= $lesson_restriction == 'none' ? 'checked="checked"' : '';	 
		$data .= '/> None<br>';
		$data .= '<input id="wpc-free-account-radio" class="' . $class . '" type="radio" name="' . $name . '" value="free-account" ';
		$data .= $lesson_restriction == 'free-account' ? 'checked="checked"' : '';
		$data .= '/> Free Account';
		// Hidden membership field
		$data .= '<input style="display:none;" id="wpc-membership-radio" class="' . $class . '" type="radio" name="' . $name . '" value="membership" ';
		$data .= $lesson_restriction == 'membership' ? 'checked="checked"' : '';
		$data .= '/>';
		return $data;
	}
	public function get_course_dropdown($class = ''){
		$data = '';
		$lesson_id = get_the_ID();
		$args = array(
			'post_type'		=> 'course',
			'post_status'		=> 'publish',
			'posts_per_page'	=> -1,
		);
		$query = new WP_Query($args);
		$data .= '<select name="course-selection" class="' . $class . '">';
		while($query->have_posts()){
			$query->the_post();
			$post_id = get_the_ID();
			$course_id = get_post_meta($lesson_id, 'wpc-connected-lesson-to-course', true);
			if( $course_id == $post_id){
				$selected = 'selected="selected"';
			} else{
				$selected = '';
			}
			$data .= '<option value="none"' . $selected . '>None</option>';
			$data .= '<option value="' . $post_id . '"' . $selected . '>' . get_the_title() . '</option>';
		}
		$data .= '</select>';
		wp_reset_postdata();
		return $data;
	}
	public function get_course_list($get = '', $class = 'button small'){
		$course_args = array(
			'post_type'			=> 'course',
			'nopaging' 			=> true,
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order',
			'post_status'		=> 'publish',
			'tax_query' => array(
				array(
					'taxonomy' => 'course-category',
					'field'    => 'slug',
					'terms'    => $_GET['category'],
				),
			),
		);
		$course_query = new WP_Query($course_args);
		$data = '';
		$wpc_courses = new WPC_Courses();
		$data .= '<ul class="course-list-buttons">';
		while($course_query->have_posts()){
			$course_query->the_post();
			$course_id = get_the_ID();
			if(isset($_GET['course_id'])){
				$active = $_GET['course_id'] == $course_id ? 'active' : '';
			} else {
				$active = '';
			}
			$difficulty = $wpc_courses->get_course_difficulty($course_id);
			$data .= '<li data-id="' . $course_id . '"><a class="' . $class . ' ' . $active . '" href="?category=' . $_GET['category'] . '&course_id=' . $course_id . $get . '">' . get_the_title() . ' - Difficulty: ' . $difficulty . '</a></li>';
		}
		$data .= '</ul>';
		wp_reset_postdata();
		return $data;
	}
	public function get_order_lessons_table($course_id){
		$data = '';
		$wpc_admin = new WPC_Admin();
		$args = array(
			'post_type'			=> 'lesson',
			'meta_value'		=> $course_id,
			'meta_key'			=> 'wpc-connected-lesson-to-course',
			'posts_per_page'	=> -1,
			'nopaging' 			=> true,
			'order'				=> 'ASC',
			'orderby'			=> 'menu_order',
		);
		$query = new WP_Query($args);
		$data .= '<h4 id="admin-lesson-count">' . $query->found_posts . ' Lessons</h4>';
		$data .= '<table class="widefat fixed sortable-table">';
		$data .= '<thead><th>Title</th><th>Course</th><th>Restriction</th></thead>';
		$data .= '<tbody class="lesson-list">';
		while($query->have_posts()){
			$query->the_post();
			$id = get_the_ID();
			$link = get_edit_post_link( $id );
			$video_id = get_post_meta($id, 'lesson-video', true);
			$data .= '<tr data-id="' . $id . '">';
			$data .= '<td><a href="' . $link . '">' . get_the_title() . '</a></td>';
			$data .= '<td>';
			$data .= $wpc_admin->get_course_dropdown('course-select chosen-select');
			$data .= '</td>';
			$data .= '<td>';
			$data .= $wpc_admin->lesson_restriction_radio_buttons($id, 'radio-' . $id);
			$data .= '</td>';
			$data .= '</tr>';
			
		}
		$data .= '<tbody>';
		$data .= '</table>';
		wp_reset_postdata();
		
		return $data;
	}
}
?>