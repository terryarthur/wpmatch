<?php
/**
 * Customizer settings for WPMatch Theme
 *
 * @package WPMatch_Theme
 */

/**
 * Add customizer settings
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function wpmatch_customize_register( $wp_customize ) {
    
    // Theme Options Panel
    $wp_customize->add_panel( 'wpmatch_theme_options', array(
        'title'       => __( 'WPMatch Theme Options', 'wpmatch-theme' ),
        'description' => __( 'Customize your WPMatch theme settings.', 'wpmatch-theme' ),
        'priority'    => 30,
    ) );
    
    // Header Section
    $wp_customize->add_section( 'wpmatch_header', array(
        'title'    => __( 'Header Settings', 'wpmatch-theme' ),
        'panel'    => 'wpmatch_theme_options',
        'priority' => 10,
    ) );
    
    // Header Layout
    $wp_customize->add_setting( 'wpmatch_header_layout', array(
        'default'           => 'horizontal',
        'sanitize_callback' => 'wpmatch_sanitize_select',
    ) );
    
    $wp_customize->add_control( 'wpmatch_header_layout', array(
        'label'    => __( 'Header Layout', 'wpmatch-theme' ),
        'section'  => 'wpmatch_header',
        'type'     => 'select',
        'choices'  => array(
            'horizontal' => __( 'Horizontal', 'wpmatch-theme' ),
            'centered'   => __( 'Centered', 'wpmatch-theme' ),
        ),
    ) );
    
    // Show/Hide Site Description
    $wp_customize->add_setting( 'wpmatch_show_site_description', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    
    $wp_customize->add_control( 'wpmatch_show_site_description', array(
        'label'   => __( 'Show Site Description', 'wpmatch-theme' ),
        'section' => 'wpmatch_header',
        'type'    => 'checkbox',
    ) );
    
    // Matching Section
    $wp_customize->add_section( 'wpmatch_matching', array(
        'title'    => __( 'Matching Settings', 'wpmatch-theme' ),
        'panel'    => 'wpmatch_theme_options',
        'priority' => 20,
    ) );
    
    // Profiles per page
    $wp_customize->add_setting( 'wpmatch_profiles_per_page', array(
        'default'           => 12,
        'sanitize_callback' => 'absint',
    ) );
    
    $wp_customize->add_control( 'wpmatch_profiles_per_page', array(
        'label'       => __( 'Profiles per Page', 'wpmatch-theme' ),
        'description' => __( 'Number of profiles to display per page in the archive.', 'wpmatch-theme' ),
        'section'     => 'wpmatch_matching',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 1,
            'max'  => 50,
            'step' => 1,
        ),
    ) );
    
    // Show compatibility scores
    $wp_customize->add_setting( 'wpmatch_show_compatibility', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    
    $wp_customize->add_control( 'wpmatch_show_compatibility', array(
        'label'       => __( 'Show Compatibility Scores', 'wpmatch-theme' ),
        'description' => __( 'Display compatibility scores on profile cards.', 'wpmatch-theme' ),
        'section'     => 'wpmatch_matching',
        'type'        => 'checkbox',
    ) );
    
    // Colors Section
    $wp_customize->add_section( 'wpmatch_colors', array(
        'title'    => __( 'Color Settings', 'wpmatch-theme' ),
        'panel'    => 'wpmatch_theme_options',
        'priority' => 30,
    ) );
    
    // Primary Color
    $wp_customize->add_setting( 'wpmatch_primary_color', array(
        'default'           => '#007cba',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wpmatch_primary_color', array(
        'label'   => __( 'Primary Color', 'wpmatch-theme' ),
        'section' => 'wpmatch_colors',
    ) ) );
    
    // Secondary Color
    $wp_customize->add_setting( 'wpmatch_secondary_color', array(
        'default'           => '#f8f9fa',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'wpmatch_secondary_color', array(
        'label'   => __( 'Secondary Color', 'wpmatch-theme' ),
        'section' => 'wpmatch_colors',
    ) ) );
    
    // Footer Section
    $wp_customize->add_section( 'wpmatch_footer', array(
        'title'    => __( 'Footer Settings', 'wpmatch-theme' ),
        'panel'    => 'wpmatch_theme_options',
        'priority' => 40,
    ) );
    
    // Footer Text
    $wp_customize->add_setting( 'wpmatch_footer_text', array(
        'default'           => '',
        'sanitize_callback' => 'wp_kses_post',
    ) );
    
    $wp_customize->add_control( 'wpmatch_footer_text', array(
        'label'       => __( 'Footer Text', 'wpmatch-theme' ),
        'description' => __( 'Custom text to display in the footer. Leave empty for default.', 'wpmatch-theme' ),
        'section'     => 'wpmatch_footer',
        'type'        => 'textarea',
    ) );
}
add_action( 'customize_register', 'wpmatch_customize_register' );

/**
 * Sanitize select fields
 *
 * @param string $input   Input value.
 * @param object $setting Setting object.
 * @return string
 */
function wpmatch_sanitize_select( $input, $setting ) {
    $choices = $setting->manager->get_control( $setting->id )->choices;
    return array_key_exists( $input, $choices ) ? $input : $setting->default;
}

/**
 * Output custom CSS from customizer
 */
function wpmatch_customizer_css() {
    $primary_color = get_theme_mod( 'wpmatch_primary_color', '#007cba' );
    $secondary_color = get_theme_mod( 'wpmatch_secondary_color', '#f8f9fa' );
    
    if ( '#007cba' === $primary_color && '#f8f9fa' === $secondary_color ) {
        return; // Using default colors
    }
    
    ?>
    <style type="text/css">
        :root {
            --wpmatch-primary-color: <?php echo esc_html( $primary_color ); ?>;
            --wpmatch-secondary-color: <?php echo esc_html( $secondary_color ); ?>;
        }
        
        a {
            color: <?php echo esc_html( $primary_color ); ?>;
        }
        
        .wpmatch-load-more,
        .btn-primary {
            background-color: <?php echo esc_html( $primary_color ); ?>;
        }
        
        .wpmatch-container,
        .widget {
            background-color: <?php echo esc_html( $secondary_color ); ?>;
        }
        
        .main-navigation a:hover {
            color: <?php echo esc_html( $primary_color ); ?>;
        }
        
        .wpmatch-match-score {
            background-color: <?php echo esc_html( $primary_color ); ?>;
        }
    </style>
    <?php
}
add_action( 'wp_head', 'wpmatch_customizer_css' );