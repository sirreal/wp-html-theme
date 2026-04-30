<?php
get_header();
?>

<article>
	<header>
		<h1><?php esc_html_e( 'Page not found', 'html' ); ?></h1>
	</header>

	<p><?php esc_html_e( 'That page does not exist. Try searching, or return to the home page.', 'html' ); ?></p>

	<?php get_search_form(); ?>

	<p>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php esc_html_e( 'Home', 'html' ); ?>
		</a>
	</p>
</article>

<?php get_footer();
