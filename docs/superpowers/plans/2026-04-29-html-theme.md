# HTML Theme Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build a minimal classic WordPress theme named "HTML" that emits semantic HTML primitives, with structural-only CSS and no JavaScript, ready for wordpress.org submission.

**Architecture:** Classic theme (PHP templates) with the block editor enabled for content. Theme owns the frame; authors own content. Single `style.css` with structural CSS only (layout, alignwide/alignfull, screen-reader-text). WordPress's class output (`body_class()`, `post_class()`, `wp_nav_menu()`, `wp_list_comments()`, etc.) is accepted as-is — the theme just doesn't add its own decoration on top. theme.json minimally configured to disable WP-generated CSS bloat.

**Tech Stack:** PHP 7.4+ (validated against 8.5), WordPress 6.5+, Composer + PHPUnit + Brain Monkey for unit tests, `wp-env` (Docker) for smoke testing, Theme Check plugin for wp.org compliance check.

**Spec:** `docs/superpowers/specs/2026-04-29-html-theme-design.md`

---

## Conventions

- Theme files live at the **repo root** (i.e. `/Users/jonsurrell/jon/wp-html-theme/{style.css, functions.php, ...}`). The repo IS the theme.
- Per-task PHP syntax check: `php -l <file>`. Expected output: `No syntax errors detected in <file>`.
- All commits are local (no push). Use clear commit messages.
- Never bypass hooks (`--no-verify`).

---

## Task 1: Create theme metadata files

**Files:**
- Create: `style.css`
- Create: `readme.txt`

- [ ] **Step 1: Create `style.css` with required theme header and the structural CSS from the spec**

```css
/*!
Theme Name: HTML
Theme URI: https://github.com/sirreal/wp-html-theme
Author: Jon Surrell
Author URI: https://jonsurrell.com
Description: A minimal WordPress theme that uses HTML primitives. Templates emit semantic HTML; CSS is structural only.
Version: 0.1.0
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: html
Tags: minimal, classic-theme, accessibility-ready, blog
*/

:root { color-scheme: light dark; }

body {
  max-width: 65ch;
  margin-inline: auto;
  padding-inline: 1rem;
}

.screen-reader-text {
  position: absolute;
  width: 1px; height: 1px;
  padding: 0; margin: -1px;
  overflow: hidden; clip: rect(0,0,0,0);
  white-space: nowrap; border: 0;
}

.screen-reader-text:focus {
  position: static;
  width: auto; height: auto;
  margin: 0; overflow: visible;
  clip: auto; white-space: normal;
}
```

- [ ] **Step 2: Create `readme.txt` with required wp.org headers**

```
=== HTML ===
Contributors: jonsurrell
Tags: minimal, classic-theme, accessibility-ready, blog
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A minimal WordPress theme that uses HTML primitives.

== Description ==

HTML is a minimal classic WordPress theme. The theme renders semantic HTML
elements (article, header, nav, main, footer). CSS is structural only —
layout, alignwide/alignfull, screen-reader-text — no typography, no colors,
no decoration. No JavaScript is shipped by the theme. Author content (the
block editor) is unrestricted; the theme is responsible only for the frame.

== Changelog ==

= 0.1.0 =
* Initial release.

== Copyright ==

HTML WordPress Theme, (C) 2026 Jon Surrell
HTML is distributed under the terms of the GNU GPL.
```

- [ ] **Step 3: Commit**

```bash
git add style.css readme.txt
git commit -m "Add theme metadata (style.css header, readme.txt)"
```

---

## Task 2: Create theme.json

**Files:**
- Create: `theme.json`

- [ ] **Step 1: Write `theme.json`**

```json
{
  "$schema": "https://schemas.wp.org/trunk/theme.json",
  "version": 3,
  "settings": {
    "appearanceTools": false,
    "color": {
      "defaultPalette": false,
      "defaultGradients": false,
      "defaultDuotone": false,
      "customDuotone": false
    },
    "typography": {
      "defaultFontSizes": false,
      "fluid": false
    },
    "layout": {
      "contentSize": "65ch",
      "wideSize": "80ch"
    },
    "spacing": {
      "defaultSpacingSizes": false
    }
  },
  "styles": {}
}
```

- [ ] **Step 2: Validate JSON**

Run: `php -r 'json_decode(file_get_contents("theme.json"), false, 512, JSON_THROW_ON_ERROR); echo "OK\n";'`
Expected: `OK`

- [ ] **Step 3: Commit**

```bash
git add theme.json
git commit -m "Add minimal theme.json (disable WP defaults)"
```

---

## Task 3: Set up testing infrastructure

**Files:**
- Create: `composer.json`
- Create: `phpunit.xml.dist`
- Create: `tests/bootstrap.php`
- Create: `.gitignore`

- [ ] **Step 1: Create `.gitignore`**

```
/vendor/
/node_modules/
.phpunit.cache/
.phpunit.result.cache
*.log
.DS_Store
```

- [ ] **Step 2: Create `composer.json`**

```json
{
  "name": "sirreal/html-wp-theme",
  "description": "Minimal HTML WordPress theme.",
  "license": "GPL-2.0-or-later",
  "type": "wordpress-theme",
  "require": {
    "php": ">=7.4"
  },
  "require-dev": {
    "phpunit/phpunit": "^9.6",
    "brain/monkey": "^2.6"
  },
  "config": {
    "sort-packages": true
  },
  "autoload-dev": {
    "psr-4": {
      "HTMLTheme\\Tests\\": "tests/"
    }
  }
}
```

- [ ] **Step 3: Install dev dependencies**

Run: `composer install --no-interaction`
Expected: succeeds, creates `vendor/` directory.

- [ ] **Step 4: Create `phpunit.xml.dist`**

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="tests/bootstrap.php"
         colors="true"
         cacheDirectory=".phpunit.cache"
         executionOrder="random"
         beStrictAboutOutputDuringTests="true"
         failOnWarning="true">
    <testsuites>
        <testsuite name="unit">
            <directory>tests/unit</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

- [ ] **Step 5: Create `tests/bootstrap.php`**

```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
```

- [ ] **Step 6: Run PHPUnit (no tests yet, verify config)**

Run: `./vendor/bin/phpunit --no-coverage`
Expected: "No tests executed!" or similar — confirms PHPUnit loads.

- [ ] **Step 7: Commit**

```bash
git add composer.json composer.lock phpunit.xml.dist tests/bootstrap.php .gitignore
git commit -m "Set up PHPUnit + Brain Monkey for unit tests"
```

---

## Task 4: Bootstrap functions.php and inc/setup.php

**Files:**
- Create: `functions.php`
- Create: `inc/setup.php`

- [ ] **Step 1: Create `functions.php` (thin bootstrap)**

```php
<?php
/**
 * HTML theme bootstrap.
 *
 * @package HTML
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once get_template_directory() . '/inc/setup.php';
```

- [ ] **Step 2: Create `inc/setup.php`**

```php
<?php
/**
 * Theme setup and asset enqueueing.
 *
 * @package HTML
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme setup. Registers theme supports and nav menu locations.
 */
function html_theme_setup() {
    load_theme_textdomain( 'html', get_template_directory() . '/languages' );

    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'responsive-embeds' );
    add_theme_support( 'custom-logo' );
    add_theme_support(
        'html5',
        array(
            'comment-list',
            'comment-form',
            'search-form',
            'gallery',
            'caption',
            'style',
            'script',
            'navigation-widgets',
        )
    );

    register_nav_menus(
        array(
            'primary' => __( 'Primary', 'html' ),
            'footer'  => __( 'Footer', 'html' ),
        )
    );
}
add_action( 'after_setup_theme', 'html_theme_setup' );

/**
 * Set $content_width if not already set.
 */
function html_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'html_content_width', 800 );
}
add_action( 'after_setup_theme', 'html_content_width', 0 );

/**
 * Enqueue the theme stylesheet.
 */
function html_enqueue_assets() {
    wp_enqueue_style(
        'html-style',
        get_stylesheet_uri(),
        array(),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'html_enqueue_assets' );

/**
 * Remove WP emoji output.
 *
 * Note: core stylesheets (`wp-block-library`, `global-styles`, `classic-theme-styles`)
 * are intentionally NOT dequeued — the block editor is enabled with no block
 * restrictions, so authors rely on `wp-block-library` for layout-bearing blocks
 * (Columns, Gallery, Cover, Buttons, Media&Text, image alignments). Stripping it
 * breaks the front-end. Where a stylesheet is unwanted, opt out by not declaring
 * the corresponding theme support rather than dequeuing after the fact.
 */
function html_disable_emojis() {
    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
    add_filter( 'tiny_mce_plugins', 'html_disable_emojis_tinymce' );
}
add_action( 'init', 'html_disable_emojis' );

/**
 * Filter out the wpemoji TinyMCE plugin.
 *
 * @param array $plugins TinyMCE plugins.
 * @return array
 */
function html_disable_emojis_tinymce( $plugins ) {
    if ( is_array( $plugins ) ) {
        return array_diff( $plugins, array( 'wpemoji' ) );
    }
    return array();
}
```

- [ ] **Step 3: Lint both files**

Run: `php -l functions.php && php -l inc/setup.php`
Expected: `No syntax errors detected in ...` for each.

- [ ] **Step 4: Commit**

```bash
git add functions.php inc/setup.php
git commit -m "Add theme bootstrap and setup"
```

---

## Task 5: ~~Custom Walker_Nav_Menu (TDD)~~ — OBSOLETE

**Status:** Removed. The original plan added an `HTML_Walker_Nav_Menu` that stripped `menu-item-*` / `current-*` / `page-item-*` classes from `<li>`/`<a>` output. That step is obsolete: the framing is "plain HTML, structural-only CSS, no theme-shipped JS," not "no classes." Classes emitted by `wp_nav_menu()` are WP's output — accepted as-is. Use the default walker.

If you previously created `inc/class-html-walker-nav-menu.php` and `tests/unit/WalkerNavMenuTest.php`, delete them and remove the `require_once` from `functions.php`.

---

## Task 6: header.php and footer.php

**Files:**
- Create: `header.php`
- Create: `footer.php`

- [ ] **Step 1: Create `header.php`**

```php
<?php
/**
 * Site header.
 *
 * @package HTML
 */
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
    <?php if ( has_custom_logo() ) : ?>
        <?php the_custom_logo(); ?>
    <?php endif; ?>
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
    <?php
    if ( has_nav_menu( 'primary' ) ) {
        ?>
        <nav aria-label="<?php esc_attr_e( 'Primary', 'html' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'items_wrap'     => '<ul>%3$s</ul>',
                    'menu_class'     => '',
                    'fallback_cb'    => false,
                )
            );
            ?>
        </nav>
        <?php
    }
    ?>
</header>
<main id="main">
```

NOTE: site title is wrapped in `<p>` rather than `<h1>` because `<h1>` is reserved for the page-level heading (post title on singular pages, archive title on archives, "Page not found" on 404). This avoids competing `<h1>`s.

- [ ] **Step 2: Create `footer.php`**

```php
<?php
/**
 * Site footer.
 *
 * @package HTML
 */
?>
</main>
<footer>
    <?php
    if ( has_nav_menu( 'footer' ) ) {
        ?>
        <nav aria-label="<?php esc_attr_e( 'Footer', 'html' ); ?>">
            <?php
            wp_nav_menu(
                array(
                    'theme_location' => 'footer',
                    'container'      => false,
                    'items_wrap'     => '<ul>%3$s</ul>',
                    'menu_class'     => '',
                    'fallback_cb'    => false,
                )
            );
            ?>
        </nav>
        <?php
    }
    ?>
    <p>
        <small>
            <?php
            printf(
                /* translators: 1: year, 2: site name */
                esc_html__( '© %1$s %2$s', 'html' ),
                esc_html( gmdate( 'Y' ) ),
                esc_html( get_bloginfo( 'name' ) )
            );
            ?>
        </small>
    </p>
</footer>
<?php wp_footer(); ?>
</body>
</html>
```

- [ ] **Step 3: Lint**

Run: `php -l header.php && php -l footer.php`
Expected: `No syntax errors detected` for each.

- [ ] **Step 4: Commit**

```bash
git add header.php footer.php
git commit -m "Add header.php and footer.php with semantic landmarks and nav menus"
```

---

## Task 7: index.php (fallback template)

**Files:**
- Create: `index.php`

- [ ] **Step 1: Create `index.php`**

```php
<?php
/**
 * Fallback template. Used when no more specific template exists.
 *
 * @package HTML
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article>
            <header>
                <h1>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h1>
                <p>
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </p>
            </header>
            <?php the_excerpt(); ?>
        </article>
    <?php endwhile; ?>

    <?php
    the_posts_pagination(
        array(
            'aria_label' => esc_attr__( 'Posts', 'html' ),
        )
    );
    ?>
<?php else : ?>
    <article>
        <h1><?php esc_html_e( 'Nothing here', 'html' ); ?></h1>
        <p><?php esc_html_e( 'No posts found.', 'html' ); ?></p>
        <?php get_search_form(); ?>
    </article>
<?php endif; ?>

<?php get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l index.php`
Expected: `No syntax errors detected in index.php`.

- [ ] **Step 3: Commit**

```bash
git add index.php
git commit -m "Add index.php fallback template (post list)"
```

---

## Task 8: home.php (blog index)

**Files:**
- Create: `home.php`

- [ ] **Step 1: Create `home.php`**

`home.php` is identical in shape to `index.php` for this theme — the blog index simply lists posts. Differentiate only by emitting an archive heading.

```php
<?php
/**
 * Blog posts index. Used as the front page (when set to "Latest posts")
 * or as the posts page when a static front is configured.
 *
 * @package HTML
 */

get_header();
?>

<header>
    <h1>
        <?php
        if ( is_home() && ! is_front_page() ) {
            echo esc_html( get_the_title( get_option( 'page_for_posts' ) ) );
        } else {
            esc_html_e( 'Latest posts', 'html' );
        }
        ?>
    </h1>
</header>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article>
            <header>
                <h2>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p>
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </p>
            </header>
            <?php the_excerpt(); ?>
        </article>
    <?php endwhile; ?>

    <?php
    the_posts_pagination(
        array(
            'aria_label' => esc_attr__( 'Posts', 'html' ),
        )
    );
    ?>
<?php else : ?>
    <p><?php esc_html_e( 'No posts found.', 'html' ); ?></p>
<?php endif; ?>

<?php get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l home.php`
Expected: `No syntax errors detected in home.php`.

- [ ] **Step 3: Commit**

```bash
git add home.php
git commit -m "Add home.php (blog index)"
```

---

## Task 9: front-page.php

**Files:**
- Create: `front-page.php`

- [ ] **Step 1: Create `front-page.php`**

```php
<?php
/**
 * Front page template. Used when WP "show on front" is "static page".
 * Falls back through index.php when "show on front" is "latest posts".
 *
 * @package HTML
 */

get_header();

if ( 'page' === get_option( 'show_on_front' ) ) {
    while ( have_posts() ) :
        the_post();
        ?>
        <article>
            <header>
                <h1><?php the_title(); ?></h1>
            </header>
            <?php the_content(); ?>
        </article>
        <?php
    endwhile;
} else {
    // "Latest posts" front: defer to home.php behavior inline.
    if ( have_posts() ) :
        ?>
        <header>
            <h1><?php esc_html_e( 'Latest posts', 'html' ); ?></h1>
        </header>
        <?php
        while ( have_posts() ) :
            the_post();
            ?>
            <article>
                <header>
                    <h2>
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h2>
                    <p>
                        <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                            <?php echo esc_html( get_the_date() ); ?>
                        </time>
                    </p>
                </header>
                <?php the_excerpt(); ?>
            </article>
            <?php
        endwhile;

        the_posts_pagination(
            array(
                'aria_label' => esc_attr__( 'Posts', 'html' ),
            )
        );
    endif;
}

get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l front-page.php`
Expected: `No syntax errors detected in front-page.php`.

- [ ] **Step 3: Commit**

```bash
git add front-page.php
git commit -m "Add front-page.php (handles static and latest-posts front)"
```

---

## Task 10: single.php

**Files:**
- Create: `single.php`

- [ ] **Step 1: Create `single.php`**

```php
<?php
/**
 * Single post template.
 *
 * @package HTML
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>
    <article>
        <header>
            <h1><?php the_title(); ?></h1>
            <p>
                <?php
                printf(
                    /* translators: 1: author name link, 2: post date */
                    esc_html__( 'By %1$s on %2$s', 'html' ),
                    sprintf(
                        '<a href="%s">%s</a>',
                        esc_url( get_author_posts_url( (int) get_the_author_meta( 'ID' ) ) ),
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

        <?php the_content(); ?>

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
```

- [ ] **Step 2: Lint**

Run: `php -l single.php`
Expected: `No syntax errors detected in single.php`.

- [ ] **Step 3: Commit**

```bash
git add single.php
git commit -m "Add single.php (single post with author, date, taxonomies, comments)"
```

---

## Task 11: page.php

**Files:**
- Create: `page.php`

- [ ] **Step 1: Create `page.php`**

```php
<?php
/**
 * Single page template.
 *
 * @package HTML
 */

get_header();

while ( have_posts() ) :
    the_post();
    ?>
    <article>
        <header>
            <h1><?php the_title(); ?></h1>
        </header>

        <?php the_content(); ?>
    </article>

    <?php
    if ( comments_open() || get_comments_number() ) :
        comments_template();
    endif;

endwhile;

get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l page.php`
Expected: `No syntax errors detected in page.php`.

- [ ] **Step 3: Commit**

```bash
git add page.php
git commit -m "Add page.php (single page)"
```

---

## Task 12: archive.php

**Files:**
- Create: `archive.php`

- [ ] **Step 1: Create `archive.php`**

```php
<?php
/**
 * Archive template (categories, tags, authors, dates).
 *
 * @package HTML
 */

get_header();
?>

<header>
    <h1><?php the_archive_title(); ?></h1>
    <?php
    $description = get_the_archive_description();
    if ( $description ) {
        echo '<div>' . wp_kses_post( $description ) . '</div>';
    }
    ?>
</header>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article>
            <header>
                <h2>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p>
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </p>
            </header>
            <?php the_excerpt(); ?>
        </article>
    <?php endwhile; ?>

    <?php
    the_posts_pagination(
        array(
            'aria_label' => esc_attr__( 'Posts', 'html' ),
        )
    );
    ?>
<?php else : ?>
    <p><?php esc_html_e( 'No posts found.', 'html' ); ?></p>
<?php endif; ?>

<?php get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l archive.php`
Expected: `No syntax errors detected in archive.php`.

- [ ] **Step 3: Commit**

```bash
git add archive.php
git commit -m "Add archive.php (category/tag/author/date archives)"
```

---

## Task 13: search.php

**Files:**
- Create: `search.php`

- [ ] **Step 1: Create `search.php`**

```php
<?php
/**
 * Search results template.
 *
 * @package HTML
 */

get_header();
?>

<header>
    <h1>
        <?php
        printf(
            /* translators: %s: search query */
            esc_html__( 'Search results for: %s', 'html' ),
            '<q>' . esc_html( get_search_query() ) . '</q>'
        );
        ?>
    </h1>
    <?php get_search_form(); ?>
</header>

<?php if ( have_posts() ) : ?>
    <?php while ( have_posts() ) : the_post(); ?>
        <article>
            <header>
                <h2>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h2>
                <p>
                    <time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
                        <?php echo esc_html( get_the_date() ); ?>
                    </time>
                </p>
            </header>
            <?php the_excerpt(); ?>
        </article>
    <?php endwhile; ?>

    <?php
    the_posts_pagination(
        array(
            'aria_label' => esc_attr__( 'Search results', 'html' ),
        )
    );
    ?>
<?php else : ?>
    <p><?php esc_html_e( 'No results. Try a different search:', 'html' ); ?></p>
    <?php get_search_form(); ?>
<?php endif; ?>

<?php get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l search.php`
Expected: `No syntax errors detected in search.php`.

- [ ] **Step 3: Commit**

```bash
git add search.php
git commit -m "Add search.php (search results)"
```

---

## Task 14: 404.php

**Files:**
- Create: `404.php`

- [ ] **Step 1: Create `404.php`**

```php
<?php
/**
 * 404 not-found template.
 *
 * @package HTML
 */

get_header();
?>

<article>
    <header>
        <h1><?php esc_html_e( 'Page not found', 'html' ); ?></h1>
    </header>

    <p><?php esc_html_e( 'That page does not exist. Try searching, or return to the home page.', 'html' ); ?></p>

    <?php get_search_form(); ?>

    <p>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <?php esc_html_e( 'Home', 'html' ); ?>
        </a>
    </p>
</article>

<?php get_footer();
```

- [ ] **Step 2: Lint**

Run: `php -l 404.php`
Expected: `No syntax errors detected in 404.php`.

- [ ] **Step 3: Commit**

```bash
git add 404.php
git commit -m "Add 404.php (not found template)"
```

---

## Task 15: comments.php

**Files:**
- Create: `comments.php`

- [ ] **Step 1: Create `comments.php`**

```php
<?php
/**
 * Comments template — uses comment_form() and wp_list_comments() defaults.
 *
 * @package HTML
 */

if ( post_password_required() ) {
    return;
}
?>

<section aria-label="<?php esc_attr_e( 'Comments', 'html' ); ?>">
    <?php if ( have_comments() ) : ?>
        <h2>
            <?php
            $count = get_comments_number();
            printf(
                esc_html(
                    /* translators: %s: comment count */
                    _n( '%s comment', '%s comments', $count, 'html' )
                ),
                esc_html( number_format_i18n( $count ) )
            );
            ?>
        </h2>

        <ol>
            <?php
            wp_list_comments(
                array(
                    'style'      => 'ol',
                    'short_ping' => true,
                )
            );
            ?>
        </ol>

        <?php
        the_comments_pagination(
            array(
                'aria_label' => esc_attr__( 'Comments', 'html' ),
            )
        );
    endif;

    if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) {
        ?>
        <p><?php esc_html_e( 'Comments are closed.', 'html' ); ?></p>
        <?php
    }

    comment_form();
    ?>
</section>
```

- [ ] **Step 2: Lint**

Run: `php -l comments.php`
Expected: `No syntax errors detected in comments.php`.

- [ ] **Step 3: Commit**

```bash
git add comments.php
git commit -m "Add comments.php using comment_form() and wp_list_comments() defaults"
```

---

## Task 16: Set up wp-env for smoke testing

**Files:**
- Create: `.wp-env.json`

- [ ] **Step 1: Create `.wp-env.json`**

```json
{
  "core": "WordPress/WordPress#6.7",
  "phpVersion": "8.2",
  "themes": ["."],
  "config": {
    "WP_DEBUG": true,
    "WP_DEBUG_DISPLAY": true,
    "WP_DEBUG_LOG": true
  },
  "plugins": ["https://downloads.wordpress.org/plugin/theme-check.zip"]
}
```

- [ ] **Step 2: Start wp-env**

Run: `wp-env start`
Expected: Docker pulls images, WP starts. Output ends with site URLs (e.g. `http://localhost:8888`). May take several minutes on first run.

- [ ] **Step 3: Activate the theme via wp-env CLI**

Run: `wp-env run cli wp theme activate html`
Expected: `Success: Switched to 'HTML' theme.`

- [ ] **Step 4: Sanity-check the front page renders**

Run: `curl -s http://localhost:8888/ | head -20`
Expected: HTML output beginning with `<!doctype html>`.

- [ ] **Step 5: Commit**

```bash
git add .wp-env.json
git commit -m "Add wp-env config for local testing"
```

---

## Task 17: ~~Smoke test "no theme-emitted classes"~~ — OBSOLETE

**Status:** Removed. The premise (theme-emitted markup must contain no class attributes except `screen-reader-text`) does not match the actual goal: plain HTML in templates, structural-only CSS, no theme-shipped JS. Classes that WordPress APIs emit (`body_class()`, `post_class()`, `wp_nav_menu()`, `wp_list_comments()`, `wp_link_pages()`, etc.) are accepted as-is and would have produced false-positive failures here. Seeding sample content (from the original Step 1) is still useful for manual verification but is no longer gated by an automated check.

If you previously created `tests/smoke/check-no-classes.sh`, delete it.

---

## Task 18: Smoke test "no theme-enqueued JavaScript"

- [ ] **Step 1: Inspect enqueued scripts on the front page**

Run:
```bash
curl -s http://localhost:8888/ | grep -oE '<script[^>]*src="[^"]+"[^>]*>' | grep -v wp-includes | grep -v wp-content/plugins || echo 'OK: no theme-enqueued scripts'
```
Expected: prints `OK: no theme-enqueued scripts`. (WP core/admin scripts from `wp-includes` are acceptable.)

If theme-enqueued scripts appear, identify the source in `inc/setup.php` or templates and remove the enqueue.

No commit — this is a verification step.

---

## Task 19: Run Theme Check plugin

- [ ] **Step 1: Activate Theme Check and run it**

Run:
```bash
wp-env run cli wp plugin activate theme-check
wp-env run cli wp theme-check html 2>&1 | tail -50
```
Expected: Theme Check completes. Goal: zero REQUIRED-level errors. WARNINGS reviewed and either fixed or documented.

If the `wp theme-check` subcommand isn't available, do this manually: open `http://localhost:8888/wp-admin/themes.php?page=themecheck`, run against "HTML" theme, and copy results.

- [ ] **Step 2: Address any required-level errors**

For each REQUIRED issue, edit the relevant file to resolve. Re-run.

- [ ] **Step 3: Commit any fixes**

```bash
git add -p
git commit -m "Fix Theme Check required issues: <list>"
```

(Skip commit if no fixes were needed.)

---

## Task 20: Generate translation template

**Files:**
- Create: `languages/html.pot`

- [ ] **Step 1: Generate the .pot file with WP-CLI**

Run:
```bash
mkdir -p languages
wp-env run cli wp i18n make-pot /var/www/html/wp-content/themes/html /var/www/html/wp-content/themes/html/languages/html.pot --domain=html
```

(Note: wp-env mounts the theme at `/var/www/html/wp-content/themes/html`.)

If the command fails because make-pot is unavailable, install it:
`wp-env run cli wp package install wp-cli/i18n-command`
then retry.

Expected: `languages/html.pot` is created with strings extracted from PHP files.

- [ ] **Step 2: Verify the .pot file**

Run: `head -30 languages/html.pot`
Expected: standard PO header followed by `msgid`/`msgstr` entries.

- [ ] **Step 3: Commit**

```bash
git add languages/html.pot
git commit -m "Generate translation template (html.pot)"
```

---

## Task 21: Add screenshot

**Files:**
- Create: `screenshot.png` (1200×900)

- [ ] **Step 1: Generate a placeholder screenshot**

Use ImageMagick to produce a minimal screenshot until a real one is captured:

Run:
```bash
if command -v magick >/dev/null 2>&1; then
    magick -size 1200x900 xc:white -fill black -gravity center \
        -pointsize 96 -annotate 0 "HTML" screenshot.png
elif command -v convert >/dev/null 2>&1; then
    convert -size 1200x900 xc:white -fill black -gravity center \
        -pointsize 96 -annotate 0 "HTML" screenshot.png
else
    echo "ImageMagick not installed — skip and add screenshot manually."
fi
```
Expected: `screenshot.png` is created at 1200×900. If ImageMagick is unavailable, take a real screenshot of the rendered theme at 1200×900 and save it as `screenshot.png`.

- [ ] **Step 2: Verify dimensions**

Run: `file screenshot.png`
Expected: output mentions `1200 x 900`.

- [ ] **Step 3: Commit**

```bash
git add screenshot.png
git commit -m "Add theme screenshot (placeholder)"
```

---

## Task 22: Final verification against success criteria

This task has no code edits — it's a checklist run.

- [ ] **Criterion 1: Theme activates without errors**

Run: `wp-env run cli wp theme list --status=active --format=csv`
Expected: shows `html,active`. Visit `http://localhost:8888/` — no PHP errors visible. Browser console shows no JS errors.

- [ ] **Criterion 2: Templates author plain semantic HTML**

Manually view source on a sample post (front page, single, archive, search, 404). Confirm the theme's templates emit `<article>`, `<header>`, `<nav>`, `<main>`, `<footer>` directly without theme-added decorative classes. WordPress's own class output (`body_class()`, `post_class()`, `wp_nav_menu()`, `wp_list_comments()`, `wp_link_pages()`, etc.) is expected and acceptable.

- [ ] **Criterion 3: Theme Check passes**

Re-run the Theme Check from Task 19. Required: zero REQUIRED issues. Document any unresolved warnings in `readme.txt` notes.

- [ ] **Criterion 4: No JavaScript enqueued by theme**

Re-run Task 18 verification.

- [ ] **Criterion 5: Only one stylesheet enqueued by theme**

Run:
```bash
curl -s http://localhost:8888/ | grep -oE '<link[^>]*rel="stylesheet"[^>]*>' | grep -v wp-includes | grep -v wp-content/plugins
```
Expected: exactly one `<link>` referring to `style.css`.

- [ ] **Criterion 6: PHPUnit unit tests pass**

Run: `./vendor/bin/phpunit`
Expected: all tests pass.

- [ ] **Criterion 7: All PHP files lint clean**

Run: `find . -name '*.php' -not -path './vendor/*' -not -path './node_modules/*' -print0 | xargs -0 -n1 php -l`
Expected: every file reports `No syntax errors detected`.

- [ ] **Step 8: Final commit / tag**

If any fixes were made during verification, commit them. Optionally tag a 0.1.0 release:

```bash
git tag -a v0.1.0 -m "Initial HTML theme release"
```

---

## Out of scope (deferred)

Tracked for future iterations, not part of this plan:

- Inlined critical CSS for performance.
- Alternate template variants (`single-{post-type}.php`, etc.).
- Customizer integration beyond WP defaults.
- Real theme screenshot (a real screenshot taken from a populated test site, replacing the placeholder).
