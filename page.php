<?php
/**
 * Single page template.
 *
 * @package HTML
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>
    <article>
        <header>
            <h1><?php the_title(); ?></h1>
        </header>

        <?php the_content(); ?>
    </article>

    <?php
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;

endwhile;

get_footer();
