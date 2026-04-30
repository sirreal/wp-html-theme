<?php
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
