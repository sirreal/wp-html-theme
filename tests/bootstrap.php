<?php
require_once __DIR__ . '/../vendor/autoload.php';

if ( ! class_exists( 'Walker_Nav_Menu' ) ) {
    class Walker_Nav_Menu {
        public $tree_type = array( 'post_type', 'taxonomy', 'custom' );
        public $db_fields = array( 'parent' => 'menu_item_parent', 'id' => 'db_id' );
        public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {}
        public function end_el( &$output, $item, $depth = 0, $args = null ) {}
    }
}
