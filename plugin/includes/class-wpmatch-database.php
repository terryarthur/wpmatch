<?php
/**
 * WPMatch Database Class
 *
 * @package WPMatch
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * WPMatch Database class
 */
class WPMatch_Database {

    /**
     * Create database tables
     */
    public function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Matches table
        $table_name = $wpdb->prefix . 'wpmatch_matches';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            matched_user_id bigint(20) NOT NULL,
            match_score decimal(5,2) NOT NULL DEFAULT '0.00',
            status varchar(20) NOT NULL DEFAULT 'pending',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY matched_user_id (matched_user_id),
            KEY status (status)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        // User preferences table
        $table_name = $wpdb->prefix . 'wpmatch_user_preferences';
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            preference_key varchar(100) NOT NULL,
            preference_value longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_preference (user_id, preference_key)
        ) $charset_collate;";

        dbDelta( $sql );
    }

    /**
     * Get matches for a user
     *
     * @param int    $user_id User ID.
     * @param string $status  Match status.
     * @param int    $limit   Number of matches to retrieve.
     * @param int    $offset  Offset for pagination.
     * @return array
     */
    public function get_user_matches( $user_id, $status = 'all', $limit = 10, $offset = 0 ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpmatch_matches';
        
        $where = $wpdb->prepare( 'user_id = %d', $user_id );
        
        if ( 'all' !== $status ) {
            $where .= $wpdb->prepare( ' AND status = %s', $status );
        }
        
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE $where ORDER BY match_score DESC, created_at DESC LIMIT %d OFFSET %d",
            $limit,
            $offset
        );
        
        return $wpdb->get_results( $sql );
    }

    /**
     * Create a new match
     *
     * @param int   $user_id         User ID.
     * @param int   $matched_user_id Matched user ID.
     * @param float $match_score     Match score.
     * @param string $status         Match status.
     * @return int|false
     */
    public function create_match( $user_id, $matched_user_id, $match_score, $status = 'pending' ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpmatch_matches';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'user_id'         => $user_id,
                'matched_user_id' => $matched_user_id,
                'match_score'     => $match_score,
                'status'          => $status,
            ),
            array(
                '%d',
                '%d',
                '%f',
                '%s',
            )
        );
        
        return $result ? $wpdb->insert_id : false;
    }

    /**
     * Update match status
     *
     * @param int    $match_id Match ID.
     * @param string $status   New status.
     * @return bool
     */
    public function update_match_status( $match_id, $status ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpmatch_matches';
        
        $result = $wpdb->update(
            $table_name,
            array( 'status' => $status ),
            array( 'id' => $match_id ),
            array( '%s' ),
            array( '%d' )
        );
        
        return false !== $result;
    }

    /**
     * Get user preference
     *
     * @param int    $user_id User ID.
     * @param string $key     Preference key.
     * @return mixed
     */
    public function get_user_preference( $user_id, $key ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpmatch_user_preferences';
        
        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT preference_value FROM $table_name WHERE user_id = %d AND preference_key = %s",
                $user_id,
                $key
            )
        );
        
        return maybe_unserialize( $value );
    }

    /**
     * Set user preference
     *
     * @param int    $user_id User ID.
     * @param string $key     Preference key.
     * @param mixed  $value   Preference value.
     * @return bool
     */
    public function set_user_preference( $user_id, $key, $value ) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wpmatch_user_preferences';
        
        $serialized_value = maybe_serialize( $value );
        
        $result = $wpdb->replace(
            $table_name,
            array(
                'user_id'         => $user_id,
                'preference_key'  => $key,
                'preference_value' => $serialized_value,
            ),
            array(
                '%d',
                '%s',
                '%s',
            )
        );
        
        return false !== $result;
    }
}