<?php
/**
 * Theme Name: WPMatch Theme
 * Description: A WordPress theme designed for the WPMatch plugin with matching functionality.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/terryarthur
 * Text Domain: wpmatch-theme
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package WPMatch_Theme
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define theme constants
define( 'WPMATCH_THEME_VERSION', '1.0.0' );
define( 'WPMATCH_THEME_DIR', get_template_directory() );
define( 'WPMATCH_THEME_URL', get_template_directory_uri() );

/**
 * WPMatch Theme Setup
 */
function wpmatch_theme_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo' );
    add_theme_support( 'custom-background' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    // Add custom image sizes
    add_image_size( 'wpmatch-featured', 600, 400, true );
    add_image_size( 'wpmatch-thumbnail', 300, 200, true );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'wpmatch-theme' ),
        'footer'  => __( 'Footer Menu', 'wpmatch-theme' ),
    ) );

    // Load text domain
    load_theme_textdomain( 'wpmatch-theme', WPMATCH_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'wpmatch_theme_setup' );

/**
 * Enqueue scripts and styles
 */
function wpmatch_theme_scripts() {
    // Enqueue styles
    wp_enqueue_style( 'wpmatch-theme-style', get_stylesheet_uri(), array(), WPMATCH_THEME_VERSION );
    wp_enqueue_style( 'wpmatch-theme-main', WPMATCH_THEME_URL . '/assets/css/main.css', array(), WPMATCH_THEME_VERSION );

    // Enqueue scripts
    wp_enqueue_script( 'wpmatch-theme-main', WPMATCH_THEME_URL . '/assets/js/main.js', array( 'jquery' ), WPMATCH_THEME_VERSION, true );

    // Localize script
    wp_localize_script( 'wpmatch-theme-main', 'wpmatch_theme', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'wpmatch_theme_nonce' ),
    ) );

    // Comment reply script
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'wpmatch_theme_scripts' );

/**
 * Register widget areas
 */
function wpmatch_theme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Sidebar', 'wpmatch-theme' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Add widgets here to appear in your sidebar.', 'wpmatch-theme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Footer', 'wpmatch-theme' ),
        'id'            => 'footer-1',
        'description'   => __( 'Add widgets here to appear in your footer.', 'wpmatch-theme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'wpmatch_theme_widgets_init' );

/**
 * Custom post types for matches
 */
function wpmatch_theme_register_post_types() {
    // Match profiles post type
    register_post_type( 'match_profile', array(
        'labels' => array(
            'name'          => __( 'Match Profiles', 'wpmatch-theme' ),
            'singular_name' => __( 'Match Profile', 'wpmatch-theme' ),
            'add_new'       => __( 'Add New Profile', 'wpmatch-theme' ),
            'add_new_item'  => __( 'Add New Match Profile', 'wpmatch-theme' ),
            'edit_item'     => __( 'Edit Match Profile', 'wpmatch-theme' ),
            'new_item'      => __( 'New Match Profile', 'wpmatch-theme' ),
            'view_item'     => __( 'View Match Profile', 'wpmatch-theme' ),
            'search_items'  => __( 'Search Match Profiles', 'wpmatch-theme' ),
        ),
        'public'        => true,
        'has_archive'   => true,
        'menu_icon'     => 'dashicons-groups',
        'supports'      => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ),
        'rewrite'       => array( 'slug' => 'profiles' ),
    ) );
}
add_action( 'init', 'wpmatch_theme_register_post_types' );

/**
 * Include theme functions
 */
require_once WPMATCH_THEME_DIR . '/inc/template-functions.php';
require_once WPMATCH_THEME_DIR . '/inc/customizer.php';