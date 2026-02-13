<?php
add_action( 'after_setup_theme', 'lkba_theme_setup' );
function lkba_theme_setup() {
load_theme_textdomain( 'rezervace_theme', get_template_directory() . '/languages' );
add_theme_support( 'title-tag' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'post-thumbnails' );
global $content_width;
if ( ! isset( $content_width ) ) $content_width = 640;


register_nav_menus( array(
		'main-menu'    => __( 'Hlavni Menu', 'rezervace_theme' ),
		'fcbk-insta' => __( 'Social links', 'rezervace_theme' ),
		'lang-menu' => __( 'Languages', 'rezervace_theme' ),
		// 'selekce-menu'    => __( 'Selection Menu', 'rezervace_theme' ),
		// 'index-menu'    => __( 'Index Menu', 'rezervace_theme' ),
		// 'contact-menu'    => __( 'Contact Menu', 'rezervace_theme' ),
		// 'support' => __( 'Podpora', 'rezervace_theme' ),
	) );
}

add_action( 'comment_form_before', 'lkba_theme_enqueue_comment_reply_script' );
function lkba_theme_enqueue_comment_reply_script() {
	if ( get_option( 'thread_comments' ) ) { wp_enqueue_script( 'comment-reply' ); }
}
add_filter( 'the_title', 'lkba_theme_title' );
function lkba_theme_title( $title ) {
	if ( $title == '' ) {
		return '&rarr;';
	} else {
		return $title;
	}
}

function wp_example_excerpt_length( $length ) {
    return 12;
}
add_filter( 'excerpt_length', 'wp_example_excerpt_length');

// REMOVE HARDCODED WIDTH & HEIGHT INLINE IN THUMBNAIL
add_filter( 'post_thumbnail_html', 'remove_thumbnail_dimensions', 10, 3 );

function remove_thumbnail_dimensions( $html, $post_id, $post_image_id ) {
    $html = preg_replace( '/(width|height)=\"\d*\"\s/', "", $html );
    return $html;
}
// END REMOVE WIDTH & HEIGHT

add_filter( 'wp_title', 'lkba_theme_filter_wp_title' );
function lkba_theme_filter_wp_title( $title ) {
	return $title . esc_attr( get_bloginfo( 'name' ) );
}
add_action( 'widgets_init', 'lkba_theme_widgets_init' );
function lkba_theme_widgets_init() {
	// Arguments used in all register_sidebar() calls.
	$shared_args = array(
		'before_title'  => '<!-- ',
		'after_title'   => ' -->',
		// 'before_widget' => '<div class="widget %2$s"><div class="widget-content">',
		'before_widget' => ' ',
		// 'after_widget'  => '</div></div>',
		'after_widget'  => ' ',
	);

	register_sidebar( 
		array_merge(
			$shared_args,
			array (
				'name' => __( 'Footer', 'rezervace_theme' ),
				'id' => 'footer-widget-area',
				// 'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
				// 'after_widget' => "</li>",
				// 'before_title' => '<h3 class="widget-title">',
				// 'after_title' => '</h3>',
			)
		)
	);

	register_sidebar( 
		array_merge(
			$shared_args,
			array (
				'name' => __( 'Social links', 'rezervace_theme' ),
				'id' => 'sociallinks-widget-area'
			)
		)
	);
}
function lkba_theme_custom_pings( $comment ) {
	$GLOBALS['comment'] = $comment;
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>"><?php echo comment_author_link(); ?></li>
	<?php 
}
add_filter( 'get_comments_number', 'lkba_theme_comments_number' );

function lkba_theme_comments_number( $count ) {
	if ( !is_admin() ) {
		global $id;
		$temp_comments = get_comments( 'status=approve&post_id=' . $id );
		$comments_by_type = separate_comments( $temp_comments );
		return count( $comments_by_type['comment'] );
	} else {
		return $count;
	}
}
/* / — / — / — / — / — / — / — / — / — */
/* / — / — / — / — / — / — / — / — / — */
/* WRAP THE POST CONTENT IN CUSTOM DIV */
/* / — / — / — / — / — / — / — / — / — */
/* / — / — / — / — / — / — / — / — / — */
function wrap_content_in_div($content) {
    global $post;
    return '<div class="flow">'.$content.'</div>';
}
add_filter('the_content', 'wrap_content_in_div');
/* / — / — / — / — / — / — / — / — / — */
/* / — / — / — / — / — / — / — / — / — */
/* / — / — / — / — / — / — / — / — / — */
/* / — / — / — / — / — / — / — / — / — */


/*   — — —  — — — — — — — — — — — — */
/*   — — —  — — — — — — — — — — — — */
/*   — — —  — — — — — — — — — — — — */
/*   — — —  — — — — — — — — — — — — */
/* ACF CUSTOM BLOCKS
/*   — — —  — — — — — — — — — — — — */
add_action( 'init', 'register_acf_blocks', 5 );
function register_acf_blocks() {
    foreach ( glob( __DIR__ . '/blocks/block-*' ) as $block_path ) {
        register_block_type( $block_path );
    }
    // Register js files in block directories
    // You'll have to name your js file "josh-my-block-name.js" so that it adds the right name space for the block.
    foreach ( glob(__DIR__ . '/blocks/block-*/*.js') as $path) {
        $file_name = pathinfo($path, PATHINFO_FILENAME);
        // wp_register_script( $file_name, get_stylesheet_directory_uri() . '/blocks/' . $file_name . '/' . $file_name . '.js', '', $GLOBALS['version']);
        wp_register_script( $file_name, get_stylesheet_directory_uri() . '/blocks/' . $file_name . '/' . $file_name . '.js', '');
    }
}

add_filter( 'acf/blocks/wrap_frontend_innerblocks', 'acf_should_wrap_innerblocks', 10, 2 );
function acf_should_wrap_innerblocks( $wrap, $name ) {
    // if ( $name == 'acf/test-block' ) {
        // return true;
    // }
    return false;
}




function sevenYearSelect($field) {
    $currentYear = date('Y');
    // Create choices array
    $field['choices'] = array();
    // Add blank first selection; remove if unnecessary
    // $field['choices'][''] = ' ';
    // Loop through a range of years and add to field 'choices'. Change range as needed.
    foreach(range($currentYear+3, $currentYear-8) as $year) {
            
        $field['choices'][$year] = $year;
            
    }
    // Return the field
    return $field;
}

// add_filter('acf/load_field/key=field_0000000000000', 'sevenYearSelect');
// Apply to fields named "proj_year".
add_filter('acf/load_field/name=proj_year', 'sevenYearSelect');


// Gutenberg custom stylesheet
#add_theme_support('editor-styles');
#add_editor_style( 'editor-style.css' ); // make sure path reflects where the file is located

/* / — / — / — / — / — / – */
/* / — / — / — / — / — / – */
/* CUSTOMIZE WP LOGIN PAGE */
/* / — / — / — / — / — / – */

function my_login_logo() { ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 300 150'%3E%3Cpath fill='%2312120D' d='m117.879 64.878 1.679-13.17h-1.049c-2.781 8.973-5.352 11.281-14.796 11.281h-1.626c-1.627 0-2.151-.42-2.151-2.151v-26.39c0-5.457.157-6.034 5.194-6.716v-.945H87.554v.945c5.036.682 5.194 1.259 5.194 6.715v22.718c0 5.352-.158 6.139-5.195 6.716v.944h19.623c2.046 0 8.237.053 10.703.053Zm2.256 52.326-13.851-15.897 8.395-7.765c5.823-5.404 7.187-6.558 10.598-6.978v-.944h-12.959v.944c5.561.42 4.669 2.256-.578 7.083l-11.7 10.86h-.052l12.33 14.376c1.836 2.151 1.783 3.568-1.732 3.83v.945h16.894v-.945c-3.148-.42-4.197-1.784-7.345-5.509Zm-20.2-1.154V93.332c0-5.456.158-6.034 5.195-6.716v-.944H87.554v.944c5.036.682 5.194 1.26 5.194 6.716v22.718c0 5.351-.158 6.138-5.195 6.716v.944h17.577v-.944c-5.037-.578-5.194-1.365-5.194-6.716ZM208.89 53.405c0-5.124-2.623-7.625-5.823-9.077 2.291-1.434 4.319-4.634 4.319-8.272 0-7.94-6.925-9.863-13.921-9.863h-14.568V64.86h15.53c7.888 0 14.463-3.148 14.463-11.472v.017Zm-23.207-21.773h8.429c3.848 0 6.401 1.766 6.401 5.176 0 3.848-2.396 5.544-6.401 5.544h-8.429v-10.72Zm0 15.897h8.639c5.177 0 7.573 1.871 7.573 5.876 0 4.005-2.344 6.034-7.205 6.034h-9.007v-11.91Zm10.878 37.443h-7.153l-14.411 38.668h6.926l2.99-8.587h15.688l3.043 8.587h7.415l-14.516-38.668h.018Zm-9.654 24.432 5.824-16.597 5.876 16.597H186.907Zm-37.408 33.579h3.497V6.85h-3.497V143v-.017Z'/%3E%3C/svg%3E");
			height:150px;
			width:320px;
			background-size: contain;
			background-repeat: no-repeat;
			padding-bottom: 30px;
        }
    </style>
<?php }
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return esc_attr( get_bloginfo( 'name' ) );
    // return $title . esc_attr( get_bloginfo( 'name' ) );
}
add_filter( 'login_headertext', 'my_login_logo_url_title' );

/* / — / — / — / — / — / – */
/* END CUSTOMIZE WP LOGIN PAGE */
/* / — / — / — / — / — / – */
/* / — / — / — / — / — / – */



// Adds support for editor color palette.

function my_theme_add_new_features() {

    // The new colors we are going to add
    $newColorPalette = [
        [
            'name'  => __( 'Krémová', 'rezervace_theme' ),
			'slug'  => 'cream',
			'color'	=> '#EEE7D6',
        ],
        [
            'name'  => __( 'Rudá', 'rezervace_theme' ),
			'slug'  => 'red',
			'color'	=> '#E92F3D',
        ],
        [
            'name'  => __( 'Zelená', 'rezervace_theme' ),
			'slug'  => 'green',
			'color' => '#00573F',
        ],
        [
            'name'  => __( 'Dark', 'rezervace_theme' ),
			'slug'  => 'black',
			'color' => '#000000',
        ],
    ];

    // Apply the color palette containing the original colors and 2 new colors:
    add_theme_support( 'editor-color-palette', $newColorPalette);
    // Disables color picker in block color palette.
    add_theme_support( 'disable-custom-colors' );
}
add_action( 'after_setup_theme', 'my_theme_add_new_features' );

/* / — / — / — / — / — / – / — / — / — / — / – */
/* / — / — / — / — / — / – / — / — / — / — / – */
/* KEEP TAGS ORDER AS INSERTED IN POST DETAILS */
/* / — / — / — / — / — / – / — / — / — / — / – */
/* / — / — / — / — / — / – / — / — / — / — / – */
require_once('includes/SlashAdmin.php');
$class = new TaxonomyOrder();

// Load Gutenberg Editor Styles
function custom_gutenberg_editor_styles() {
    wp_enqueue_style(
        'admin-styles',
        get_stylesheet_directory_uri().'/style-editor.css?v02-11-2023.01'
    );
}
add_action( 'admin_enqueue_scripts', 'custom_gutenberg_editor_styles' );

// add_post_type_support( 'post', 'page-attributes' );

/* WORK AROUND WP NASTY BUG PREPENDING AUTO TO SIZES ATTR */
add_filter(
        'wp_content_img_tag',
        static function ( $image ) {
                return str_replace( ' sizes="auto, ', ' sizes="', $image );
        }
);
add_filter(
        'wp_get_attachment_image_attributes',
        static function ( $attr ) {
                if ( isset( $attr['sizes'] ) ) {
                        $attr['sizes'] = preg_replace( '/^auto, /', '', $attr['sizes'] );
                }
                return $attr;
        }
);

add_action( 'wp_enqueue_scripts', 'rezervace_theme_scripts' );
function rezervace_theme_scripts() {
    wp_register_script('rezervace_theme_gsap', get_template_directory_uri() . '/assets/js/libs/gsap.min.js', array(), '1.0.1', true);
    wp_enqueue_script('rezervace_theme_gsap');
}

/* / — / — / — / — / — / – / — / — / — / — / – */
/* / — / — / — / — / — / – / — / — / — / — / – */
/* REZEŘVATION SYSTEM AND SCHEDULE RELATED */
/* / — / — / — / — / — / – / — / — / — / — / – */
/* / — / — / — / — / — / – / — / — / — / — / – */
/**/


/**
 * Register Instructors Custom Post Type
 */
function register_instructors_cpt() {
    $labels = [
        'name'                  => 'Instructors',
        'singular_name'         => 'Instructor',
        'menu_name'             => 'Instructors',
        'name_admin_bar'        => 'Instructor',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Instructor',
        'new_item'              => 'New Instructor',
        'edit_item'             => 'Edit Instructor',
        'view_item'             => 'View Instructor',
        'all_items'             => 'All Instructors',
        'search_items'          => 'Search Instructors',
        'not_found'             => 'No instructors found.',
        'not_found_in_trash'    => 'No instructors found in Trash.',
    ];

    $args = [
        'labels'                => $labels,
        'public'                => true,
        'publicly_queryable'    => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-groups', // or 'dashicons-businessperson'
        'query_var'             => true,
        'rewrite'               => ['slug' => 'instructor'],
        'capability_type'       => 'post',
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => 25,
        'supports'              => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'          => true, // important for Gutenberg + future API usage
    ];

    register_post_type('instructor', $args);
}

add_action('init', 'register_instructors_cpt');
/**
 * Register BARRE Lessons Custom Post Type
 * Used as base/template for lesson types
 */
function register_barre_lessons_cpt() {
    $labels = [
        'name'                  => 'Lesson Types',
        'singular_name'         => 'Lesson Type',
        'menu_name'             => 'Lesson Types',
        'name_admin_bar'        => 'Lesson Type',
        'add_new'               => 'Add New',
        'add_new_item'          => 'Add New Lesson Type',
        'new_item'              => 'New Lesson Type',
        'edit_item'             => 'Edit Lesson Type',
        'view_item'             => 'View Lesson Type',
        'all_items'             => 'All Lesson Types',
        'search_items'          => 'Search Lesson Types',
        'not_found'             => 'No lesson types found.',
        'not_found_in_trash'    => 'No lesson types found in Trash.',
    ];

    $args = [
        'labels'                => $labels,
        'public'                => false,               // not public - only admin use
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-calendar-alt',
        'query_var'             => false,
        'rewrite'               => false,
        'capability_type'       => 'post',
        'has_archive'           => false,
        'hierarchical'          => false,
        'menu_position'         => 26,
        'supports'              => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest'          => true,
    ];

    register_post_type('lesson_type', $args);
}

add_action('init', 'register_barre_lessons_cpt');



/**
 * Create custom Barre tables on theme activation
 */
function barre_create_custom_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $lessons_table = $wpdb->prefix . 'barre_lessons';
    $reservations_table = $wpdb->prefix . 'barre_reservations';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    // Lessons table
    $sql_lessons = "
    CREATE TABLE $lessons_table (
        id INT NOT NULL AUTO_INCREMENT,
        lesson_post_id INT NOT NULL,
        date DATE NOT NULL,
        start_time TIME NOT NULL,
        duration INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        instructor_id INT NOT NULL,
        capacity INT NOT NULL,
        used_spots INT NOT NULL DEFAULT 0,
        is_recurring TINYINT(1) NOT NULL DEFAULT 0,
        recurrence_rule VARCHAR(255) DEFAULT NULL,
        location VARCHAR(50) NOT NULL,
        PRIMARY KEY (id),
        KEY lesson_post_id (lesson_post_id),
        KEY instructor_id (instructor_id),
        KEY date (date)
    ) $charset_collate;
    ";

    // Reservations table
    $sql_reservations = "
    CREATE TABLE $reservations_table (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        lesson_id INT NOT NULL,
        num_persons INT NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'pending',
        payment_id VARCHAR(255) DEFAULT NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        cancelled_at datetime DEFAULT NULL,
        rescheduled_at datetime DEFAULT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY lesson_id (lesson_id),
        CONSTRAINT fk_lesson_id
            FOREIGN KEY (lesson_id)
            REFERENCES $lessons_table(id)
            ON DELETE CASCADE
    ) $charset_collate;
    ";

    dbDelta($sql_lessons);
    dbDelta($sql_reservations);
}
add_action('after_switch_theme', 'barre_create_custom_tables');



/**
 * Custom Admin Menu & Pages for BARRE Schedule Management
 * File: functions.php (or better - custom plugin)
 */

function barre_admin_menu() {

    // Main menu item - "BARRE Schedule"
    add_menu_page(
        'BARRE Schedule Management',      // Page title
        'BARRE Schedule',                 // Menu title
        'manage_options',                // Capability
        'barre-schedule',                 // Slug (main page)
        'barre_schedule_main_page',       // Callback function
        'dashicons-calendar-alt',        // Icon
        25                               // Position
    );

    // Submenu: Overview / Calendar (same as main, but can be different later)
    add_submenu_page(
        'barre-schedule',                 // Parent slug
        'Schedule Overview',             // Page title
        'Overview',                      // Menu title
        'manage_options',
        'barre-schedule',                 // Same slug → becomes the main page
        'barre_schedule_main_page'
    );

    // Submenu: Add New Lesson
    add_submenu_page(
        'barre-schedule',
        'Add New BARRE Lesson',
        'Add New',
        'manage_options',
        'barre-schedule-add',
        'barre_schedule_add_new_page'
    );

    // Optional: Stats / Reports
    add_submenu_page(
        'barre-schedule',
        'Schedule Statistics',
        'Statistics',
        'manage_options',
        'barre-schedule-stats',
        'barre_schedule_stats_page'
    );

    // In your admin menu setup
    add_submenu_page(
        'barre-schedule',
        'Edit Barre Lesson',
        'Edit Lesson',
        'manage_options',
        'barre-schedule-edit',
        'barre_schedule_edit_page'
    );

    // Settings subpage
    add_submenu_page(
        'barre-schedule', 
        'Schedule Settings', 
        'Settings',      
        'manage_options',
        'barre-schedule-settings', 
        'barre_schedule_settings_page'
    );
}

add_action('admin_menu', 'barre_admin_menu');

// ───────────────────────────────────────────────
//   MAIN SCHEDULE PAGE (Calendar + List)
// ───────────────────────────────────────────────

function barre_schedule_main_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Access denied.');
    }

    ?>
    <div class="wrap">
        <h1>BARRE Schedule — Overview</h1>

        <div class="barre-admin-controls">
            <a href="<?php echo admin_url('admin.php?page=barre-schedule-add'); ?>" class="page-title-action">
                + Add New Lesson
            </a>

            <div class="nav-controls">
                <button class="button" id="prev-week">← Předchozí týden</button>
                <span class="current-week-display" id="weekDisplay">Načítám...</span>
                <button class="button" id="this-week">Tento týden</button>
                <button class="button" id="next-week">Další týden →</button>
            </div>
        </div>

        <!-- <div id="barre-schedule-calendar" class="barre-admin-calendar"> -->
        <div id="barre-admin-calendar" class="barre-admin-calendar">
            <!-- Loaded via AJAX -->
            <p class="loading">Loading schedule...</p>
        </div>

        <div id="barre-quick-actions">
            <h3>Quick Actions</h3>
            <p>Click on any lesson to edit | Right-click for options (later)</p>
        </div>
    </div>

    <?php
    // Enqueue admin styles & scripts only on this page
    /*
    */
    wp_enqueue_script('barre-admin-schedule', get_stylesheet_directory_uri() . '/assets/js/admin-schedule.js', ['jquery'], '1.2', true);
    wp_localize_script('barre-admin-schedule', 'barreAdminAjax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('barre_admin_schedule_nonce')
    ]);
    wp_enqueue_style('barre-admin-style', get_stylesheet_directory_uri() . '/assets/css/admin-schedule.css');
}


// ───────────────────────────────────────────────
//   SCHEDULE SETTINGS PAGE
// ───────────────────────────────────────────────

function barre_schedule_settings_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Access denied.');
    }
    // GLOBAL SETTINGS FOR NUM SPOTS LIMITS, SECRET AND PRIVATE KEYS, ETC.
    ?>
     <div class="wrap">
        <h1>BARRE Settings</h1>
    </div>
    <?php
}

// ───────────────────────────────────────────────
//   ADD NEW LESSON PAGE
// ───────────────────────────────────────────────

function barre_schedule_add_new_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Access denied.');
    }

    // Handle form submit (very basic example)
    // Inside your barre_schedule_add_new_page() function – after the form handling check

    if (isset($_POST['barre_add_lesson']) && check_admin_referer('barre_add_lesson_action')) {
        // Sanitize and validate inputs
        $lesson_type_id = isset($_POST['lesson_type_id']) ? intval($_POST['lesson_type_id']) : 0;
        $location       = isset($_POST['location'])       ? sanitize_text_field($_POST['location']) : '';
        $lesson_date    = isset($_POST['lesson_date'])    ? sanitize_text_field($_POST['lesson_date']) : '';
        $start_time     = isset($_POST['start_time'])     ? sanitize_text_field($_POST['start_time']) : '';
        $duration       = isset($_POST['duration'])       ? intval($_POST['duration']) : 60;
        $instructor_id  = isset($_POST['instructor_id'])  ? intval($_POST['instructor_id']) : 0;
        $capacity       = isset($_POST['capacity'])       ? intval($_POST['capacity']) : 15;
        $price          = isset($_POST['price'])          ? floatval($_POST['price']) : 400.00;

        // Basic validation
        $errors = [];

        // Get lesson type name (for the `name` field in barre_lessons)
        $lesson_type = get_post($lesson_type_id);
        if (!$lesson_type || $lesson_type->post_type !== 'lesson_type') {
            $errors[] = 'Invalid lesson type.';
        }


        
        if (!empty($_POST['is_recurring'])) {

            $repeat_days = isset($_POST['repeat_days']) ? (array)$_POST['repeat_days'] : [];
            $recurrence_end = !empty($_POST['recurrence_end']) ? sanitize_text_field($_POST['recurrence_end']) : null;
            $exclude_dates_str = isset($_POST['exclude_dates']) ? sanitize_text_field($_POST['exclude_dates']) : '';
            $exclude_dates = array_map('trim', explode(',', $exclude_dates_str));

            if (empty($repeat_days)) {
                $errors[] = 'Select at least one day of week for repeating.';
            }

            if (!empty($errors)) {
                // show errors...
            } else {
                // Create master record first
                $master_data = [
                    'location'      => $location,
                    'date'          => $lesson_date,
                    'start_time'    => $start_time . ':00',
                    'duration'      => $duration,
                    'name'          => $lesson_type->post_title,
                    'instructor_id' => $instructor_id ?: null,
                    'capacity'      => $capacity,
                    'used_spots'    => 0,
                    'price'         => $price,
                    'is_recurring'  => 1,
                    'created_at'    => current_time('mysql')
                ];

                global $wpdb;
                $table = $wpdb->prefix . 'barre_lessons';

                $wpdb->insert($table, $master_data);
                $master_id = $wpdb->insert_id;

                // Generate future dates
                $current = new DateTime($lesson_date);
                $end_date = $recurrence_end ? new DateTime($recurrence_end) : null;

                $instances = 0;
                $max_weeks = 52 * 2; // safety limit ~2 years

                while ((!$end_date || $current <= $end_date) && $instances < 200) {
                    $current->modify('+1 day');
                    $day_name = strtolower($current->format('l'));

                    if (in_array($day_name, $repeat_days) && 
                        !in_array($current->format('Y-m-d'), $exclude_dates)) {
                        
                        $wpdb->insert($table, [
                            'location'      => $location,
                            'date'          => $current->format('Y-m-d'),
                            'start_time'    => $start_time . ':00',
                            'duration'      => $duration,
                            'name'          => $lesson_type->post_title,
                            'instructor_id' => $instructor_id ?: null,
                            'capacity'      => $capacity,
                            'used_spots'    => 0,
                            'price'         => $price,
                            'master_id'     => $master_id,
                            'created_at'    => current_time('mysql')
                        ]);

                        $instances++;
                    }
                }

                if ($instances > 0) {
                    $wpdb->update($table, ['recurrence_rule' => json_encode([
                        'days' => $repeat_days,
                        'end'  => $recurrence_end,
                        'exclusions' => $exclude_dates
                    ])], ['id' => $master_id]);

                    echo '<div class="notice notice-success"><p>Recurring series created! Generated ' . $instances . ' future lessons.</p></div>';
                }
            }
        } else {

            if (!$lesson_type_id) {
                $errors[] = 'Please select a lesson type.';
            }

            if (empty($lesson_date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $lesson_date)) {
                $errors[] = 'Invalid date.';
            }

            if (empty($start_time) || !preg_match('/^\d{2}:\d{2}$/', $start_time)) {
                $errors[] = 'Invalid start time.';
            }

            if ($duration < 15 || $duration > 240) {
                $errors[] = 'Duration must be between 15 and 240 minutes.';
            }

            if ($capacity < 1 || $capacity > 100) {
                $errors[] = 'Capacity must be between 1 and 100.';
            }

            if ($price < 0 || $price > 10000) {
                $errors[] = 'Price must be between 0 and 10,000.';
            }

            if (!empty($errors)) {
                // Show errors
                echo '<div class="notice notice-error">';
                foreach ($errors as $error) {
                    echo '<p>' . esc_html($error) . '</p>';
                }
                echo '</div>';
            } else {
                global $wpdb;
                $table = $wpdb->prefix . 'barre_lessons';

                $inserted = $wpdb->insert(
                    $table,
                    [
                        'location'      => $location,
                        'date'          => $lesson_date,
                        'start_time'    => $start_time . ':00', // ensure seconds
                        'duration'      => $duration,
                        'name'          => $lesson_type->post_title,
                        'instructor_id' => $instructor_id > 0 ? $instructor_id : null,
                        'capacity'      => $capacity,
                        'used_spots'    => 0,
                        'price'         => $price,
                        'created_at'    => current_time('mysql')
                    ],
                    [
                        '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%d', '%f', '%s'
                    ]
                );

                if ($inserted) {
                    echo '<div class="notice notice-success"><p>Barre lesson added successfully!</p></div>';
                    // Optional: clear form fields or redirect
                } else {
                    echo '<div class="notice notice-error"><p>Database error: ' . esc_html($wpdb->last_error) . '</p></div>';
                }
            }
        }
    } // END MAIN VALIDATION IF

    ?>
    <div class="wrap">
        <h1>Add New BARRE Lesson</h1>

        <form method="post" action="">
            <?php wp_nonce_field('barre_add_lesson_action'); ?>

            <table class="form-table">
                <tr>
                    <th><label>Lesson Type</label></th>
                    <td>
                        <?php
                        $lesson_types = get_posts([
                            'post_type'      => 'lesson_type',
                            'posts_per_page' => -1,
                            'orderby'        => 'title',
                            'order'          => 'ASC'
                        ]);
                        ?>
                        <select name="lesson_type_id" required>
                            <option value="">— Select Lesson Type —</option>
                            <?php foreach ($lesson_types as $type): ?>
                                <option value="<?= $type->ID ?>"><?= esc_html($type->post_title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Location</label></th>
                    <td>
                        <select name="location" required>
                            <option value="">— Select Location —</option>
                            <option value="LKBA-Holešovice">Holešovice</option>
                            <option value="LKBA-Vršovice">Vršovice</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Date</label></th>
                    <td><input type="date" name="lesson_date" required></td>
                </tr>
                <tr>
                    <th><label>Start Time</label></th>
                    <td><input type="time" name="start_time" required></td>
                </tr>
                <tr>
                    <th><label>Duration (minutes)</label></th>
                    <td><input type="number" name="duration" value="60" min="30" step="15"></td>
                </tr>
                <tr>
                    <th><label>Instructor</label></th>
                    <td>
                        <?php
                        $instructors = get_posts([
                            'post_type'      => 'instructor',
                            'posts_per_page' => -1,
                            'orderby'        => 'title'
                        ]);
                        ?>
                        <select name="instructor_id">
                            <option value="">— Any / None —</option>
                            <?php foreach ($instructors as $inst): ?>
                                <option value="<?= $inst->ID ?>"><?= esc_html($inst->post_title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Capacity</label></th>
                    <td><input type="number" name="capacity" value="15" min="1"></td>
                </tr>
                <tr>
                    <th><label>Price (CZK)</label></th>
                    <td><input type="number" name="price" value="400" min="0" step="50"></td>
                </tr>
                <tr>
                    <th><label for="is_recurring">Repeating Lesson</label></th>
                    <td>
                        <input type="checkbox" name="is_recurring" id="is_recurring" value="1">
                        <label for="is_recurring">This is a recurring series</label>
                    </td>
                </tr>

                <tr class="recurring-fields" style="display:none;">
                    <th><label>Repeat on days</label></th>
                    <td>
                        <?php
                        $days = ['monday'=>'Mon', 'tuesday'=>'Tue', 'wednesday'=>'Wed', 'thursday'=>'Thu', 
                                 'friday'=>'Fri', 'saturday'=>'Sat', 'sunday'=>'Sun'];
                        foreach ($days as $key => $label): ?>
                            <label>
                                <input type="checkbox" name="repeat_days[]" value="<?= $key ?>">
                                <?= $label ?>
                            </label>
                        <?php endforeach; ?>
                    </td>
                </tr>

                <tr class="recurring-fields" style="display:none;">
                    <th><label>Repeat until</label></th>
                    <td>
                        <input type="date" name="recurrence_end" id="recurrence_end">
                        <p class="description">Leave empty for indefinite repetition</p>
                    </td>
                </tr>

                <tr class="recurring-fields" style="display:none;">
                    <th><label>Exceptions (exclude dates)</label></th>
                    <td>
                        <textarea name="exclude_dates" rows="3" placeholder="2026-02-15, 2026-03-01, ..."></textarea>
                        <p class="description">Comma separated dates that should be skipped</p>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" name="barre_add_lesson" class="button button-primary" value="Add Lesson">
            </p>
        </form>
    </div>
    <script>
        jQuery(document).ready(function($) {
            $('#is_recurring').on('change', function() {
                $('.recurring-fields').toggle(this.checked);
            });
        });
    </script>
    <?php
}



// ───────────────────────────────────────────────
//   EDIT-SCHEDULE PAGE (Recurring and Single)
// ───────────────────────────────────────────────

function barre_schedule_edit_page() {
    
    // At the beginning of barre_schedule_edit_page()

    if (isset($_POST['barre_update_lesson']) && check_admin_referer('barre_edit_lesson_action')) {

        $lesson_id   = intval($_POST['lesson_id']);
        $master_id   = intval($_POST['master_id']);
        $edit_scope  = isset($_POST['edit_scope']) ? $_POST['edit_scope'] : 'single';

        // Sanitize new values (same as in add form)
        $new_date       = sanitize_text_field($_POST['date']);
        $new_start_time = sanitize_text_field($_POST['start_time']) . ':00';
        $new_duration   = intval($_POST['duration']);
        $new_capacity   = intval($_POST['capacity']);
        $new_price      = floatval($_POST['price']);
        $new_instructor = intval($_POST['instructor_id']) ?: null;

        $new_lesson_type_id = intval($_POST['lesson_type_id']);
        // Get lesson type name (for the `name` field in barre_lessons)
        $new_name = get_post($new_lesson_type_id);
        // $new_name       = sanitize_text_field($_POST['name']); // if editable

        global $wpdb;
        $table = $wpdb->prefix . 'barre_lessons';

        $where = ['id' => $lesson_id];
        $data  = [
            'date'          => $new_date,
            'start_time'    => $new_start_time,
            'duration'      => $new_duration,
            'capacity'      => $new_capacity,
            'price'         => $new_price,
            'instructor_id' => $new_instructor,
            'name'          => $new_name
        ];

        // 1. Single instance only
        if ($edit_scope === 'single' || !$master_id) {
            $wpdb->update($table, $data, $where);
            echo '<div class="notice notice-success"><p>Single lesson updated.</p></div>';
        }

        // 2. This and following
        elseif ($edit_scope === 'following') {
            $wpdb->update(
                $table,
                $data,
                [
                    'master_id' => $master_id,
                    'date'      => ['date' => $new_date, 'compare' => '>='],
                ],
                null,
                ['%d', '%s']
            );
            echo '<div class="notice notice-success"><p>Updated this lesson and all following occurrences.</p></div>';
        }

        // 3. All in series
        elseif ($edit_scope === 'all') {
            $wpdb->update(
                $table,
                $data,
                ['master_id' => $master_id],
                null,
                '%d'
            );
            // Also update master if needed
            $wpdb->update($table, $data, ['id' => $master_id]);

            echo '<div class="notice notice-success"><p><strong>All</strong> lessons in the series have been updated.</p></div>';
        }
    }
    if (!current_user_can('manage_options')) {
        wp_die('Access denied.');
    }

    $lesson_id = isset($_GET['lesson_id']) ? intval($_GET['lesson_id']) : 0;
    if (!$lesson_id) {
        wp_die('No lesson ID provided.');
    }

    global $wpdb;
    $table = $wpdb->prefix . 'barre_lessons';

    $lesson = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $table WHERE id = %d",
        $lesson_id
    ));

    if (!$lesson) {
        wp_die('Lesson not found.');
    }

    $is_recurring = $lesson->is_recurring;
    $master_id    = $lesson->master_id ?: $lesson_id;
    $is_master    = ($lesson->id === $master_id);

    // Get all future instances of the series (for statistics/warning)
    $future_count = 0;
    if ($is_recurring || $is_master) {
        $future_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table 
             WHERE (id = %d OR master_id = %d) 
             AND date >= CURDATE()",
            $master_id, $master_id
        ));
    }

    ?>
    <div class="wrap">
        <h1>Edit Barre Lesson</h1>

        <?php if ($future_count > 1): ?>
            <div class="notice notice-warning">
                <p><strong>Warning:</strong> This is part of a recurring series 
                (<?= $future_count ?> future occurrences remaining).</p>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('barre_edit_lesson_action'); ?>
            <input type="hidden" name="lesson_id" value="<?= $lesson_id ?>">
            <input type="hidden" name="master_id" value="<?= $master_id ?>">

            <!-- Basic fields (same as add form) -->
            <table class="form-table">
                <tr>
                    <th><label>Date</label></th>
                    <td><input type="date" name="date" value="<?= esc_attr($lesson->date) ?>" required></td>
                </tr>
                <tr>
                    <th><label>Start Time</label></th>
                    <td><input type="time" name="start_time" value="<?= substr($lesson->start_time, 0, 5) ?>" required></td>
                </tr>
                <!-- ... other fields: duration, instructor, capacity, price, name ... -->
                <tr>
                    <th><label>Lesson Type</label></th>
                    <td>
                        <?php
                        $lesson_types = get_posts([
                            'post_type'      => 'lesson_type',
                            'posts_per_page' => -1,
                            'orderby'        => 'title',
                            'order'          => 'ASC'
                        ]);
                        ?>
                        <select name="lesson_type_id" required>
                            <option value="">— Select Lesson Type —</option>
                            <?php foreach ($lesson_types as $type): ?>
                                <option value="<?= $type->ID ?>"><?= esc_html($type->post_title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Location</label></th>
                    <td>
                        <select name="location" required>
                            <option value="">— Select Location —</option>
                            <option value="LKBA-Holešovice">Holešovice</option>
                            <option value="LKBA-Vršovice">Vršovice</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Duration (minutes)</label></th>
                    <td><input type="number" name="duration" value="60" min="30" step="15"></td>
                </tr>
                <tr>
                    <th><label>Instructor</label></th>
                    <td>
                        <?php
                        $instructors = get_posts([
                            'post_type'      => 'instructor',
                            'posts_per_page' => -1,
                            'orderby'        => 'title'
                        ]);
                        ?>
                        <select name="instructor_id">
                            <option value="">— Any / None —</option>
                            <?php foreach ($instructors as $inst): ?>
                                <option value="<?= $inst->ID ?>"><?= esc_html($inst->post_title) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label>Capacity</label></th>
                    <td><input type="number" name="capacity" value="15" min="1"></td>
                </tr>
                <tr>
                    <th><label>Price (CZK)</label></th>
                    <td><input type="number" name="price" value="400" min="0" step="50"></td>
                </tr>
            </table>

            <?php if ($future_count > 1): ?>
                <h3>How to apply these changes?</h3>
                <fieldset>
                    <label>
                        <input type="radio" name="edit_scope" value="single" checked>
                        <strong>Only this lesson</strong> (<?= date_i18n('d.m.Y', strtotime($lesson->date)) ?>)
                    </label><br><br>

                    <label>
                        <input type="radio" name="edit_scope" value="following">
                        <strong>This and all following</strong> (<?= $future_count - 1 ?> future lessons)
                    </label><br><br>

                    <label>
                        <input type="radio" name="edit_scope" value="all">
                        <strong>All lessons in this series</strong> (including past — <?= $future_count ?> total)
                        <span style="color:#dc3545; font-weight:bold;">(dangerous!)</span>
                    </label>
                </fieldset>
            <?php endif; ?>

            <p class="submit">
                <input type="submit" name="barre_update_lesson" class="button button-primary" value="Update Lesson">
            </p>
        </form>
    </div>
    <?php
}

// Optional: Statistics page stub
function barre_schedule_stats_page() {
    ?>
    <div class="wrap">
        <h1>Schedule Statistics</h1>
        <p>Coming soon: occupancy %, revenue, popular classes...</p>
    </div>
    <?php
}


/**
 * Helper function: Render week schedule as HTML table (admin version)
 */
/**/
function barre_admin_render_week_table($lessons_by_date, $from_date) {
    $html = '<table class="barre-admin-schedule-table widefat fixed striped">';
    $html .= '<thead><tr>';
    $html .= '<th>Time</th>';

    $current = new DateTime($from_date);
    for ($i = 0; $i < 7; $i++) {
        $day_label = $current->format('D d.m');
        $html .= "<th>{$day_label}</th>";
        $current->modify('+1 day');
    }
    $html .= '</tr></thead><tbody>';

    // Example time slots (you can make this configurable)
    // $time_slots = [
    //     '06:00', '07:00', '08:00', '09:00', '10:00', '11:00',
    //     '16:00', '17:00', '18:00', '19:00', '20:00'
    // ];
    $time_slots = [
        '06:00', '06:15', '06:30', '06:45', '07:00', '07:15', '07:30', '07:45', '08:00', '08:15', '08:30', '08:45', '09:00', '09:15', '09:30', '09:45', '10:00', '10:15', '10:30', '10:45', '11:00', '11:15', '11:30', '11:45', '16:00', '16:15', '16:30', '16:45', '17:00', '17:15', '17:30', '17:45', '18:00', '18:15', '18:30', '18:45', '19:00', '19:15', '19:30', '19:45', '20:00', '20:15', '20:30', '20:45'
    ];

    foreach ($time_slots as $time) {
        $html .= '<tr>';
        $html .= "<td class='time-column'>{$time}</td>";

        $current = new DateTime($from_date);
        for ($d = 0; $d < 7; $d++) {
            $date_str = $current->format('Y-m-d');
            $lessons = $lessons_by_date[$date_str] ?? [];

            $slot_html = '<td class="slot empty">—</td>';

            foreach ($lessons as $lesson) {
                if (substr($lesson['start_time'], 0, 5) === $time) {
                    $status_class = $lesson['available'] <= 0 ? 'full' :
                                   ($lesson['available'] <= 3 ? 'almost-full' : 'available');

                    $occupancy = $lesson['occupancy'];

                    $actions = sprintf(
                        '<div class="row-actions">' .
                        '<a href="%s">Edit</a> | ' .
                        ($lesson['can_delete']
                            ? '<a href="#" class="delete-lesson" data-id="%d" data-nonce="%s">Delete</a>'
                            : '<span>Delete</span>') .
                        '</div>',
                        esc_url($lesson['edit_url']),
                        $lesson['id'],
                        esc_attr($lesson['delete_nonce'])
                    );

                    $slot_html = sprintf(
                        '<td class="slot %s">' .
                        '<strong>%s</strong><br>' .
                        '<span class="instructor">%s</span><br>' .
                        '<span class="occupancy">%d / %d (%s)</span><br>' .
                        '<span class="price">%s</span>' .
                        '%s' .
                        '</td>',
                        esc_attr($status_class),
                        esc_html($lesson['name']),
                        esc_html($lesson['instructor']),
                        $lesson['used'], $lesson['capacity'], $occupancy,
                        esc_html($lesson['price_formatted']),
                        $actions
                    );
                    break;
                }
            }

            $html .= $slot_html;
            $current->modify('+1 day');
        }

        $html .= '</tr>';
    }

    $html .= '</tbody></table>';

    return $html;
}
/**
 * AJAX Handler: Admin schedule with pre-rendered HTML
 */
/**/
function barre_admin_load_schedule_ajax() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(['message' => 'Insufficient permissions']);
    }

    check_ajax_referer('barre_admin_schedule_nonce', '_ajax_nonce');

    $from_date = isset($_POST['from_date']) ? sanitize_text_field($_POST['from_date']) : '';
    $to_date   = isset($_POST['to_date'])   ? sanitize_text_field($_POST['to_date'])   : '';

    // Default to current week if missing
    if (!$from_date || !$to_date) {
        $monday = strtotime('monday this week');
        $from_date = date('Y-m-d', $monday);
        $to_date   = date('Y-m-d', strtotime('+6 days', $monday));
    }

    global $wpdb;
    $table = $wpdb->prefix . 'barre_lessons';

    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT id, date, start_time, duration, name, instructor_id, 
                capacity, location, used_spots, price
         FROM $table 
         WHERE date BETWEEN %s AND %s
         ORDER BY date ASC, start_time ASC",
        $from_date,
        $to_date
    ), ARRAY_A);

    $lessons_by_date = [];

    foreach ($results as $row) {
        $instructor_name = '—';
        if ($row['instructor_id']) {
            $instructor = get_post($row['instructor_id']);
            $instructor_name = $instructor ? $instructor->post_title : '—';
        }

        $date_key = $row['date'];

        $lessons_by_date[$date_key][] = [
            'id'              => (int)$row['id'],
            'name'            => esc_html($row['name']),
            'start_time'      => substr($row['start_time'], 0, 5),
            'instructor'      => esc_html($instructor_name),
            'capacity'        => (int)$row['capacity'],
            'used'            => (int)$row['used_spots'],
            'available'       => (int)$row['capacity'] - (int)$row['used_spots'],
            'occupancy'       => $row['capacity'] > 0 
                ? round(($row['used_spots'] / $row['capacity']) * 100, 1) . '%' : '—',
            'location'        => esc_html($row['location']),
            'price_formatted' => number_format($row['price'], 0) . ' Kč',
            'edit_url'        => admin_url("admin.php?page=barre-schedule-edit&lesson_id={$row['id']}"),
            'delete_nonce'    => wp_create_nonce("delete_barre_lesson_{$row['id']}"),
            'can_delete'      => ($row['used_spots'] == 0)
        ];
    }

    // Generate HTML on server
    $html = barre_admin_render_week_table($lessons_by_date, $from_date);

    wp_send_json_success([
        'html'          => $html,
        'from_date'     => $from_date,
        'to_date'       => $to_date,
        'week_range'    => date_i18n('j. M', strtotime($from_date)) . ' – ' .
                           date_i18n('j. M Y', strtotime($to_date)),
        'total_lessons' => count($results)
    ]);
}

add_action('wp_ajax_barre_admin_load_schedule', 'barre_admin_load_schedule_ajax');

/**
————————————————————————————————————————————————————
// FRONT-END > SCHEDULE & MY RESERVATION PAGES
————————————————————————————————————————————————————

function enqueue_barre_schedule_scripts() {
  
    // Only on pages where you need it
    // if (is_page_template('template-schedule.php') || has_shortcode(get_post()->post_content, 'barre_schedule')) {
    if (is_page_template('page-schedule-template.php') || has_shortcode(get_post()->post_content, 'barre_schedule')) {
        wp_enqueue_script(
            'barre-schedule-js',
            get_stylesheet_directory_uri() . '/assets/js/schedule.js',
            [],
            '1.0.2',
            true
        );

        wp_localize_script(
            'barre-schedule-js',
            'barreAjax',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                // 'nonce'   => wp_create_nonce('barre_schedule_nonce'),
                'nonce'   => wp_create_nonce('barre-ajax-nonce'),
                // PASS PHP VARIABLE TO JS
                'example_variable' => 'Hello, world NOW!'
            ]
        );
    }

    wp_enqueue_style(
        'barre-schedule-css',
        get_stylesheet_directory_uri() . '/assets/css/schedule.css',
        [],
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'enqueue_barre_schedule_scripts');
*/

/**
 * AJAX Handler: Load barre lessons for selected week
 */
function barre_load_schedule_ajax_handler() {
    // check_ajax_referer('barre_schedule_nonce', '_ajax_nonce');
    // check_ajax_referer('barre-ajax-nonce', '_ajax_nonce');
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    $from_date = isset($_POST['from_date']) ? sanitize_text_field($_POST['from_date']) : '';
    $to_date   = isset($_POST['to_date'])   ? sanitize_text_field($_POST['to_date'])   : '';

    if (!$from_date || !$to_date) {
        wp_send_json_error(['message' => 'Missing date range']);
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'barre_lessons';

    $query = $wpdb->prepare(
        "SELECT 
            id,
            date,
            start_time,
            duration,
            name,
            instructor_id,
            capacity,
            used_spots AS used,
            price
        FROM $table_name 
        WHERE date BETWEEN %s AND %s
        ORDER BY date ASC, start_time ASC",
        $from_date,
        $to_date
    );

    $results = $wpdb->get_results($query, ARRAY_A);

    // Group by date
    $lessons_by_date = [];

    foreach ($results as $row) {
        // Get instructor name
        $instructor_name = 'Unknown';
        if ($row['instructor_id']) {
            $instructor_post = get_post($row['instructor_id']);
            if ($instructor_post) {
                $instructor_name = $instructor_post->post_title;
            }
        }

        $date_key = $row['date'];

        $lessons_by_date[$date_key][] = [
            'id'            => (int)$row['id'],
            'name'          => $row['name'],
            'start_time'    => substr($row['start_time'], 0, 5), // HH:MM
            'end_time'      => date('H:i', strtotime($row['start_time'] . " +{$row['duration']} minutes")),
            'instructor'    => $instructor_name,
            // 'capacity'      => (int)$row['capacity'],
            // 'used'          => (int)$row['used'],
            'used'      => (int)$row['used_spots'],
            'capacity'  => (int)$row['capacity'],
            'available' => (int)$row['capacity'] - (int)$row['used_spots'],
            'price'         => floatval($row['price'])
        ];
    }

    wp_send_json_success([
        'lessons' => $lessons_by_date
    ]);
}

add_action('wp_ajax_barre_load_schedule', 'barre_load_schedule_ajax_handler');
add_action('wp_ajax_nopriv_barre_load_schedule', 'barre_load_schedule_ajax_handler');

/**
————————————————————————————————————————————————————
// CHECKOUT NONCE AND AJAX SCRIPT
————————————————————————————————————————————————————
*/
/*
function enqueue_barre_checkout_scripts() {
  
    // Only on pages where you need it
    if (is_page_template('page-checkout-template.php') || has_shortcode(get_post()->post_content, 'barre_checkout')) {
        wp_enqueue_script(
            'barre-checkout-js',
            get_stylesheet_directory_uri() . '/assets/js/checkout.js',
            [],
            '1.0.2',
            true
        );

        wp_localize_script(
            'barre-checkout-js',
            'barreCheckoutAjax',
            [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('barre_checkout_nonce'),
                // PASS PHP VARIABLE TO JS
                'example_variable' => 'Hello, checkout NOW!'
            ]
        );

        wp_enqueue_style(
            'barre-checkout-css',
            get_stylesheet_directory_uri() . '/assets/css/checkout.css',
            [],
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'enqueue_barre_checkout_scripts');
*/
/**
————————————————————————————————————————————————————
// STORE BASKET ON SERVER
————————————————————————————————————————————————————
*/

add_action('wp_ajax_barre_sync_basket_to_server', 'barre_sync_basket_to_server');

function barre_sync_basket_to_server() {
    // check_ajax_referer('barre-ajax-nonce', '_ajax_nonce');
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    
    if (!is_user_logged_in()) {
        error_log('AJAX called but user NOT logged in');
        wp_send_json_error(['message' => 'Session expired - please log in again']);
    }

    $basket_json = isset($_POST['basket']) ? wp_unslash($_POST['basket']) : '';
    $basket = json_decode($basket_json, true);

    if (!is_array($basket) || empty($basket)) {
        wp_send_json_error(['message' => 'Basket is empty or invalid.']);
    }

    // Store in user meta (persistent across pages/sessions)
    update_user_meta(get_current_user_id(), 'barre_temp_basket', $basket);

    // Also store in session as fallback
    $_SESSION['barre_basket'] = json_encode($basket);

    wp_send_json_success();
}

/*
function barre_enqueue_checkout_scripts() {
    // Only on checkout page
    if (is_page_template('page-checkout-template.php') || is_page('checkout')) {  // adjust slug if needed

        // Enqueue the same JS file you use for schedule/basket
        wp_enqueue_script(
            'barre-checkout-js',
            get_stylesheet_directory_uri() . '/assets/js/checkout.js',  // or schedule.js if shared
            ['jquery'],
            '1.0.5',
            true
        );

        // Localize barreAjax (same as on schedule page)
        wp_localize_script('barre-checkout-js', 'barreAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('barre_nonce')   // use same nonce name
        ]);

        // Optional: enqueue modal CSS if not already global
        

        wp_enqueue_style(
            'barre-checkout-css',
            get_stylesheet_directory_uri() . '/assets/css/checkout.css',
            [],
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'barre_enqueue_checkout_scripts');
*/

function barre_enqueue_frontend_scripts() {
    // Always load shared core
    wp_enqueue_script(
        'barre-shared-js',
        get_stylesheet_directory_uri() . '/assets/js/shared-frontend.js',
        [],
        '1.0.3',
        true
    );

    // Page-specific
    if (is_page_template('page-template-schedule.php') || is_page('schedule')) {
        wp_enqueue_script('barre-schedule-js', get_stylesheet_directory_uri() . '/assets/js/schedule.js', ['jquery', 'barre-shared-js'], '1.0', true);
        wp_localize_script('barre-schedule-js', 'barreAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('barre_nonce')
        ]);
        // CSS
        // wp_enqueue_style(
        //     'barre-schedule-css',
        //     get_stylesheet_directory_uri() . '/assets/css/schedule.css',
        //     [],
        //     '1.0.0'
        // );
    }

    if (is_page_template('page-checkout-template.php') || is_page('checkout')) {
        wp_enqueue_script('barre-checkout-js', get_stylesheet_directory_uri() . '/assets/js/checkout.js', ['jquery', 'barre-shared-js'], '1.0', true);
        wp_localize_script('barre-checkout-js', 'barreAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('barre_nonce')
        ]);
        // CSS
        // wp_enqueue_style(
        //     'barre-checkout-css',
        //     get_stylesheet_directory_uri() . '/assets/css/checkout.css',
        //     [],
        //     '1.0.0'
        // );
    }

    if (is_page_template('page-my-reservations.php') || is_page('my-reservations')) {
        wp_enqueue_script('barre-reservations-js', get_stylesheet_directory_uri() . '/assets/js/my-reservations.js', ['jquery', 'barre-shared-js'], '1.0', true);
        wp_localize_script('barre-reservations-js', 'barreAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('barre_nonce')
        ]);
        // CSS
        // wp_enqueue_style(
        //     'barre-reservations-css',
        //     get_stylesheet_directory_uri() . '/assets/css/my-reservations.css',
        //     [],
        //     '1.0.0'
        // );
    }
    // CSS
        wp_enqueue_style(
            'barre-schedule-css',
            get_stylesheet_directory_uri() . '/assets/css/schedule.css',
            [],
            '1.0.0'
        );
// CSS
        wp_enqueue_style(
            'barre-checkout-css',
            get_stylesheet_directory_uri() . '/assets/css/checkout.css',
            [],
            '1.0.0'
        );
// CSS
        wp_enqueue_style(
            'barre-reservations-css',
            get_stylesheet_directory_uri() . '/assets/css/my-reservations.css',
            [],
            '1.0.0'
        );
    /*
        */
}
add_action('wp_enqueue_scripts', 'barre_enqueue_frontend_scripts');
/**
————————————————————————————————————————————————————
// RESERVATION SCRIPTS AND STYLES
————————————————————————————————————————————————————

function barre_enqueue_myreservations_scripts() {
    if (is_page_template('page-my-reservations.php') || is_page('my-reservations')) {  // adjust slug if needed
        // Enqueue the same JS file you use for schedule/basket
        wp_enqueue_script(
            'barre-reservations-js',
            get_stylesheet_directory_uri() . '/assets/js/my-reservations.js',  // or schedule.js if shared
            ['jquery'],
            '1.0.5',
            true
        );
        // Localize barreAjax (same as on schedule page)
        wp_localize_script('barre-reservations-js', 'barreAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('barre_nonce')   // use same nonce name
        ]);

        wp_enqueue_style(
            'barre-reservations-css',
            get_stylesheet_directory_uri() . '/assets/css/my-reservations.css',
            [],
            '1.0.0'
        );
    }
}
add_action('wp_enqueue_scripts', 'barre_enqueue_myreservations_scripts');
*/

/**
————————————————————————————————————————————————————
// BACKEND – RESERVATION CANCEL HANDLER
————————————————————————————————————————————————————
*/
add_action('wp_ajax_barre_cancel_reservation', 'barre_cancel_reservation');

function barre_cancel_reservation() {
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    $res_id = isset($_POST['reservation_id']) ? intval($_POST['reservation_id']) : 0;
    if (!$res_id) {
        wp_send_json_error(['message' => 'Invalid reservation ID']);
    }

    global $wpdb;
    $res_table   = $wpdb->prefix . 'barre_reservations';
    $lessons_tbl = $wpdb->prefix . 'barre_lessons';

    // Get reservation
    $res = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $res_table WHERE id = %d AND user_id = %d AND status = 'confirmed'",
        $res_id,
        get_current_user_id()
    ));

    if (!$res) {
        wp_send_json_error(['message' => 'Reservation not found or not yours']);
    }

    // Check time policy (example: >24h before)
    $lesson = $wpdb->get_row($wpdb->prepare(
        "SELECT CONCAT(date, ' ', start_time) AS start_dt FROM $lessons_tbl WHERE id = %d",
        $res->lesson_id
    ));

    $start_time = strtotime($lesson->start_dt);
    if ($start_time < (time() + 24 * 3600)) {
        wp_send_json_error(['message' => 'Too late to cancel (less than 24 hours remaining)']);
    }

    // Cancel
    $wpdb->update($res_table, [
        'status'       => 'cancelled',
        'cancelled_at' => current_time('mysql')
    ], ['id' => $res_id]);

    // Decrease used spots
    $wpdb->query($wpdb->prepare(
        "UPDATE $lessons_tbl SET used_spots = used_spots - %d WHERE id = %d",
        $res->num_persons,
        $res->lesson_id
    ));

    wp_send_json_success(['message' => 'Reservation cancelled successfully']);
}

/**
————————————————————————————————————————————————————
// PAYMENT SIMULATION
————————————————————————————————————————————————————
*/
add_action('wp_ajax_barre_simulate_payment', 'barre_simulate_payment_handler');

function barre_simulate_payment_handler() {
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'User not logged in']);
    }

    $user_id = get_current_user_id();
    $basket = get_user_meta($user_id, 'barre_temp_basket', true);

    if (!is_array($basket) || empty($basket)) {
        wp_send_json_error(['message' => 'Basket empty']);
    }

    global $wpdb;
    $lessons_tbl = $wpdb->prefix . 'barre_lessons';
    $res_tbl     = $wpdb->prefix . 'barre_reservations';

    $user_id = get_current_user_id();
    $fake_payment_id = 'sim_' . uniqid(); // fake ID for testing

    $wpdb->query('START TRANSACTION');

    try {
        foreach ($basket as $item) {
            // Re-check capacity
            $lesson = $wpdb->get_row($wpdb->prepare(
                "SELECT capacity, used_spots FROM $lessons_tbl WHERE id = %d",
                $item['lessonId']
            ));

            if (!$lesson || ($lesson->capacity - $lesson->used_spots) < $item['persons']) {
                throw new Exception("Capacity exceeded for lesson {$item['lessonId']}");
            }

            // Insert reservation
            $wpdb->insert($res_tbl, [
                'user_id'     => $user_id,
                'lesson_id'   => $item['lessonId'],
                'num_persons' => $item['persons'],
                'status'      => 'confirmed',
                'payment_id'  => $fake_payment_id,
                'created_at'  => current_time('mysql')
            ]);

            // Update spots
            $wpdb->query($wpdb->prepare(
                "UPDATE $lessons_tbl SET used_spots = used_spots + %d WHERE id = %d",
                $item['persons'],
                $item['lessonId']
            ));
        }

        $wpdb->query('COMMIT');

        // After successful processing, clean up
        // Clear basket
        delete_user_meta($user_id, 'barre_temp_basket');
        unset($_SESSION['barre_basket']);

        // Simulate email (or call real function)
        barre_send_confirmation_email($user_id, $basket, (object)['payment_intent' => $fake_payment_id]);

        wp_send_json_success();
        // wp_send_json_success([
        //     'message'          => 'Reservation completed',
        //     'clear_client_basket' => true   // ← new flag
        // ]);

    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}

/**
————————————————————————————————————————————————————
// AJAX PRE-CHECK HANDLER
————————————————————————————————————————————————————
/*
add_action('wp_ajax_barre_check_basket_before_checkout', 'barre_check_basket_before_checkout');

function barre_check_basket_before_checkout() {
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Please log in to proceed to checkout.']);
    }

    // You can add more checks (e.g. spots still available, etc.)
    wp_send_json_success();
}
*/


/**
————————————————————————————————————————————————————
// RESCHEDULE
————————————————————————————————————————————————————
*/

add_action('wp_ajax_barre_load_available_slots_reschedule', 'barre_load_available_slots_reschedule');

function barre_load_available_slots_reschedule() {
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    $from = sanitize_text_field($_POST['from_date'] ?? '');
    $to   = sanitize_text_field($_POST['to_date'] ?? '');

    if (!$from || !$to) {
        wp_send_json_error(['message' => 'Missing dates']);
    }

    global $wpdb;
    $tbl = $wpdb->prefix . 'barre_lessons';

    $slots = $wpdb->get_results($wpdb->prepare(
        "SELECT id, name, date, start_time, duration, instructor_id, capacity, used_spots
         FROM $tbl
         WHERE date BETWEEN %s AND %s
           AND date > CURDATE()
         ORDER BY date, start_time",
        $from,
        $to
    ), ARRAY_A);

    $by_date = [];
    foreach ($slots as $s) {
        $avail = $s['capacity'] - $s['used_spots'];
        if ($avail < 1) continue; // skip full

        $instructor = $s['instructor_id'] ? get_the_title($s['instructor_id']) : '—';

        $by_date[$s['date']][] = [
            'id'          => $s['id'],
            'name'        => $s['name'],
            'start_time'  => substr($s['start_time'], 0, 5),
            'instructor'  => $instructor,
            'capacity'    => $s['capacity'],
            'available'   => $avail
        ];
    }

    wp_send_json_success([
        'slots'      => $by_date,
        'week_range' => date_i18n('j. M', strtotime($from)) . ' – ' . date_i18n('j. M Y', strtotime($to))
    ]);
}

add_action('wp_ajax_barre_reschedule_reservation', 'barre_reschedule_reservation');

function barre_reschedule_reservation() {
    check_ajax_referer('barre_nonce', '_ajax_nonce');
    // error_log('Reschedule called – nonce check skipped for debug');

    $res_id     = intval($_POST['reservation_id'] ?? 0);
    $new_lesson = intval($_POST['new_lesson_id'] ?? 0);

    if (!$res_id || !$new_lesson) {
        wp_send_json_error(['message' => 'Missing parameters']);
    }

    global $wpdb;
    $res_tbl = $wpdb->prefix . 'barre_reservations';
    $les_tbl = $wpdb->prefix . 'barre_lessons';

    $res = $wpdb->get_row($wpdb->prepare(
        "SELECT * FROM $res_tbl WHERE id = %d AND user_id = %d AND status = 'confirmed'",
        $res_id, get_current_user_id()
    ));

    if (!$res) {
        wp_send_json_error(['message' => 'Reservation not found']);
    }

    // Check time window (example: >24h before original)
    $old_lesson = $wpdb->get_row($wpdb->prepare(
        "SELECT CONCAT(date,' ',start_time) AS dt FROM $les_tbl WHERE id = %d",
        $res->lesson_id
    ));

    if (strtotime($old_lesson->dt) < time() + 24*3600) {
        wp_send_json_error(['message' => 'Too late to reschedule']);
    }

    // Check new slot availability
    $new = $wpdb->get_row($wpdb->prepare(
        "SELECT capacity, used_spots FROM $les_tbl WHERE id = %d AND date > CURDATE()",
        $new_lesson
    ));

    if (!$new || $new->capacity - $new->used_spots < $res->num_persons) {
        wp_send_json_error(['message' => 'Not enough spots in selected slot']);
    }
    


    // Perform reschedule
    $wpdb->query('START TRANSACTION');

    try {
        // Decrease old
        $wpdb->query($wpdb->prepare(
            "UPDATE $les_tbl SET used_spots = used_spots - %d WHERE id = %d",
            $res->num_persons, $res->lesson_id
        ));

        // Increase new
        $wpdb->query($wpdb->prepare(
            "UPDATE $les_tbl SET used_spots = used_spots + %d WHERE id = %d",
            $res->num_persons, $new_lesson
        ));

        // Update reservation
        $wpdb->update($res_tbl, [
            'lesson_id' => $new_lesson,
            'rescheduled_at' => current_time('mysql')
        ], ['id' => $res_id]);


        $wpdb->query('COMMIT');
        wp_send_json_success(['message' => 'Successfully rescheduled!']);

    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        wp_send_json_error(['message' => 'Failed to reschedule']);
    }
}

/**
————————————————————————————————————————————————————
// VIEW RESERVATION DETAILS
————————————————————————————————————————————————————
*/

add_action('wp_ajax_barre_get_reservation_details', 'barre_get_reservation_details');

function barre_get_reservation_details() {
    check_ajax_referer('barre_nonce', '_ajax_nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'Not logged in']);
    }

    $res_id = intval($_POST['reservation_id'] ?? 0);
    if (!$res_id) {
        wp_send_json_error(['message' => 'Invalid reservation ID']);
    }

    global $wpdb;
    $res_tbl = $wpdb->prefix . 'barre_reservations';
    $les_tbl = $wpdb->prefix . 'barre_lessons';

    $res = $wpdb->get_row($wpdb->prepare(
        "SELECT r.*, l.name, l.date, l.start_time, l.duration, l.price, l.instructor_id
         FROM $res_tbl r
         INNER JOIN $les_tbl l ON r.lesson_id = l.id
         WHERE r.id = %d AND r.user_id = %d",
        $res_id,
        get_current_user_id()
    ), ARRAY_A);

    if (!$res) {
        wp_send_json_error(['message' => 'Reservation not found']);
    }

    // Format everything here – so JS gets ready-to-use values
    $instructor_name = $res['instructor_id'] ? get_the_title($res['instructor_id']) : '—';

    $response_data = [
        'id'                     => $res_id,
        'name'                   => esc_html($res['name']),
        'date_formatted'         => date_i18n('l, d.m.Y H:i', strtotime($res['date'] . ' ' . $res['start_time'])),
        'duration'               => $res['duration'],
        'instructor'             => esc_html($instructor_name),
        'num_persons'            => $res['num_persons'],
        'price_formatted'        => number_format($res['price'], 0),
        'total_formatted'        => number_format($res['price'] * $res['num_persons'], 0),
        'created_at_formatted'   => date_i18n('d.m.Y H:i', strtotime($res['created_at'])),
        'cancelled_at_formatted' => $res['cancelled_at'] ? date_i18n('d.m.Y H:i', strtotime($res['cancelled_at'])) : '',
        'rescheduled_at_formatted'=> $res['rescheduled_at'] ? date_i18n('d.m.Y H:i', strtotime($res['rescheduled_at'])) : ''
    ];

    wp_send_json_success($response_data);
}




/**
————————————————————————————————————————————————————
// BACK-END > PROCESS BASKET & CREATE STRIPE SESSION
————————————————————————————————————————————————————
*/

add_action('wp_ajax_barre_process_basket', 'barre_process_basket_handler');

function barre_process_basket_handler() {
    check_ajax_referer('barre_checkout_nonce', '_ajax_nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'User must be logged in']);
    }

    $basket_json = isset($_POST['basket']) ? wp_unslash($_POST['basket']) : '';
    $basket = json_decode($basket_json, true);

    if (empty($basket) || !is_array($basket)) {
        wp_send_json_error(['message' => 'Invalid basket data']);
    }

    global $wpdb;
    $lessons_table = $wpdb->prefix . 'barre_lessons';
    $reservations_table = $wpdb->prefix . 'barre_reservations';

    $line_items = [];
    $total_amount = 0;
    $pending_reservations = [];


    foreach ($basket as $item) {
        // Re-check availability (critical!)
        $lesson = $wpdb->get_row($wpdb->prepare(
            "SELECT capacity, used_spots FROM $lessons_table WHERE id = %d FOR UPDATE",
            $item['lessonId']
        ));

        if (!$lesson || ($lesson->capacity - $lesson->used_spots) < $item['persons']) {
            wp_send_json_error(['message' => "Not enough spots left for: {$item['name']}"]);
        }

        $amount = (int)($item['price'] * $item['persons'] * 100); // in cents
        $total_amount += $amount;

        $line_items[] = [
            'price_data' => [
                'currency' => 'czk',
                'product_data' => [
                    'name' => "{$item['name']} • {$item['date']} • {$item['time']}",
                    'description' => "{$item['persons']} person(s)",
                ],
                'unit_amount' => $amount,
            ],
            'quantity' => 1,
        ];

        // Remember what to reserve if payment succeeds
        $pending_reservations[] = [
            'lesson_id' => $item['lessonId'],
            'user_id'   => get_current_user_id(),
            'num_persons' => $item['persons'],
            'price'     => $item['price'],
        ];
    }

    try {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY); // ← define this constant somewhere secure!

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $line_items,
            'mode' => 'payment',
            'success_url' => home_url('/reservation-success/?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url'  => home_url('/checkout/?cancelled=1'),
            'metadata' => [
                'user_id' => get_current_user_id(),
                'basket_hash' => md5(json_encode($basket)) // simple verification
            ],
        ]);

        // Store pending reservations temporarily (you can use transient or separate table)
        set_transient('pending_res_' . $session->id, $pending_reservations, 60 * 60 * 2); // 2 hours

        wp_send_json_success([
            'checkout_url' => $session->url
        ]);

    } catch (Exception $e) {
        wp_send_json_error(['message' => 'Payment initialization failed: ' . $e->getMessage()]);
    }
}


/**
 * Stripe Webhook – handle successful payment
 */

//*/
function barre_stripe_webhook_handler() {
    // Only allow POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Method Not Allowed');
    }

    // Read raw POST body (important - don't use $_POST!)
    $payload = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

    // Your webhook secret from Stripe dashboard (different for test/live)
    $endpoint_secret = defined('STRIPE_WEBHOOK_SECRET') 
        ? STRIPE_WEBHOOK_SECRET 
        : 'whsec_placeholder'; // ← CHANGE THIS!

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig_header,
            $endpoint_secret
        );
    } catch(\UnexpectedValueException $e) {
        // Invalid payload
        http_response_code(400);
        exit("Invalid payload: " . $e->getMessage());
    } catch(\Stripe\Exception\SignatureVerificationException $e) {
        // Invalid signature
        http_response_code(400);
        exit("Webhook signature verification failed: " . $e->getMessage());
    }

    // Handle the event
    switch ($event->type) {
        case 'checkout.session.completed':
            $session = $event->data->object;
            
            // Safety checks
            if ($session->payment_status !== 'paid') {
                break;
            }

            // Get pending reservations stored during checkout creation
            $pending_key = 'pending_res_' . $session->id;
            $reservations = get_transient($pending_key);

            if (!$reservations || !is_array($reservations)) {
                error_log("No pending reservations found for session: " . $session->id);
                break;
            }

            global $wpdb;
            $res_table = $wpdb->prefix . 'barre_reservations';
            $lessons_table = $wpdb->prefix . 'barre_lessons';

            $wpdb->query('START TRANSACTION');

            try {
                foreach ($reservations as $res) {
                    // Double-check availability (paranoid mode)
                    $current = $wpdb->get_var($wpdb->prepare(
                        "SELECT used_spots FROM $lessons_table WHERE id = %d",
                        $res['lesson_id']
                    ));
                    /* fetch capacity again if needed */
                    if ($current === null || ($current + $res['num_persons']) > $res['capacity']) {
                        throw new Exception("Capacity exceeded for lesson " . $res['lesson_id']);
                    }

                    // Insert reservation
                    $wpdb->insert($res_table, [
                        'user_id'     => $res['user_id'],
                        'lesson_id'   => $res['lesson_id'],
                        'num_persons' => $res['num_persons'],
                        'status'      => 'confirmed',
                        'payment_id'  => $session->payment_intent,
                        'created_at'  => current_time('mysql')
                    ]);

                    // Update used spots
                    $wpdb->query($wpdb->prepare(
                        "UPDATE $lessons_table SET used_spots = used_spots + %d WHERE id = %d",
                        $res['num_persons'],
                        $res['lesson_id']
                    ));
                }

                $wpdb->query('COMMIT');

                // Clean up
                delete_transient($pending_key);

                // Send confirmation email(s)
                barre_send_reservation_confirmation($session->customer_details->email, $reservations, $session);

            } catch (Exception $e) {
                $wpdb->query('ROLLBACK');
                error_log("Reservation commit failed: " . $e->getMessage());
                http_response_code(500);
                exit();
            }

            break;

        case 'checkout.session.expired':
            // Optional: clean up pending reservations if you want
            $session = $event->data->object;
            delete_transient('pending_res_' . $session->id);
            break;

        // Add other events you care about (payment_intent.payment_failed, etc.)
        default:
            // Unexpected event type
            error_log("Unhandled Stripe event type: " . $event->type);
    }

    // Everything OK
    http_response_code(200);
    exit();
}

/*/
add_action('template_redirect', 'barre_handle_webhook');

function barre_handle_webhook() {
    if (!get_query_var('barre_webhook')) {
        return;
    }

    // Only POST allowed
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit;
    }

    $payload    = @file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

    $endpoint_secret = defined('STRIPE_WEBHOOK_SECRET') 
        ? STRIPE_WEBHOOK_SECRET 
        : 'whsec_placehold'; // ← CHANGE THIS!

    try {
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig_header,
            $endpoint_secret
        );
    } catch (\Exception $e) {
        http_response_code(400);
        error_log("Webhook signature verification failed: " . $e->getMessage());
        exit("Webhook Error");
    }

    // Handle checkout completed
    if ($event->type === 'checkout.session.completed') {
        $session = $event->data->object;

        // Skip if not paid
        if ($session->payment_status !== 'paid') {
            http_response_code(200);
            exit;
        }

        $session_id = $session->id;

        // Get stored basket from transient (set during checkout creation)
        $basket = get_transient('barre_pending_' . $session_id);

        if (!$basket || !is_array($basket)) {
            error_log("No pending basket found for session: " . $session_id);
            http_response_code(200);
            exit;
        }

        global $wpdb;
        $lessons_tbl = $wpdb->prefix . 'barre_lessons';
        $res_tbl     = $wpdb->prefix . 'barre_reservations';

        $user_id = $session->metadata->user_id ?? 0;
        if (!$user_id) {
            $user = get_user_by('email', $session->customer_details->email);
            $user_id = $user ? $user->ID : 0;
        }

        $wpdb->query('START TRANSACTION');

        try {
            foreach ($basket as $item) {
                // Re-check availability (paranoid)
                $lesson = $wpdb->get_row($wpdb->prepare(
                    "SELECT capacity, used_spots FROM $lessons_tbl WHERE id = %d",
                    $item['lessonId']
                ));

                if (!$lesson || ($lesson->capacity - $lesson->used_spots) < $item['persons']) {
                    throw new Exception("Capacity exceeded for lesson {$item['lessonId']}");
                }

                // Create reservation
                $wpdb->insert($res_tbl, [
                    'user_id'     => $user_id,
                    'lesson_id'   => $item['lessonId'],
                    'num_persons' => $item['persons'],
                    'status'      => 'confirmed',
                    'payment_id'  => $session->payment_intent,
                    'created_at'  => current_time('mysql')
                ]);

                // Increase used spots
                $wpdb->query($wpdb->prepare(
                    "UPDATE $lessons_tbl SET used_spots = used_spots + %d WHERE id = %d",
                    $item['persons'],
                    $item['lessonId']
                ));
            }

            $wpdb->query('COMMIT');

            // Clean up
            delete_transient('barre_pending_' . $session_id);

            // Send email (implement your own function)
            barre_send_confirmation_email($user_id, $basket, $session);

        } catch (Exception $e) {
            $wpdb->query('ROLLBACK');
            error_log("Reservation failed for session {$session_id}: " . $e->getMessage());
            http_response_code(500);
            exit;
        }
    }

    http_response_code(200);
    exit;
}
//*/

/**
 * Register custom rewrite rule for clean webhook URL
 */
function barre_register_webhook_rewrite() {
    add_rewrite_rule(
        '^stripe-webhook/?$',
        'index.php?barre_webhook=1',
        'top'
    );
}
add_action('init', 'barre_register_webhook_rewrite');

/**
 * Handle our custom query var
 */
function barre_webhook_template_redirect() {
    if (get_query_var('barre_webhook')) {
        barre_stripe_webhook_handler();
        exit;
    }
}
add_action('template_redirect', 'barre_webhook_template_redirect');

function barre_send_confirmation_email($user_id, $basket, $session) {
    $user = get_userdata($user_id);
    if (!$user) return;

    $to      = $user->user_email;
    $subject = 'Your Barre Reservation Confirmation';
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    $message = '<h2>Thank you for your booking!</h2>';
    $message .= '<p>Payment ID: ' . esc_html($session->payment_intent) . '</p>';
    $message .= '<p>Total paid: ' . number_format($session->amount_total / 100, 2) . ' CZK</p>';
    $message .= '<h3>Your reservations:</h3><ul>';

    foreach ($basket as $item) {
        $message .= "<li>{$item['name']} – {$item['date']} at {$item['time']} × {$item['persons']} person(s)</li>";
    }

    $message .= '</ul>';
    $message .= '<p>View details: <a href="' . home_url('/my-reservations') . '">My Reservations</a></p>';

    wp_mail($to, $subject, $message, $headers);
}