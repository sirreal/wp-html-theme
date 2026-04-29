<?php
/**
 * Custom nav menu walker that emits no classes on <li>/<a>.
 *
 * @package HTML
 */

/**
 * Walker that strips per-item classes from menu output.
 *
 * Output shape: `<li><a href="...">Title</a>`
 * For the current item: `<li><a href="..." aria-current="page">Title</a>`
 */
class HTML_Walker_Nav_Menu extends Walker_Nav_Menu {

    /**
     * Emit a single menu item.
     *
     * @param string   $output Passed by reference.
     * @param object   $item   Menu item data object.
     * @param int      $depth  Depth.
     * @param stdClass $args   Args.
     * @param int      $id     ID.
     */
    public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        $url   = ! empty( $item->url ) ? esc_url( $item->url ) : '';
        $title = esc_html( $item->title );
        $aria  = ! empty( $item->current ) ? ' aria-current="page"' : '';

        $output .= '<li><a href="' . $url . '"' . $aria . '>' . $title . '</a>';
    }
}
