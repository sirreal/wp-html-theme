<?php
$heading = isset( $args['heading'] ) ? (int) $args['heading'] : 2;
$tag     = 'h' . max( 1, min( 6, $heading ) );
?>
<article <?php post_class(); ?>>
	<header>
		<<?php echo $tag; ?>>
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</<?php echo $tag; ?>>
		<p>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date() ); ?>
			</time>
		</p>
	</header>
	<?php if ( has_post_thumbnail() ) : ?>
		<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium' ); ?></a>
	<?php endif; ?>
	<?php the_excerpt(); ?>
</article>
