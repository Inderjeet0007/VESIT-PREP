<?php 
	add_shortcode('lesson_count', 'wpc_lesson_count');
	function wpc_lesson_count(){
		$args = array(
			'post_type' => 'lesson',
			'posts_per_page' => -1
		);
		$query = new WP_Query($args);
		return $query->post_count;
	}
	add_shortcode('course_count', 'wpc_course_count');
	function wpc_course_count(){
		$args = array(
			'post_type' => 'course',
			'posts_per_page' => -1
		);
		$query = new WP_Query($args);
		return $query->post_count;
	}
	// list all courses
	add_shortcode( 'courses', 'wpc_courses' );
	function wpc_courses(){
		$data = '';
		// course category list
		$wpc_course = new WPC_Courses();
		$data .= $wpc_course->get_course_category_list();
		// courses in selected category
		if(!empty($_GET['category'])){
			$terms = array(
					'taxonomy' 	=> 'course-category',
					'field'    	=> 'slug',
					'terms'		=> $_GET['category'],
				);
			} else{
				$terms = array(
					'taxonomy' 	=> 'course-category',
					'field'    	=> 'slug',
				);
			}
			if(empty($_GET['category'])) {
				$args = array(
					'post_type'			=> 'course',
					'nopaging' 			=> true,
					'order'				=> 'ASC',
					'orderby'			=> 'menu_order',
					'post_status'		=> 'publish',
				);
			} else {
				$args = array(
					'post_type'			=> 'course',
					'nopaging' 			=> true,
					'order'				=> 'ASC',
					'orderby'			=> 'menu_order',
					'post_status'		=> 'publish',
					'tax_query' => array(
					$terms,
					),
				);
			}
			$query = new WP_Query($args);
			$wp_courses = new WPC_Courses();
			if ( $query->have_posts() ) {
				$data .= '<div id="courses-wrapper">';
				while ( $query->have_posts() ) {
					$query->the_post();
					$course_id = get_the_ID();
					$excerpt = get_the_excerpt($course_id);
					$course_video = get_post_meta($course_id, 'course-video', true);
					$teacher_id = get_post_meta( $course_id, 'wpc-connected-teacher-to-course', true );
					$data .= '<div class="course-container">';
							if($course_video != ''){
									if(strpos($course_video, 'iframe') === false && !empty($course_video)){
										if(preg_match("/[a-z]/i", $lesson_video) || preg_match("/[A-Z]/i", $course_video)){
										    $course_video = '<iframe id="video-iframe" width="560" height="315" src="https://www.youtube.com/embed/' . $course_video . '" frameborder="0" allowfullscreen></iframe>';
										} else {
											$course_video = '<iframe id="video-iframe" src="https://player.vimeo.com/video/' . $course_video . '" width="500" height="216" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
										}
									}
									$data .= $course_video;
							} else{
								$data .= get_the_post_thumbnail($course_id, 'large');
							}
							$data .= '<div class="course-excerpt">';
								$data .= '<h2 class="course-title"><a href="' . get_the_permalink($course_id) . '">' . get_the_title($course_id) . '</a></h2>';
								$data .= $excerpt;
							$data .= '</div>';
							$data .= '<div class="course-meta-wrapper">';
							    $data .= $wp_courses->get_start_course_button($course_id);
								$data .= '<div class="cm-item">';
									$data .= '<span>Lvl: ' . $wp_courses->get_course_difficulty($course_id) . '</span>';
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
						$data .= '</div>';
				}
				$data .= '</div>';
			} else {
				$data .= 'There are no courses.';
			}
		return $data;
	}
?>