<?php
/**
 * Front page template. Used when WP "show on front" is "static page".
 * Falls back through index.php when "show on front" is "latest posts".
 *
 * @package HTML
 */

get_header();

if ( 'page' === get_option( 'show_on_front' ) ) {
    while ( have_posts() ) :
        the_post();
        ?>
        <article <?php post_class(); ?>>
            <header>
                <h1><?php the_title(); ?></h1>
            </header>
            <?php the_content(); ?>
            <?php wp_link_pages(); ?>
        </article>
        <?php
    endwhile;
} else {
    if ( have_posts() ) :
        ?>
        <header>
            <h1><?php esc_html_e( 'Latest posts', 'html' ); ?></h1>
        </header>
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
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
            <?php
        endwhile;

        the_posts_pagination(
            array(
                'aria_label' => esc_attr__( 'Posts', 'html' ),
            )
        );
    endif;
}

get_footer();
