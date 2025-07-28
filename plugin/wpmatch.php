<?php
/**
 * Plugin Name: WPMatch
 * Plugin URI: https://github.com/terryarthur/wpmatch
 * Description: A WordPress plugin for matching functionality.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/terryarthur
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpmatch
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 *
 * @package WPMatch
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'WPMATCH_VERSION', '1.0.0' );
define( 'WPMATCH_PLUGIN_FILE', __FILE__ );
define( 'WPMATCH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPMATCH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPMATCH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main WPMatch class
 */
class WPMatch {

    /**
     * Instance of this class
     *
     * @var WPMatch
     */
    private static $instance;

    /**
     * Get instance of this class
     *
     * @return WPMatch
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'init', array( $this, 'init' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
        
        // Activation and deactivation hooks
        register_activation_hook( WPMATCH_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( WPMATCH_PLUGIN_FILE, array( $this, 'deactivate' ) );
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Load text domain for translations
        load_plugin_textdomain( 'wpmatch', false, dirname( WPMATCH_PLUGIN_BASENAME ) . '/languages' );
        
        // Include required files
        $this->includes();
    }

    /**
     * Include required files
     */
    private function includes() {
        require_once WPMATCH_PLUGIN_DIR . 'includes/class-wpmatch-admin.php';
        require_once WPMATCH_PLUGIN_DIR . 'includes/class-wpmatch-frontend.php';
        require_once WPMATCH_PLUGIN_DIR . 'includes/class-wpmatch-database.php';
    }

    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 
            'wpmatch-frontend', 
            WPMATCH_PLUGIN_URL . 'assets/css/frontend.css', 
            array(), 
            WPMATCH_VERSION 
        );
        
        wp_enqueue_script( 
            'wpmatch-frontend', 
            WPMATCH_PLUGIN_URL . 'assets/js/frontend.js', 
            array( 'jquery' ), 
            WPMATCH_VERSION, 
            true 
        );
        
        // Localize script for AJAX
        wp_localize_script( 'wpmatch-frontend', 'wpmatch_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'wpmatch_nonce' ),
        ) );
    }

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts() {
        wp_enqueue_style( 
            'wpmatch-admin', 
            WPMATCH_PLUGIN_URL . 'assets/css/admin.css', 
            array(), 
            WPMATCH_VERSION 
        );
        
        wp_enqueue_script( 
            'wpmatch-admin', 
            WPMATCH_PLUGIN_URL . 'assets/js/admin.js', 
            array( 'jquery' ), 
            WPMATCH_VERSION, 
            true 
        );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        // Create database tables if needed
        $database = new WPMatch_Database();
        $database->create_tables();
        
        // Set default options
        add_option( 'wpmatch_version', WPMATCH_VERSION );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

// Initialize the plugin
WPMatch::get_instance();