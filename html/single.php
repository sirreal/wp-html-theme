<?php
get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class(); ?>>
		<header>
			<h1><?php the_title(); ?></h1>
			<p>
				<?php
				printf(
					/* translators: 1: author name link, 2: post date */
					esc_html__( 'By %1$s on %2$s', 'html' ),
					sprintf(
						'<a href="%s">%s</a>',
						esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
						esc_html( get_the_author() )
					),
					sprintf(
						'<time datetime="%s">%s</time>',
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date() )
					)
				);
				?>
			</p>
		</header>

		<?php if ( has_post_thumbnail() ) : ?>
			<?php the_post_thumbnail(); ?>
		<?php endif; ?>

		<?php the_content(); ?>

		<?php wp_link_pages(); ?>

		<footer>
			<?php
			$categories = get_the_category_list( ', ' );
			$tags       = get_the_tag_list( '', ', ' );
			if ( $categories ) {
				printf(
					'<p>%s %s</p>',
					esc_html__( 'Categories:', 'html' ),
					wp_kses_post( $categories )
				);
			}
			if ( $tags ) {
				printf(
					'<p>%s %s</p>',
					esc_html__( 'Tags:', 'html' ),
					wp_kses_post( $tags )
				);
			}
			?>
		</footer>
	</article>

	<?php
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;

endwhile;

get_footer();
