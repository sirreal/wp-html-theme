<?php
/**
 * Run Theme Check programmatically against the installed theme.
 * Designed to be invoked via:
 *   wp-env run cli wp eval-file /var/www/html/wp-content/themes/html/tests/smoke/run-theme-check.php
 *
 * Reports REQUIRED / WARNING / RECOMMENDED / INFO counts and exits non-zero
 * if any REQUIRED issues are found.
 */

$theme_slug = 'html';

require_once WP_PLUGIN_DIR . '/theme-check/checkbase.php';

$theme = wp_get_theme( $theme_slug );
if ( ! $theme->exists() ) {
    fwrite( STDERR, "Theme not found: {$theme_slug}\n" );
    exit( 2 );
}

$success = run_themechecks_against_theme( $theme, $theme_slug );

global $themechecks;

$buckets = array(
    'REQUIRED'    => array(),
    'WARNING'     => array(),
    'RECOMMENDED' => array(),
    'INFO'        => array(),
    'OTHER'       => array(),
);

foreach ( $themechecks as $check ) {
    if ( ! ( $check instanceof themecheck ) ) {
        continue;
    }
    $errors = $check->getError();
    if ( empty( $errors ) ) {
        continue;
    }
    foreach ( $errors as $err ) {
        $msg = wp_strip_all_tags( $err );
        $msg = trim( preg_replace( '/\s+/', ' ', $msg ) );
        if ( stripos( $msg, 'REQUIRED' ) !== false ) {
            $buckets['REQUIRED'][] = $msg;
        } elseif ( stripos( $msg, 'WARNING' ) !== false ) {
            $buckets['WARNING'][] = $msg;
        } elseif ( stripos( $msg, 'RECOMMENDED' ) !== false ) {
            $buckets['RECOMMENDED'][] = $msg;
        } elseif ( stripos( $msg, 'INFO' ) !== false ) {
            $buckets['INFO'][] = $msg;
        } else {
            $buckets['OTHER'][] = $msg;
        }
    }
}

foreach ( $buckets as $level => $msgs ) {
    echo "\n=== {$level} (" . count( $msgs ) . ") ===\n";
    foreach ( $msgs as $m ) {
        echo "  - " . $m . "\n";
    }
}

echo "\nOverall: " . ( $success ? 'PASS' : 'FAIL' ) . "\n";
echo "Required count: " . count( $buckets['REQUIRED'] ) . "\n";
exit( count( $buckets['REQUIRED'] ) > 0 ? 1 : 0 );
