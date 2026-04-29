<?php
/**
 * Fallback template. Used when no more specific template exists.
 *
 * @package HTML
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class(); ?>>
            <header>
                <h1>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h1>
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
    <article>
        <h1><?php esc_html_e( 'Nothing here', 'html' ); ?></h1>
        <p><?php esc_html_e( 'No posts found.', 'html' ); ?></p>
        <?php get_search_form(); ?>
    </article>
<?php endif; ?>

<?php get_footer();
