<?php

?>
</main>
<footer>
	<?php if (has_nav_menu('footer')): ?>
		<nav aria-label="<?php esc_attr_e('Footer', 'html'); ?>">
			<?php

			wp_nav_menu(array(
				'theme_location' => 'footer',
				'container' => false,
				'items_wrap' => '<ul>%3$s</ul>',
				'menu_class' => '',
				'walker' => new HTML_Walker_Nav_Menu(),
				'fallback_cb' => false,
			));
			?>
		</nav>
	<?php endif; ?>
	<p>
		<small>
			<?php

			printf(
				/* translators: 1: year, 2: site name */
				esc_html__('© %1$s %2$s', 'html'),
				esc_html(gmdate('Y')),
				esc_html(get_bloginfo('name')),
			);
			?>
		</small>
	</p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
