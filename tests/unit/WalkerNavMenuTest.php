<?php
/**
 * @package HTML
 */

namespace HTMLTheme\Tests;

use Brain\Monkey;
use Brain\Monkey\Functions;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../inc/class-html-walker-nav-menu.php';

class WalkerNavMenuTest extends TestCase {

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        Functions\when( 'apply_filters' )->returnArg( 2 );
        // Default escape stubs: passthrough. Tests override with expect() when needed.
        Functions\when( 'esc_url' )->returnArg();
        Functions\when( 'esc_html' )->returnArg();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_start_el_emits_no_classes_on_li_or_a(): void {
        $walker = new \HTML_Walker_Nav_Menu();
        $output = '';
        $item   = (object) array(
            'ID'             => 1,
            'object_id'      => 1,
            'object'         => 'page',
            'title'          => 'About',
            'url'            => 'https://example.com/about',
            'attr_title'     => '',
            'target'         => '',
            'xfn'            => '',
            'description'    => '',
            'current'        => false,
            'classes'        => array( 'menu-item', 'menu-item-1', 'page-item-99' ),
        );
        $args   = (object) array( 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '' );

        $walker->start_el( $output, $item, 0, $args, 0 );

        $this->assertStringNotContainsString( 'class=', $output, 'No class attribute should be emitted' );
        $this->assertStringContainsString( '<li>', $output );
        $this->assertStringContainsString( '<a href="https://example.com/about">About</a>', $output );
    }

    public function test_start_el_emits_aria_current_for_current_item(): void {
        $walker = new \HTML_Walker_Nav_Menu();
        $output = '';
        $item   = (object) array(
            'ID'          => 2,
            'object_id'   => 2,
            'object'      => 'page',
            'title'       => 'Home',
            'url'         => 'https://example.com/',
            'attr_title'  => '',
            'target'      => '',
            'xfn'         => '',
            'description' => '',
            'current'     => true,
            'classes'     => array( 'menu-item', 'current-menu-item' ),
        );
        $args   = (object) array( 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '' );

        $walker->start_el( $output, $item, 0, $args, 0 );

        $this->assertStringContainsString( 'aria-current="page"', $output );
        $this->assertStringNotContainsString( 'class=', $output );
    }

    public function test_start_el_passes_url_and_title_through_escape_functions(): void {
        // Replace default passthrough stubs with tagging stubs to verify the
        // walker routes its inputs through the WP escape functions.
        Monkey\tearDown();
        Monkey\setUp();
        Functions\when( 'esc_url' )->alias( static function ( $u ) { return '[U:' . $u . ']'; } );
        Functions\when( 'esc_html' )->alias( static function ( $h ) { return '[H:' . $h . ']'; } );

        $walker = new \HTML_Walker_Nav_Menu();
        $output = '';
        $item   = (object) array(
            'ID'          => 3,
            'object_id'   => 3,
            'object'      => 'custom',
            'title'       => 'A & B',
            'url'         => 'https://example.com/?x=1&y=2',
            'attr_title'  => '',
            'target'      => '',
            'xfn'         => '',
            'description' => '',
            'current'     => false,
            'classes'     => array(),
        );
        $args   = (object) array( 'before' => '', 'after' => '', 'link_before' => '', 'link_after' => '' );

        $walker->start_el( $output, $item, 0, $args, 0 );

        $this->assertSame(
            '<li><a href="[U:https://example.com/?x=1&y=2]">[H:A & B]</a>',
            $output
        );
    }
}
