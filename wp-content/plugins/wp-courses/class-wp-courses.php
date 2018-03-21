<?php
	class WPC_Courses{
		public function get_percent_viewed($course_id){
			$wpc_lessons = new WPC_Lessons();
			$all_lessons = $wpc_lessons->get_connected_lessons($course_id);
			$wpc_tracking = new WPC_Tracking;
			$viewed_lessons = $wpc_tracking->get_lesson_tracking();
			$matching_lessons = array_intersect($viewed_lessons, $all_lessons);
			$lesson_count = count($all_lessons);
			if( $lesson_count > 0 ) {
				$percent_viewed = count($matching_lessons) / count($all_lessons); 
				$percent_viewed = $percent_viewed * 100;
			} else {
				$percent_viewed = 0;
			}
			return round($percent_viewed);
		}
		public function get_course_category_list($get = '', $class = ''){
			$data = '';
			$categories = get_terms('course-category');
			if(isset($_GET['category'])){
				$cat = $_GET['category'];
			} else {
				$cat = '';
			}
			//$data .= '<h2>Course Categories</h2>';
			$data .= '<ul class="course-category-list">';
				foreach($categories as $category){
					$category->slug == $cat ?	$active = 'active' : $active = '';
					$data .= '<li class="cm-item"><a href="?category=' . $category->slug . $get . '" class="' . $class . ' ' . $active . '">' . $category->name . '</a></li>';
				}
			$data .= '</ul>';
			return $data;
		}
		public function course_list($get = ''){
			do_action('wpc_before_course_list');
			$course_args = array(
				'post_type'			=> 'course',
				'nopaging' 			=> true,
				'orderby'			=> 'menu_order',
				'post_status'		=> 'publish',
			);
			$course_query = new WP_Query($course_args);
			$data = '';
			$data .= '<ul class="course-list">';
			while($course_query->have_posts()){
				$course_query->the_post();
				$data .= '<li data-id="' . get_the_ID() . '">' . get_the_title() . '</li>';
			}
			$data .= '</ul>';
			wp_reset_postdata();
			do_action('wpc_after_course_list');
			return $data;
		}
		public function get_course_difficulty($post_id){
			$data = '';
			$terms = wp_get_post_terms($post_id, 'course-difficulty');
			foreach($terms as $term){
				$data .= $term->name;
			}
			return '<span class="difficulty-' . $term->slug . ' course-difficulty">' . $data . '</span>';
		}
		// function should be used on single course page template
		public function get_start_course_button($course_id){
			//global $wpdb;
			//$prefix = $wpdb->prefix;
			$course_id = (empty($course_id)) ? get_the_ID() : $course_id;
			$args = array(
				'post_type'			=> 'lesson',
				'meta_value'		=> $course_id,
				'meta_key'			=> 'wpc-connected-lesson-to-course',
				'posts_per_page'	=> 1,
				'paged'				=> false,
				'nopaging' 			=> true,
				'order'				=> 'ASC',
				'orderby'			=> 'menu_order'
			);
			$query = new WP_Query($args);
			while($query->have_posts()){
				$query->the_post();
				$data = '<a class="start-button cm-item" href="' . get_the_permalink() . '">Lesson 1 <i class="fa fa-arrow-right"></i></a>';
				break;
			}
			wp_reset_postdata();
			return $data;
		}
	}
	class WPC_Lessons{
		public function __construct(){
			$this->post_id = get_the_ID();
		}
		public function get_lesson_attachments($lesson_id){
			$sections = get_post_meta($lesson_id, 'wpc-num-sections', true); 
			$attachments = array();
			for($i = 1; $i<=$sections; $i++){
				$url = get_post_meta( $lesson_id, 'wpc-media-sections-' . $i, true );
				array_push($attachments, $url);
			}
			return $attachments;
		}
		public function get_lesson_video($lesson_id, $show_title = true){
			do_action('wpc_before_lesson_video');
			$data = '';
			$lesson_video = get_post_meta($lesson_id, 'lesson-video', true);
			if(strpos($lesson_video, 'iframe') === false && !empty($lesson_video)){
				if(preg_match("/[a-z]/i", $lesson_video) || preg_match("/[A-Z]/i", $lesson_video)){
				    $lesson_video = '<iframe id="video-iframe" width="560" height="315" src="https://www.youtube.com/embed/' . $lesson_video . '" frameborder="0" allowfullscreen></iframe>';
				} else {
					$lesson_video = '<iframe id="video-iframe" src="https://player.vimeo.com/video/' . $lesson_video . '" width="500" height="216" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
				}
			}
			if(has_filter( 'wpc_lesson_video' )){
				$lesson_video = apply_filters( 'wpc_lesson_video', $lesson_video );
			}
			if($show_title == true){
				$data .= '<h1>' . get_the_title() . '</h1>';
			}
			$course_id = get_post_meta($lesson_id, 'wpc-connected-lesson-to-course', true);
	
			$data .= $lesson_video;
			do_action('wpc_after_lesson_video');
			return $data;
		}
		// gets lessons that are connected to the same course as $lesson_id
		public function get_connected_lessons($course_id){
			global $wpdb;
			$prefix = $wpdb->prefix;
			$lessons = $wpdb->get_results("SELECT post_id FROM {$prefix}postmeta WHERE meta_value = $course_id AND meta_key = 'wpc-connected-lesson-to-course'");
			$lessons_array = array();
			foreach( $lessons as $lesson ){
				array_push($lessons_array, $lesson->post_id);
			}
			return $lessons_array;
		}
		public function get_lesson_list($course_id){
			$data = '';
			$lessons = $this->get_connected_lessons( $course_id );
			if(!empty($lessons)){
				$args = array(
					'post_type'			=> 'lesson',
					'post__in'			=> $lessons,
					'nopaging' 			=> true,
					'order'				=> 'ASC',
					'orderby'			=> 'menu_order'
				);
				$query = new WP_Query($args);
				$data .= '<ul class="lesson-list">';
				while($query->have_posts()){
					$query->the_post();
					$id = get_the_ID();
					$link = get_edit_post_link( $id );
					$data .= '<li data-id="' . $id . '"><a href="' . $link . '">' . get_the_title() . '</a></li>';
				
				}
				$data .= '</ul>';
				wp_reset_postdata();
			}
			return $data;
		}
		public function get_lesson_navigation($course_id){
			$data = '';
			$count = 1;
			$lessons = $this->get_connected_lessons($course_id);
			$tracking = new WPC_Tracking();
			$tracking = $tracking->get_lesson_tracking();
			if(!empty($lessons)){
				$args = array(
					'post_type'			=> 'lesson',
					'post__in'			=> $lessons,
					'nopaging' 			=> true,
					'order'				=> 'ASC',
					'orderby'			=> 'menu_order'
				);
				$query = new WP_Query($args);
				$data .= '<ul class="lesson-nav">';
				$this_post_id = get_the_ID();
				while($query->have_posts()){
					$query->the_post();
					$lesson_button_id = get_the_ID();
					$post_id = $lesson_button_id;
					$restriction = get_post_meta( $post_id, 'wpc-lesson-restriction', true );
					if( is_user_logged_in() ){
						
						if(in_array($post_id, $tracking)){
							$icon = '<i class="fa fa-eye"></i>';
						} else {
							$icon = '<i class="fa fa-play"></i>';
						}
						
					} else {
						if($restriction == 'none'){
							$icon = '<i class="fa fa-play"></i>';
						} else {
							$icon = '<i class="fa fa-lock"></i>';
						}
					}			
					if(has_filter( 'wpc_lesson_button_icon' )){
			            $icon = apply_filters( 'wpc_lesson_button_icon', $icon, $lesson_id );
			        }
					
					if($this->post_id == $lesson_button_id){
						$active = 'active-lesson-button';
					} else{
						$active = '';
					}
					$data .= '<li class="lesson-button ' . $active . '"><a href="' . get_the_permalink() . '">' . $icon . $count . ' - ' . get_the_title() . '</a></li>';
					$count++;
				}
				$data .= '</ul>';
				wp_reset_postdata();
			}
			return $data;
		}
	}
	class WPC_Tracking{
		// call this function to initiate tracking.
		public function lesson_tracking($post_id){
			$user_id = get_current_user_id();
			$viewed_lessons = get_user_meta($user_id, 'wpc-lesson-tracking', true);
			if($viewed_lessons == '' || count($viewed_lessons) < 1){
				$viewed_lessons = array($post_id);
				update_user_meta($user_id, 'wpc-lesson-tracking', $viewed_lessons );
			} else{
				//$viewed_lessons = array_unique($viewed_lessons);
				array_unshift($viewed_lessons, $post_id);
				$viewed_lessons = array_unique($viewed_lessons);
				update_user_meta($user_id, 'wpc-lesson-tracking', $viewed_lessons);
			}
			return;
		}
		// return array of viewed lesson ids
		public function get_lesson_tracking(){
			$user_id = get_current_user_id();
			$lessons = get_user_meta($user_id, 'wpc-lesson-tracking', true);
			if(empty($lessons)){
				$lessons = array();
			}
			return $lessons;
		}
		// This function checks if course is completed and updates user meta accordingly
		public function course_tracking(){
			$user_id = get_current_user_id();
			$wpc_courses = new WPC_Courses();
			$lesson_id = get_the_ID();
			$course_id = get_post_meta($lesson_id, 'wpc-connected-lesson-to-course', true);
			$percent = $wpc_courses->get_percent_viewed($course_id);
			$completed_courses = get_user_meta($user_id, 'wpc-completed-courses', true );
			 
			if($percent == 100){
				if(empty($completed_courses)){
					$completed_courses = array($course_id);
					update_user_meta($user_id, 'wpc-completed-courses', $completed_courses );
				} else{
					array_push($completed_courses, $course_id);
					$completed_courses = array_unique($completed_courses);
					update_user_meta($user_id, 'wpc-completed-courses', $completed_courses);
				}
			}
		}
	}
	class WPC_Tools{
		public function get_toolbar(){
			$data = '';
			$tracking = new WPC_Tracking();
			$tracking = $tracking->get_lesson_tracking();
			if( is_user_logged_in() && count($tracking) > 0 ){
			$args = array(
				'post_type'			=> 'lesson',
				'post__in'			=> $tracking,
				'posts_per_page'	=> 10,
				'orderby'			=> 'post__in',
			);
			
			$query = new WP_Query($args);
				$data .= '<div class="tools-container">';
					$data .= '<div class="tool-toggle">';
						$data .= '<i class="fa fa-eye"></i>';
						$data .= '<ul class="lesson-nav toolbar-content">';
						if($query->have_posts()){
							while($query->have_posts()){
								$query->the_post();
								$data .= '<li class="lesson-button"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
							}
						}
						wp_reset_postdata();
						$data .= '</ul>';
					$data .= '</div>';
					$lesson_id = get_the_ID();
					$attachments = new WPC_Lessons();
					$attachments = $attachments->get_lesson_attachments($lesson_id);
					$extensions = array();
					foreach($attachments as $ext){
						$info = new SplFileInfo($ext);
						$this_ext = $info->getExtension();
						array_push($extensions, $this_ext);
						$extensions = array_unique($extensions);
					}
					if(in_array('jpg', $extensions) == true && in_array('png', $extensions) == true){
						$search = array_search('jpg', $extensions);
						unset($extensions[$search]);
					}
					foreach($extensions as $ext){
						if($ext == 'jpg' || $ext == 'png') {
							$data .= '<div class="tool-toggle">';
								$data .= '<i class="fa fa-file-image-o"></i>';
								$data .= '<div class="toolbar-content">';
									foreach($attachments as $att){
										$info = new SplFileInfo($att);
										$this_ext = $info->getExtension();
										if($this_ext == 'jpg' || $this_ext == 'png'){
											$data .= '<a class="toolbar-button" href="' . $att . '">Image</a>';
										}
									}
								$data .= '</div>';
							$data .= '</div>';
						}
						if($ext == 'zip') {
							$data .= '<div class="tool-toggle">';
								$data .= '<i class="fa fa-file-zip-o"></i>';
								$data .= '<div class="toolbar-content">';
									foreach($attachments as $att){
										$info = new SplFileInfo($att);
										$this_ext = $info->getExtension();
										if($this_ext == 'zip'){
											$data .= '<a class="toolbar-button" href="' . $att . '">ZIP File</a>';
										}
									}
								$data .= '</div>';
							$data .= '</div>';
						}
						if($ext == 'pdf') {
							$data .= '<div class="tool-toggle">';
								$data .= '<i class="fa fa-file-pdf-o"></i>';
								$data .= '<div class="toolbar-content">';
									foreach($attachments as $att){
										$info = new SplFileInfo($att);
										$this_ext = $info->getExtension();
										if($this_ext == 'pdf'){
											$data .= '<a class="toolbar-button" href="' . $att . '">PDF Attachment</a>';
										}
									}
								$data .= '</div>';
							$data .= '</div>';
						}
						if($ext == 'mp3') {
							$data .= '<div class="tool-toggle">';
								$data .= '<i class="fa fa-file-audio-o"></i>';
								$data .= '<div class="toolbar-content">';
									foreach($attachments as $att){
										$info = new SplFileInfo($att);
										$this_ext = $info->getExtension();
										if($this_ext == 'mp3'){
											$data .= '<a class="toolbar-button" href="' . $att . '">Audio File</a>';
										}
									}
								$data .= '</div>';
							$data .= '</div>';
						}
					}
				$data .= '</div>';
			}
			wp_reset_postdata();
			return $data;
		}
	}
?>