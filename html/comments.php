<?php

if (post_password_required()) {
	return;
}
?>

<section aria-label="<?php esc_attr_e('Comments', 'html'); ?>">
	<?php if (have_comments()): ?>
		<h2>
			<?php

			$count = get_comments_number();
			printf(
				/* translators: %s: comment count */
				esc_html(_n('%s comment', '%s comments', $count, 'html')),
				esc_html(number_format_i18n($count)),
			);
			?>
		</h2>

		<ol>
			<?php

			wp_list_comments(array(
				'style' => 'ol',
				'short_ping' => true,
			));
			?>
		</ol>

		<?php

		the_comments_pagination(array(
			'aria_label' => esc_attr__('Comments', 'html'),
		));
	endif;

	if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) { ?>
		<p><?php esc_html_e('Comments are closed.', 'html'); ?></p>
		<?php }

	comment_form();
	?>
</section>
