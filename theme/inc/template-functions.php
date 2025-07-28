<?php
/**
 * Template functions for WPMatch Theme
 *
 * @package WPMatch_Theme
 */

/**
 * Get match profiles
 *
 * @param array $args Query arguments.
 * @return WP_Query
 */
function wpmatch_get_profiles( $args = array() ) {
    $defaults = array(
        'post_type'      => 'match_profile',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'meta_query'     => array(),
    );
    
    $args = wp_parse_args( $args, $defaults );
    
    return new WP_Query( $args );
}

/**
 * Display match profile card
 *
 * @param int|WP_Post $post Post ID or post object.
 */
function wpmatch_display_profile_card( $post = null ) {
    $post = get_post( $post );
    if ( ! $post ) {
        return;
    }
    
    $age = get_post_meta( $post->ID, '_profile_age', true );
    $location = get_post_meta( $post->ID, '_profile_location', true );
    $interests = get_post_meta( $post->ID, '_profile_interests', true );
    
    ?>
    <div class="match-profile-card" data-profile-id="<?php echo esc_attr( $post->ID ); ?>">
        <?php if ( has_post_thumbnail( $post ) ) : ?>
            <div class="profile-image">
                <?php echo get_the_post_thumbnail( $post, 'wpmatch-thumbnail' ); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-info">
            <h3 class="profile-name">
                <a href="<?php echo get_permalink( $post ); ?>">
                    <?php echo get_the_title( $post ); ?>
                </a>
            </h3>
            
            <?php if ( $age ) : ?>
                <p class="profile-age">
                    <?php printf( __( 'Age: %s', 'wpmatch-theme' ), esc_html( $age ) ); ?>
                </p>
            <?php endif; ?>
            
            <?php if ( $location ) : ?>
                <p class="profile-location">
                    <?php printf( __( 'Location: %s', 'wpmatch-theme' ), esc_html( $location ) ); ?>
                </p>
            <?php endif; ?>
            
            <?php if ( $interests ) : ?>
                <div class="profile-interests">
                    <strong><?php _e( 'Interests:', 'wpmatch-theme' ); ?></strong>
                    <p><?php echo esc_html( $interests ); ?></p>
                </div>
            <?php endif; ?>
            
            <div class="profile-excerpt">
                <?php echo wp_trim_words( get_the_excerpt( $post ), 20 ); ?>
            </div>
            
            <div class="profile-actions">
                <a href="<?php echo get_permalink( $post ); ?>" class="btn btn-primary">
                    <?php _e( 'View Profile', 'wpmatch-theme' ); ?>
                </a>
                <button class="btn btn-secondary like-profile" data-profile-id="<?php echo esc_attr( $post->ID ); ?>">
                    <?php _e( 'Like', 'wpmatch-theme' ); ?>
                </button>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Get user's match compatibility score
 *
 * @param int $user_id      Current user ID.
 * @param int $profile_id   Profile ID to compare with.
 * @return float
 */
function wpmatch_get_compatibility_score( $user_id, $profile_id ) {
    // This is a placeholder implementation
    // In a real application, this would calculate based on user preferences
    
    $user_interests = get_user_meta( $user_id, 'interests', true );
    $profile_interests = get_post_meta( $profile_id, '_profile_interests', true );
    
    if ( empty( $user_interests ) || empty( $profile_interests ) ) {
        return 50.0; // Default score
    }
    
    // Simple compatibility calculation based on common interests
    $user_interests_array = explode( ',', strtolower( $user_interests ) );
    $profile_interests_array = explode( ',', strtolower( $profile_interests ) );
    
    $common_interests = array_intersect( $user_interests_array, $profile_interests_array );
    $total_interests = array_unique( array_merge( $user_interests_array, $profile_interests_array ) );
    
    if ( empty( $total_interests ) ) {
        return 50.0;
    }
    
    $score = ( count( $common_interests ) / count( $total_interests ) ) * 100;
    
    return round( $score, 1 );
}

/**
 * Display compatibility score
 *
 * @param float $score Compatibility score.
 */
function wpmatch_display_compatibility_score( $score ) {
    $class = '';
    if ( $score >= 80 ) {
        $class = 'score-excellent';
    } elseif ( $score >= 60 ) {
        $class = 'score-good';
    } elseif ( $score >= 40 ) {
        $class = 'score-average';
    } else {
        $class = 'score-poor';
    }
    
    printf(
        '<span class="compatibility-score %s">%s%%</span>',
        esc_attr( $class ),
        esc_html( $score )
    );
}

/**
 * Custom breadcrumb function
 */
function wpmatch_breadcrumb() {
    if ( is_front_page() ) {
        return;
    }
    
    echo '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'wpmatch-theme' ) . '">';
    echo '<ol>';
    
    // Home link
    echo '<li><a href="' . home_url( '/' ) . '">' . __( 'Home', 'wpmatch-theme' ) . '</a></li>';
    
    if ( is_single() ) {
        $post_type = get_post_type();
        if ( 'match_profile' === $post_type ) {
            echo '<li><a href="' . get_post_type_archive_link( 'match_profile' ) . '">' . __( 'Profiles', 'wpmatch-theme' ) . '</a></li>';
        } elseif ( 'post' === $post_type ) {
            echo '<li><a href="' . get_permalink( get_option( 'page_for_posts' ) ) . '">' . __( 'Blog', 'wpmatch-theme' ) . '</a></li>';
        }
        echo '<li aria-current="page">' . get_the_title() . '</li>';
    } elseif ( is_page() ) {
        echo '<li aria-current="page">' . get_the_title() . '</li>';
    } elseif ( is_archive() ) {
        echo '<li aria-current="page">' . get_the_archive_title() . '</li>';
    } elseif ( is_search() ) {
        echo '<li aria-current="page">' . sprintf( __( 'Search Results for: %s', 'wpmatch-theme' ), get_search_query() ) . '</li>';
    }
    
    echo '</ol>';
    echo '</nav>';
}

/**
 * Add custom body classes
 *
 * @param array $classes Existing body classes.
 * @return array
 */
function wpmatch_body_classes( $classes ) {
    // Add class for logged-in users
    if ( is_user_logged_in() ) {
        $classes[] = 'logged-in-user';
    }
    
    // Add class for match profile pages
    if ( is_singular( 'match_profile' ) ) {
        $classes[] = 'single-match-profile';
    }
    
    return $classes;
}
add_filter( 'body_class', 'wpmatch_body_classes' );