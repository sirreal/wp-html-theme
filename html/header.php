<?php
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'html' ); ?></a>
<header>
	<?php the_custom_logo(); ?>
	<?php
	$blog_name        = get_bloginfo( 'name' );
	$blog_description = get_bloginfo( 'description', 'display' );
	if ( $blog_name ) :
		?>
		<p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php echo esc_html( $blog_name ); ?></a>
		</p>
	<?php endif; ?>
	<?php if ( $blog_description ) : ?>
		<p><?php echo esc_html( $blog_description ); ?></p>
	<?php endif; ?>
	<?php if ( has_nav_menu( 'primary' ) ) : ?>
		<nav aria-label="<?php esc_attr_e( 'Primary', 'html' ); ?>">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'items_wrap'     => '<ul>%3$s</ul>',
					'menu_class'     => '',
					'walker'         => new HTML_Walker_Nav_Menu(),
					'fallback_cb'    => false,
				)
			);
			?>
		</nav>
	<?php endif; ?>
</header>
<main id="main">
