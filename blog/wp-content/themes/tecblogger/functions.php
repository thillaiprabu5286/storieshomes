<?php
/**
 * tecblogger functions and definitions
 *
 * @package tecblogger
 */
if ( ! isset( $content_width ) ) $content_width = 900;

if ( ! function_exists( 'tecblogger_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function tecblogger_setup() {
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on tecblogger, use a find and replace
	 * to change 'tecblogger' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'tecblogger', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	add_image_size( 'blog-thumbnail', 960, 450, true ); 	
	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'tecblogger' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	/*
	 * Enable support for Post Formats.
	 * See http://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
	) );

	// Set up the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'tecblogger_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // tecblogger_setup
add_action( 'after_setup_theme', 'tecblogger_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function tecblogger_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'tecblogger_content_width', 640 );
}
add_action( 'after_setup_theme', 'tecblogger_content_width', 0 );

/**
 * Register widget area.
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function tecblogger_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'tecblogger' ),
		'id'            => 'sidebar-1',
		'description'   => '',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'tecblogger_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function tecblogger_scripts() {
	
	wp_enqueue_style( 'tecblogger-bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css' );
	wp_enqueue_style( 'tecblogger-fontawesome', get_template_directory_uri() . '/css/font-awesome.min.css' );
		
	
	
	wp_enqueue_style( 'tecblogger-style', get_stylesheet_uri() );
	wp_enqueue_style( 'tecblogger-responsive', get_template_directory_uri() . '/css/responsive.css' );	
	
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'tecblogger-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
	wp_enqueue_script( 'tecblogger-bootstrap', get_template_directory_uri() . '/js/bootstrap.js', array(), '20120209', true );
	wp_enqueue_script( 'tecblogger-customizer', get_template_directory_uri() . '/js/customizer.js', array(), '20120209', true );

	wp_enqueue_script( 'tecblogger-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'tecblogger_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Load Navwalker compatibility file.
 */
require get_template_directory() . '/inc/tecblogger_navwalker.php';


if( ! function_exists('tecblogger_iesupported')):
function tecblogger_iesupported(){
	?>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->	
	
	<?php
}
add_action('wp_head', 'tecblogger_iesupported');
endif;


function tecblogger_custom_wp_admin_style() {
        wp_register_style( 'tecblogger-custom_wp_admin_css', get_template_directory_uri() . '/admin/css/tecblogger-admin.css', false, '1.0.0' );
        wp_enqueue_style( 'tecblogger-custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'tecblogger_custom_wp_admin_style' );


add_action('admin_menu', 'tecblogger_menu_init');
function tecblogger_menu_settings(){
	include('admin/admin-settings.php');	
}



function tecblogger_menu_init() {
	add_theme_page( __('TecBlogger Admin','tecblogger'), __('TecBlogger Admin','tecblogger'), 'manage_options', 'tecblogger_menu_settings', 'tecblogger_menu_settings');		
}
