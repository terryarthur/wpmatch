<?php
/**
 * WPMatch Admin Class
 *
 * @package WPMatch
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPMatch Admin class
 */
class WPMatch_Admin {

    /**
     * Constructor
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'init_settings' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'WPMatch Settings', 'wpmatch' ),
            __( 'WPMatch', 'wpmatch' ),
            'manage_options',
            'wpmatch',
            array( $this, 'admin_page' ),
            'dashicons-heart',
            30
        );
    }

    /**
     * Initialize settings
     */
    public function init_settings() {
        register_setting( 'wpmatch_settings', 'wpmatch_options' );
        
        add_settings_section(
            'wpmatch_general',
            __( 'General Settings', 'wpmatch' ),
            array( $this, 'settings_section_callback' ),
            'wpmatch'
        );
        
        add_settings_field(
            'enable_matching',
            __( 'Enable Matching', 'wpmatch' ),
            array( $this, 'enable_matching_callback' ),
            'wpmatch',
            'wpmatch_general'
        );
    }

    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __( 'Configure your WPMatch settings below.', 'wpmatch' ) . '</p>';
    }

    /**
     * Enable matching field callback
     */
    public function enable_matching_callback() {
        $options = get_option( 'wpmatch_options' );
        $enabled = isset( $options['enable_matching'] ) ? $options['enable_matching'] : false;
        
        echo '<input type="checkbox" name="wpmatch_options[enable_matching]" value="1" ' . checked( 1, $enabled, false ) . ' />';
        echo '<label for="wpmatch_options[enable_matching]">' . __( 'Enable matching functionality', 'wpmatch' ) . '</label>';
    }

    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'wpmatch_settings' );
                do_settings_sections( 'wpmatch' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize admin class if in admin
if ( is_admin() ) {
    new WPMatch_Admin();
}