<?php

get_header();
?>

<header>
	<h1>
		<?php

		printf(
			/* translators: %s: search query */
			esc_html__('Search results for: %s', 'html'),
			'<q>' . esc_html(get_search_query()) . '</q>',
		);
		?>
	</h1>
	<?php get_search_form(); ?>
</header>

<?php if (have_posts()): ?>
	<?php

	while (have_posts()):
		the_post();
		get_template_part('template-parts/post-summary');
	endwhile;

	the_posts_pagination(array(
		'aria_label' => esc_attr__('Search results', 'html'),
	));
	?>
<?php else: ?>
	<p><?php esc_html_e('No results. Try a different search:', 'html'); ?></p>
	<?php get_search_form(); ?>
<?php endif; ?>

<?php get_footer();
