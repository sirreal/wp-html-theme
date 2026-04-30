<?php

get_header();

if ('page' === get_option('show_on_front')) {
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
		<?php endwhile;
} elseif (have_posts()) {
	?>
	<header>
		<h1><?php esc_html_e('Latest posts', 'html'); ?></h1>
	</header>
	<?php

	while (have_posts()):
		the_post();
		get_template_part('template-parts/post-summary');
	endwhile;

	the_posts_pagination(array(
		'aria_label' => esc_attr__('Posts', 'html'),
	));
}

get_footer();
