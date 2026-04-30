<?php

get_header();

while (have_posts()):
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

	if (comments_open() || get_comments_number()):
		comments_template();
	endif;
endwhile;

get_footer();
