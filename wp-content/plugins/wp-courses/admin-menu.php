<?php
// create custom plugin settings menu
add_action('admin_menu', 'wpc_create_menu');
function wpc_create_menu() {
	//create new top-level menu
	add_menu_page('WP Courses', 'WP Courses', 'administrator', 'wpc_settings', 'wpc_settings_page' );
	//call register settings function
	//add_action( 'admin_init', 'wpc_register_settings' );
}
function wpc_settings_page(){ ?>
	<div class="wpc-alt-admin-buttons">
	    <a href="http://mylesenglish.com">Developer's Home Page</a>
	    <a href="http://mylesenglish.com/contact">Contact the Developer</a>
	</div>
	<div class="wpc-alt-admin-menu">
	    <a href="edit.php?post_type=course">Courses</a>
	   	<a href="edit-tags.php?taxonomy=course-difficulty&post_type=course">Course Difficulties</a>
	    <a href="edit-tags.php?taxonomy=course-category&post_type=course">Course Categories</a>
	    <a href="edit.php?post_type=lesson">Lessons</a>
	    <a href="admin.php?page=order_lessons">Order Lessons</a>
	   	<a href="edit.php?post_type=teacher">Teachers</a>
	</div>
	<div class="wpc-admin-section">
	    <h1>Extensions</h1>
	    	<div class="extension">
	    		<h3>Paid Memberships Pro Integration</h3>
	    		<p>Sell and restrict access to your course content.</p>
	    		<a href="http://wpcoursesplugin.com/product/wp-courses-paid-memberships-pro-integration/" class="wpc-admin-btn">Learn More</a>
	    	</div>
	    	<div class="extension">
	    		<h3>User Activity Shortcode</h3>
	    		<p>Let your users keep track of the lessons they've recently viewed.</p>
	    		<a href="http://wpcoursesplugin.com/product/wp-courses-user-activity/" class="wpc-admin-btn">Learn More</a>
	    	</div>
	    	<div class="extension">
	    		<h3>What's New Widget</h3>
	    		<p>Display a list of new lessons, courses and teachers.</p>
	    		<a href="http://wpcoursesplugin.com/product/wp-courses-whats-new-widget/" class="wpc-admin-btn">Learn More</a>
	    	</div>
	    	<div class="extension">
	    		<h3>PayPal integration (Coming Soon)</h3>
	    		<p>Sell access to individual courses with PayPal.</p>
	    	</div>
	    	<div class="extension">
	    		<h3>Quizes (Coming Soon)</h3>
	    		<p>Add quizes to your courses!</p>
	    	</div>
    </div>
    <div class="wpc-admin-section">
    	<h1>Getting Started</h1>
    	<p>Setting up WP Courses is very easy.</p>
    	<h2>Basic Setup</h2>
    	<ol>
    		<li>Make a new page and give it a title like "Courses".</li>
    		<li>Include the shortcode [courses] anywhere in the page content</li>
    		<li>Add the page to your navigation menu so your users can find your courses</li>
    	</ol>
    	<h2>Making Courses</h2>
    	<ol>
    		<li>Click course difficulties at the top of this page and create at least one course difficulty.  For example, easy, medium and difficult or 1, 2 and 3.</li>
    		<li>Click course categories at the top of this page and create at least one category.</li>
    		<li>Click teachers at the top of this page and create at least one teacher.</li>
    		<li>Create a course by clicking courses at the top of this page.  Fill in the title, content and other applicable info.</li>
    		<li>Create lessons by clicking lessons at the top of this page.  Make sure to connect the lesson(s) you make to the course(s) you've made.  If you are embedding a video, use the embed code.  The embed code starts with "iframe".</li>
    		<li>Order the lessons on the order lessons page by first selecting their category and difficulty, then drag and drop them.</li>
    		<li>The orphan lessons page is there to help you find lessons that are not connected to a course.  Lessons that are not connected to a course will not display anywhere in the front-end of your website.</li>
    	</ol>
    </div>
<?php }
function wpc_register_submenu(){
    add_submenu_page( 'wpc_settings', 'Courses', 'Courses', 'manage_options', 'edit.php?post_type=course' );
    add_submenu_page( 'wpc_settings', 'Course Difficulties', 'Course Difficulties', 'manage_options', 'edit-tags.php?taxonomy=course-difficulty&post_type=course' );
    add_submenu_page( 'wpc_settings', 'Course Categories', 'Course Categories', 'manage_options', 'edit-tags.php?taxonomy=course-category&post_type=course' );
    add_submenu_page( 'wpc_settings', 'Order Courses', 'Order Courses', 'manage_options', 'order_courses', 'wpc_order_courses_page' );
    add_submenu_page( 'wpc_settings', 'Lessons', 'Lessons', 'manage_options', 'edit.php?post_type=lesson' );
    add_submenu_page( 'wpc_settings', 'Order Lessons', 'Order Lessons', 'manage_options', 'order_lessons', 'wpc_order_lessons_page' );
    add_submenu_page( 'wpc_settings', 'Orphan Lessons', 'Orphan Lessons', 'manage_options', 'orphan_lessons', 'wpc_orphan_lessons_page' );
    add_submenu_page( 'wpc_settings', 'Teachers', 'Teachers', 'manage_options', 'edit.php?post_type=teacher' );
}
	// Ajax for saving ordering
	add_action( 'admin_footer', 'wpc_action_order_lessons_javascript' );
	function wpc_action_order_lessons_javascript() { ?>
		<script type="text/javascript" >
		jQuery(document).ready(function() {
			jQuery( ".lesson-list" ).sortable({
				    axis: 'y',
				    update: function (event, ui) {
				        var posts = [];
				        var menuOrder = [];
				        count = 0;
				        jQuery('.lesson-list tr').each(function(key, value){
				        	posts.push(jQuery(this).attr('data-id'));
				        	menuOrder.push(key);
				        	count = count + 1;
				        });
						var data = {
							'action': 'order_lessons',
							'menuOrder': menuOrder,
							'id': posts,
						};
						jQuery.post(ajaxurl, data, function(response) {
							// response goes here
						});
				    }
			});
		});
		</script> <?php
	}
	add_action( 'wp_ajax_order_lessons', 'wpc_order_lessons_action_callback' );
	function wpc_order_lessons_action_callback(){
		global $wpdb;
	    $posts = $_POST;	
	    $count = 0;
	    foreach($_POST['id'] as $id){
	    	$order = $_POST['menuOrder'][$count];
	    	$id = (int) $id;
	    	$order = (int) $order;
			$wpdb->query(
			    $wpdb->prepare(
			        "UPDATE $wpdb->posts SET menu_order = %d WHERE ID = %d",
			        $order,
			        $id
			    )
			);
	    	$count++;
	    }
	    wp_die(); // required
	}
function wpc_orphan_lessons_page(){
	echo '<div class="wrap">';
	echo '<h1>Orphan Lessons</h1>';
	echo '<p>These are lessons that are not currently connected to a course.</p>';
		$wpc_admin = new WPC_Admin();
		echo $wpc_admin->get_order_lessons_table('none');
	echo '</div>';
}
function wpc_order_lessons_page(){
	$wpc_admin = new WPC_Admin();
	echo '<div class="wrap">';
	echo '<div class="wp-filter wpc-nav-wrap">';
		echo '<h2>Course Categories</h2>';
		$wpc_course = new WPC_Courses();
		echo $wpc_course->get_course_category_list('&page=order_lessons', 'button small');
		echo '</div>';
	echo '</div>';
	if(isset($_GET['category'])){
		echo '<div class="wrap">';
		echo '<div class="wp-filter wpc-nav-wrap">';
			echo '<h2>Courses</h2>';
			echo $wpc_admin->get_course_list('&page=order_lessons');
		echo '</div>';
		echo '</div>';
	}
	echo '<div class="wrap">';
	if(isset($_GET['course_id'])){
		echo $wpc_admin->get_order_lessons_table($_GET['course_id']);
	}
	echo '</div>';
}
add_action( 'admin_footer', 'wpc_change_course_javascript' );
function wpc_change_course_javascript() { 
	$ajax_nonce = wp_create_nonce( "request-is-good-wpc" ); ?>
	<script type="text/javascript" >
	jQuery(document).ready(function() {
		jQuery('.course-select').change(function(){
			var parent = jQuery(this).parent().parent();
			var data = {
				'security': "<?php echo $ajax_nonce; ?>",
				'action': 'change_course',
				'course_id': jQuery(this).val(),
				'lesson_id': parent.attr('data-id'),
			}
			if (confirm('Are you sure you want to change the connected course?')) {
			    parent.fadeToggle();
			} else {
			    return;
			}
			jQuery.post(ajaxurl, data, function(response) {});
			
		});
	});
	</script> <?php
}
add_action( 'wp_ajax_change_course', 'wpc_change_course_action_callback' );
function wpc_change_course_action_callback(){
	check_ajax_referer( 'request-is-good-wpc', 'security' );
	$lesson_id = (int) $_POST['lesson_id'];
	$course_id = (int) $_POST['course_id'];
	update_post_meta($lesson_id, 'wpc-connected-lesson-to-course', $course_id);
	wp_die(); // required
}
add_action( 'admin_footer', 'wpc_change_lesson_restriction_javascript' );
function wpc_change_lesson_restriction_javascript() { 
	$ajax_nonce = wp_create_nonce( "request-is-good-wpc" ); ?>
	<script type="text/javascript" >
	jQuery(document).ready(function() {
		jQuery('.lesson-restriction-radio').click(function(){
			var parent = jQuery(this).parent().parent();
			var data = {
				'security': "<?php echo $ajax_nonce; ?>",
				'action': 'wpc_change_restriction',
				'lesson_id': parent.attr('data-id'),
				'restriction': jQuery(this).val(),
			}
			jQuery.post(ajaxurl, data, function(response) {
				alert('Lesson Restriction Updated.');
			});
			
		});
	});
	</script> <?php
}
add_action( 'wp_ajax_wpc_change_restriction', 'wpc_change_restriction_action_callback' );
function wpc_change_restriction_action_callback(){
	check_ajax_referer( 'request-is-good-wpc', 'security' );
	$lesson_id = (int) $_POST['lesson_id'];
	$restriction = addslashes(strip_tags($_POST['restriction']));
	update_post_meta($lesson_id, 'wpc-lesson-restriction', $restriction);
	wp_die(); // required
}
add_action( 'admin_footer', 'wpc_order_course_javascript' );
function wpc_order_course_javascript() { ?>
	<script type="text/javascript" >
	jQuery(document).ready(function() {
		jQuery( ".course-list" ).sortable({
			    axis: 'y',
			    update: function (event, ui) {
			        var posts = [];
			        var menuOrder = [];
			        count = 0;
			        jQuery('.course-list li').each(function(key, value){
			        	posts.push(jQuery(this).attr('data-id'));
			        	menuOrder.push(key);
			        	count = count + 1;
			        });
					var data = {
						'action': 'order_course',
						'menuOrder': menuOrder,
						'id': posts,
					};
					jQuery.post(ajaxurl, data, function(response) {
					
					});
			    }
		});
	});
	</script> <?php
}
add_action( 'wp_ajax_order_course', 'wpc_order_course_action_callback' );
function wpc_order_course_action_callback(){
	global $wpdb;
    $posts = $_POST;	
    $count = 0;
    foreach($_POST['id'] as $id){
	    	$order = $_POST['menuOrder'][$count];
	    	$id = (int) $id;
	    	$order = (int) $order;
			$wpdb->query(
			    $wpdb->prepare(
			        "UPDATE $wpdb->posts SET menu_order = %d WHERE ID = %d",
			        $order,
			        $id
			    )
			);
	    	$count++;
    }
    wp_die(); // required
}
function wpc_order_courses_page(){ ?>
		<div class="wrap">
			<h1>Order Courses</h1>
			<div id="order-course-msg-wrapper"></div>
			<div class="card">
			<?php 
				$wpc_courses = new WPC_Courses();
				echo $wpc_courses->course_list();
			?>
			</div>
		</div>
<?php }
add_action('admin_menu', 'wpc_register_submenu');