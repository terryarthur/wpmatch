            </main>

            <?php get_sidebar(); ?>
        </div>
    </div>

    <footer id="colophon" class="site-footer">
        <div class="container">
            <?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
                <div class="footer-widgets">
                    <?php dynamic_sidebar( 'footer-1' ); ?>
                </div>
            <?php endif; ?>
            
            <div class="site-info">
                <p>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. 
                <?php _e( 'Powered by', 'wpmatch-theme' ); ?> 
                <a href="<?php echo esc_url( __( 'https://wordpress.org/', 'wpmatch-theme' ) ); ?>">
                    <?php _e( 'WordPress', 'wpmatch-theme' ); ?>
                </a> 
                <?php _e( 'and WPMatch Theme.', 'wpmatch-theme' ); ?></p>
                
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'footer',
                    'menu_id'        => 'footer-menu',
                    'fallback_cb'    => false,
                    'depth'          => 1,
                ) );
                ?>
            </div>
        </div>
    </footer>
</div>

<?php wp_footer(); ?>

</body>
</html>