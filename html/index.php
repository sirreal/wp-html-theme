<?php
get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/post-summary', null, array( 'heading' => 1 ) );
	endwhile;

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
