<?php
/**
 * Archive template (categories, tags, authors, dates).
 *
 * @package HTML
 */

get_header();
?>

<header>
    <h1><?php the_archive_title(); ?></h1>
    <?php
    $description = get_the_archive_description();
    if ( $description ) {
        echo '<div>' . wp_kses_post( $description ) . '</div>';
    }
    ?>
</header>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article <?php post_class(); ?>>
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
