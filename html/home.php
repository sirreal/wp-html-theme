<?php
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
	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/post-summary' );
	endwhile;

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
