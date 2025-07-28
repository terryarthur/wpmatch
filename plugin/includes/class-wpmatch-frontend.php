<?php
/**
 * WPMatch Frontend Class
 *
 * @package WPMatch
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPMatch Frontend class
 */
class WPMatch_Frontend {

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
        add_shortcode( 'wpmatch', array( $this, 'shortcode' ) );
        add_action( 'wp_ajax_wpmatch_action', array( $this, 'ajax_handler' ) );
        add_action( 'wp_ajax_nopriv_wpmatch_action', array( $this, 'ajax_handler' ) );
    }

    /**
     * Shortcode handler
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function shortcode( $atts ) {
        $atts = shortcode_atts(
            array(
                'type' => 'default',
                'limit' => 5,
            ),
            $atts,
            'wpmatch'
        );

        $options = get_option( 'wpmatch_options' );
        if ( ! isset( $options['enable_matching'] ) || ! $options['enable_matching'] ) {
            return '<p>' . __( 'Matching is currently disabled.', 'wpmatch' ) . '</p>';
        }

        ob_start();
        ?>
        <div class="wpmatch-container" data-type="<?php echo esc_attr( $atts['type'] ); ?>" data-limit="<?php echo esc_attr( $atts['limit'] ); ?>">
            <div class="wpmatch-matches">
                <!-- Matches will be loaded here -->
            </div>
            <button class="wpmatch-load-more"><?php _e( 'Load More Matches', 'wpmatch' ); ?></button>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX handler
     */
    public function ajax_handler() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'wpmatch_nonce' ) ) {
            wp_die( __( 'Security check failed', 'wpmatch' ) );
        }

        $type = sanitize_text_field( $_POST['type'] ?? 'default' );
        $limit = intval( $_POST['limit'] ?? 5 );
        $offset = intval( $_POST['offset'] ?? 0 );

        // Get matches (placeholder implementation)
        $matches = $this->get_matches( $type, $limit, $offset );

        wp_send_json_success( $matches );
    }

    /**
     * Get matches (placeholder implementation)
     *
     * @param string $type   Match type.
     * @param int    $limit  Number of matches to retrieve.
     * @param int    $offset Offset for pagination.
     * @return array
     */
    private function get_matches( $type, $limit, $offset ) {
        // This is a placeholder implementation
        // In a real application, this would query your matching algorithm/database
        
        $matches = array();
        for ( $i = $offset; $i < $offset + $limit; $i++ ) {
            $matches[] = array(
                'id' => $i + 1,
                'title' => sprintf( __( 'Match %d', 'wpmatch' ), $i + 1 ),
                'description' => sprintf( __( 'This is match description %d', 'wpmatch' ), $i + 1 ),
                'score' => rand( 70, 100 ),
            );
        }
        
        return $matches;
    }
}

// Initialize frontend class
new WPMatch_Frontend();