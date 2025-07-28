<?php
/**
 * The main template file
 *
 * @package WPMatch_Theme
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
    
    <?php if ( is_home() && ! is_front_page() ) : ?>
        <header>
            <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
        </header>
    <?php endif; ?>

    <?php while ( have_posts() ) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php
                if ( is_singular() ) :
                    the_title( '<h1 class="entry-title">', '</h1>' );
                else :
                    the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                endif;
                ?>
                
                <?php if ( 'post' === get_post_type() ) : ?>
                    <div class="entry-meta">
                        <?php
                        printf(
                            __( 'Posted on %1$s by %2$s', 'wpmatch-theme' ),
                            '<time class="entry-date published" datetime="' . esc_attr( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date() ) . '</time>',
                            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
                        );
                        ?>
                    </div>
                <?php endif; ?>
            </header>

            <?php if ( has_post_thumbnail() && ! is_singular() ) : ?>
                <div class="entry-thumbnail">
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail( 'wpmatch-featured' ); ?>
                    </a>
                </div>
            <?php endif; ?>

            <div class="entry-content">
                <?php
                if ( is_singular() ) :
                    the_content();
                else :
                    the_excerpt();
                endif;
                ?>
            </div>

            <?php if ( is_singular() ) : ?>
                <footer class="entry-footer">
                    <?php
                    $categories_list = get_the_category_list( esc_html__( ', ', 'wpmatch-theme' ) );
                    if ( $categories_list ) {
                        printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'wpmatch-theme' ) . '</span>', $categories_list );
                    }

                    $tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'wpmatch-theme' ) );
                    if ( $tags_list ) {
                        printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'wpmatch-theme' ) . '</span>', $tags_list );
                    }
                    ?>
                </footer>
            <?php endif; ?>
        </article>

    <?php endwhile; ?>

    <?php
    the_posts_navigation( array(
        'prev_text' => __( 'Older posts', 'wpmatch-theme' ),
        'next_text' => __( 'Newer posts', 'wpmatch-theme' ),
    ) );
    ?>

<?php else : ?>
    
    <section class="no-results not-found">
        <header class="page-header">
            <h1 class="page-title"><?php _e( 'Nothing here', 'wpmatch-theme' ); ?></h1>
        </header>

        <div class="page-content">
            <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
                <p>
                    <?php
                    printf(
                        wp_kses(
                            __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'wpmatch-theme' ),
                            array(
                                'a' => array(
                                    'href' => array(),
                                ),
                            )
                        ),
                        esc_url( admin_url( 'post-new.php' ) )
                    );
                    ?>
                </p>
            <?php elseif ( is_search() ) : ?>
                <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'wpmatch-theme' ); ?></p>
                <?php get_search_form(); ?>
            <?php else : ?>
                <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wpmatch-theme' ); ?></p>
                <?php get_search_form(); ?>
            <?php endif; ?>
        </div>
    </section>

<?php endif; ?>

<?php
get_footer();