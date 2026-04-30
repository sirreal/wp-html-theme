<?php

// Walker that strips per-item classes from menu output. Emits:
//   <li><a href="...">Title</a>
//   <li><a href="..." aria-current="page">Title</a>  (current item)
class HTML_Walker_Nav_Menu extends Walker_Nav_Menu {
	public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
		$url = !empty($item->url) ? esc_url($item->url) : '';
		$title = esc_html($item->title);
		$aria = !empty($item->current) ? ' aria-current="page"' : '';

		$output .= '<li><a href="' . $url . '"' . $aria . '>' . $title . '</a>';
	}
}
