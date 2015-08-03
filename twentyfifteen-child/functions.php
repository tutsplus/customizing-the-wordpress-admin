<?php 
//===============================================================
// CHILD THEME SCRIPT
//===============================================================

//-----------------------------------------------
// Enqueue the parent theme's stylesheet
//-----------------------------------------------
function theme_enqueue_styles() {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );



//===============================================================
// THE LEFT MENU
//===============================================================

function edit_wp_menu() {
	//-----------------------------------------------
	// Remove menu items
	//-----------------------------------------------
	remove_menu_page( 'edit-comments.php' );

	//-----------------------------------------------
	// Add a menu item
	//-----------------------------------------------
	add_menu_page(
		'New Comments',
		'My Comments',
		'manage_options',
		'edit-comments.php',
		'',
		'',
		6
	);

	//-----------------------------------------------
	// Change the menu order
	//-----------------------------------------------
	function change_menu_order( $menu_order ) {
		return array(
			'index.php',
			'themes.php',
			'edit.php',
			'edit.php?post_type=page',
			'upload.php'
		);
	}

	add_filter( 'custom_menu_order', '__return_true' );
	add_filter( 'menu_order', 'change_menu_order' );

	//-----------------------------------------------
	// Rename Posts to Articles
	//-----------------------------------------------
	global $menu;
	global $submenu;

	// print_r($submenu);

	$menu[5][0] = 'Articles';
	$submenu['edit.php'][5][0] = 'All Articles';
	$submenu['edit.php'][10][0] = 'Add an Article';
	$submenu['edit.php'][15][0] = 'Article Categories';
	$submenu['edit.php'][16][0] = 'Article Tags';
}

//-----------------------------------------------
// Change post labels
//-----------------------------------------------
function change_post_labels() {
	global $wp_post_types;

	// print_r($wp_post_types);
	
	// Get the current post labels
	$articleLabels = $wp_post_types['post']->labels;

	$articleLabels->name               = 'Articles';
	$articleLabels->singular_name      = 'Article';
	$articleLabels->add_new            = 'Add Articles';
	$articleLabels->add_new_item       = 'Add Articles';
	$articleLabels->edit_item          = 'Edit Articles';
	$articleLabels->new_item           = 'Articles';
	$articleLabels->view_item          = 'View Articles';
	$articleLabels->search_items       = 'Search Articles';
	$articleLabels->not_found          = 'No Articles found';
	$articleLabels->not_found_in_trash = 'No Articles found in Trash';
}

add_action( 'admin_menu', 'edit_wp_menu' );
add_action( 'init', 'change_post_labels' );



//===============================================================
// THE DASHBOARD
//===============================================================
function customize_dashboard() {
	//-----------------------------------------------
	// Remove a default dashboard widget
	//-----------------------------------------------
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );

	// Remove the welcome panel
	remove_action( 'welcome_panel', 'wp_welcome_panel' );

	//-----------------------------------------------
	// Add a new dashboard widget
	//-----------------------------------------------
	wp_add_dashboard_widget(
		'date_dashboard_widget', // ID
		'Today', // Title,
		'date_dashboard_widget_function' // Callback
	);
}

add_action( 'wp_dashboard_setup', 'customize_dashboard' );

function date_dashboard_widget_function() {
	echo "Hi. Today is " . date( 'l\, F jS Y' );
}



//===============================================================
// THE TABLE COLUMNS
//===============================================================
function customize_posts_listing_cols( $columns ) {
	// print_r($columns);
	
	unset( $columns[ 'tags' ] );
	unset( $columns[ 'comments' ] );
	unset( $columns[ 'categories' ] );

	return $columns;
}

function customize_pages_listing_cols( $columns ) {
	unset( $columns[ 'comments' ] );

	return $columns;
}

add_action( 'manage_posts_columns', 'customize_posts_listing_cols' );
add_action( 'manage_pages_columns', 'customize_pages_listing_cols' );



//===============================================================
// THE HELP AREAS
//===============================================================
function add_help_metaboxes() {
	$screens = array( 'post', 'page' );

	foreach ( $screens as $screen ) {
		add_meta_box(
			'helping-metabox',
			'Helpful Metabox',
			'add_help_metaboxes_callback',
			$screen,
			'advanced',
			'high'
		);

		add_meta_box(
			'helping-metabox-side',
			'Another Helpful Metabox',
			'add_side_help_metaboxes_callback',
			$screen,
			'side',
			'low'
		);
	}
}

function add_help_metaboxes_callback() {
	echo "<p>Hello. This is a very helpful message.</p>";
}

function add_side_help_metaboxes_callback() {
	echo "<p>Hello. This is another very helpful message.</p>";
}

add_action( 'add_meta_boxes', 'add_help_metaboxes' );

//-----------------------------------------------
// Add new tabs in the help menu
//-----------------------------------------------
function add_help_tabs() {
	if ( $screen = get_current_screen() ) {
		// Save the current help tabs
		$help_tabs = $screen->get_help_tabs();

		// Remvoe the current help tabs from the screen
		$screen->remove_help_tabs();

		$screen->add_help_tab( array(
			'id'      => 'new_help_tab',
			'title'   => 'Custom Help Tab',
			'content' => '<p>This is some very helpful content.</p>'
		) );

		if ( count( $help_tabs ) ) {
			// If we had help tabs before, add them back to the screen
			foreach ( $help_tabs as $help_tab ) {
				$screen->add_help_tab( $help_tab );
			}
		}
	}
}

add_action( 'in_admin_header', 'add_help_tabs' );



//===============================================================
// SOME SMALLER CUSTOMIZATIONS
//===============================================================

//-----------------------------------------------
// Change the footer text
//-----------------------------------------------
function change_admin_footer() {
	echo "Hello, this is a custom footer text.";
}

add_filter( 'admin_footer_text', 'change_admin_footer' );

//-----------------------------------------------
// Remove the WP version from the footer
//-----------------------------------------------
function remove_footer_version() {
	// remove_filter( 'update_footer', 'core_update_footer' );
}

add_action( 'admin_menu', 'remove_footer_version' );

//-----------------------------------------------
// Disable standard widgets
//-----------------------------------------------
function disable_wp_widgets() {
	unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'WP_Widget_Search' );
	unregister_widget( 'WP_Widget_Recent_Comments' );
}

add_action( 'widgets_init', 'disable_wp_widgets' );

//-----------------------------------------------
// Customize the WYSIWYG Editor's CSS
//-----------------------------------------------
function add_editor_styles() {
	add_editor_style( 'editor-style.css' );
}

add_action( 'admin_init', 'add_editor_styles' );



//===============================================================
// THE COLOR SCHEMES
//===============================================================
function add_color_schemes() {
	$dir = get_stylesheet_directory_uri();

	wp_admin_css_color(
		'tutsplus',
		'Tuts+ Color Scheme',
		$dir . '/admin-color-schemes/tutsplus/colors.min.css',
		array( '#2A3743', '#239951', '#FEF5DA', '#8E5489' )
	);
}

add_action( 'admin_init', 'add_color_schemes' );



//===============================================================
// THE LOGIN SCREEN
//===============================================================

//-----------------------------------------------
// Change the login form logo
//-----------------------------------------------
function change_login_logo() { ?>
	<style>
		.login h1 a {
			background-image: url( <?php echo get_stylesheet_directory_uri(); ?>/images/login-logo.png );
			padding-bottom: 20px;
		}
	</style>
<?php }

add_action( 'login_enqueue_scripts', 'change_login_logo' );

//-----------------------------------------------
// Change the login logo URL and title
//-----------------------------------------------
function change_login_logo_url() {
	return home_url();
}

function change_login_logo_url_title() {
	return "Tuts+ Child Theme";
}

add_filter( 'login_headerurl', 'change_login_logo_url' );
add_filter( 'login_headertitle', 'change_login_logo_url_title' );

//-----------------------------------------------
// Style the login page
//-----------------------------------------------
function change_login_stylesheet() {
	wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/custom-login/custom-login.css' );
	wp_enqueue_script( 'custom-login', get_stylesheet_directory_uri() . '/custom-login/custom-login.js' );
}

add_action( 'login_enqueue_scripts', 'change_login_stylesheet' );

//-----------------------------------------------
// Disable the password reset feature
//-----------------------------------------------
function disable_reset_pwd() {
	return false;
}

add_filter( 'allow_password_reset', 'disable_reset_pwd' );

//-----------------------------------------------
// Remove error shake
//-----------------------------------------------
function remove_shake() {
	remove_action( 'login_head', 'wp_shake_js', 12 );
}

add_action( 'login_head', 'remove_shake' );



//===============================================================
// THE ADMIN BAR
//===============================================================

//-----------------------------------------------
// Remove links
//-----------------------------------------------
function remove_admin_bar_links() {
	global $wp_admin_bar;

	// print_r($wp_admin_bar);
	$wp_admin_bar->remove_menu( 'wp-logo' );
	// $wp_admin_bar->remove_menu( 'my-account' );
}

add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );

//-----------------------------------------------
// Add links
//-----------------------------------------------
function add_admin_bar_links() {
	global $wp_admin_bar;

	$wp_admin_bar->add_menu( array(
		'id' => 'tutsplus-menu',
		'title' => 'Tuts+',
		'href' => 'http://tutsplus.com',
		'meta' => array( 'target' => '_blank' )
	) );
}

add_action( 'admin_bar_menu', 'add_admin_bar_links', 35 );

//-----------------------------------------------
// Customize the appearance
//-----------------------------------------------
function admin_bar_css() { ?>
	<style>
		/*#wpadminbar { background-color: #1E1E1E; }*/
	</style>
<?php }

add_action( 'admin_head', 'admin_bar_css' );



//===============================================================
// POSTS AND PAGES EDITING SCREENS
//===============================================================
function remove_meta_boxes() {
	remove_meta_box( 'commentsdiv', 'post', 'normal' );

	remove_meta_box( 'commentsdiv', 'page', 'normal' );
}

add_action( 'admin_init', 'remove_meta_boxes' );



//===============================================================
// CUSTOM ADMIN THEME
//===============================================================
function load_custom_admin_theme() {
	wp_enqueue_style( 'custom-admin-theme', get_stylesheet_directory_uri() . '/custom-admin-theme/custom-admin-theme.css' );
	wp_enqueue_script( 'custom-admin-theme', get_stylesheet_directory_uri() . '/custom-admin-theme/custom-admin-theme.js' );
}

add_action( 'admin_enqueue_scripts', 'load_custom_admin_theme' );
?>