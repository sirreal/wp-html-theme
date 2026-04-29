<?php
/**
 * Blog posts index. Used as the front page (when set to "Latest posts")
 * or as the posts page when a static front is configured.
 *
 * @package HTML
 */

get_header();
?>

<header>
    <h1>
        <?php
        if ( is_home() && ! is_front_page() ) {
            echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) );
        } else {
            esc_html_e( 'Latest posts', 'html' );
        }
        ?>
    </h1>
</header>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article>
            <header>
                <h2>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p>
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </p>
            </header>
            <?php the_excerpt(); ?>
        </article>
    <?php endwhile; ?>

    <?php
    the_posts_pagination(
        array(
            'aria_label' => esc_attr__( 'Posts', 'html' ),
        )
    );
    ?>
<?php else : ?>
    <p><?php esc_html_e( 'No posts found.', 'html' ); ?></p>
<?php endif; ?>

<?php get_footer();
