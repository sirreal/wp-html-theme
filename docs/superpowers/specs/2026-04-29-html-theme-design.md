# HTML Theme — Design Spec

**Date:** 2026-04-29
**Status:** Implemented (see "Implementation Deviations" at end)
**Target:** wordpress.org theme directory

## Premise

A WordPress theme named "HTML" whose purpose is to render content using semantic HTML primitives, with as little CSS and as little JavaScript as possible. The block editor remains available for content authoring; the theme is responsible only for the surrounding frame (templates, chrome, structural CSS).

This is a **classic theme** (PHP templates), not a block theme (FSE). Block themes generate wrapper `<div>`s that fight the premise. A classic theme allows the templates to emit `<article>`, `<header>`, `<nav>`, `<main>`, `<footer>` directly without WP-injected layout markup.

## Goals

1. Templates output pure semantic HTML — no theme-emitted classes except where strictly required for accessibility.
2. CSS is structural only (layout, breakpoints, grid). No typography, color, or decoration.
3. No JavaScript shipped by the theme.
4. Pass wordpress.org theme review.
5. Provide a "modern WordPress" content authoring experience (block editor enabled, no block restrictions).

## Non-Goals

- No Site Editor / FSE support.
- No widget areas / sidebars.
- No customizer panels beyond WP defaults (site identity, custom logo).
- No theme options page.
- No font loading, icon sets, SVG sprites, or build step.
- No site-wide admin-configurable sidebar adjacent to posts.

## Distribution

- License: GPLv2 or later.
- Theme slug: `html`.
- Text domain: `html`.
- All user-facing strings translatable.

## Authoring Model

- **Frame** (header, nav, post wrappers, footer): theme's responsibility, written in PHP, emits clean semantic HTML.
- **Content** (everything inside `the_content()`): author's responsibility. No block restrictions. Authors may use any block including Group/Columns/Cover; resulting wrapper divs are accepted as content-author choices, not theme pollution.
- **Sidebar layouts**: a content concern. Authors compose sidebar-style pages using Columns or Group blocks within content. The theme provides no admin-configurable site-wide sidebar.

## File Structure

```
html/
├── style.css                              # Required header + structural CSS
├── functions.php                          # Bootstraps inc/setup.php
├── theme.json                             # Minimal — disables defaults
├── readme.txt                             # wp.org required
├── screenshot.png                         # 1200x900
├── index.php                              # Required fallback template
├── front-page.php                         # Static front (when set)
├── home.php                               # Blog index
├── single.php                             # Single post
├── page.php                               # Single page
├── archive.php                            # Categories, tags, authors, dates
├── search.php
├── 404.php
├── comments.php                           # Uses comment_form() (Phase 1)
├── header.php
├── footer.php
└── inc/
    ├── setup.php                          # after_setup_theme + init hooks
    └── class-html-walker-nav-menu.php     # Custom nav walker
```

No `sidebar.php` (no widget areas).

## Templates — Output Shape

Illustrative HTML emitted by `single.php` (with `header.php` + `footer.php`):

```html
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- wp_head() -->
</head>
<body>
  <a href="#main" class="screen-reader-text">Skip to content</a>
  <header>
    <h1><a href="/" rel="home">Site Title</a></h1>
    <p>Site tagline</p>
    <nav aria-label="Primary">
      <ul>
        <li><a href="/about">About</a></li>
        <li><a href="/blog" aria-current="page">Blog</a></li>
      </ul>
    </nav>
  </header>
  <main id="main">
    <article>
      <header>
        <h1>Post Title</h1>
        <p>By <a href="…">Author</a> on <time datetime="2026-04-29">April 29, 2026</time></p>
      </header>
      <!-- the_content() output -->
      <footer>
        <p>Tagged: <a href="…">tag1</a>, <a href="…">tag2</a></p>
      </footer>
    </article>
    <section aria-label="Comments">
      <!-- comments_template() output -->
    </section>
  </main>
  <footer>
    <nav aria-label="Footer">
      <ul>…</ul>
    </nav>
    <p><small>© 2026 Site Title</small></p>
  </footer>
  <!-- wp_footer() -->
</body>
</html>
```

Notes:
- The skip link uses `class="screen-reader-text"` — the single accessibility-only class, required for visually-hidden but screen-reader-accessible content.
- `aria-current="page"` is emitted automatically by WP when `add_theme_support('html5', [..., 'navigation-widgets'])` is registered.
- `<nav>` elements have `aria-label` to disambiguate when multiple nav landmarks exist on a page.
- The class on the skip link is the only theme-emitted class. Everything else is bare semantic HTML.

## Navigation

### Locations

```php
register_nav_menus([
  'primary' => __('Primary', 'html'),
  'footer'  => __('Footer', 'html'),
]);
```

### Rendering

```php
wp_nav_menu([
  'theme_location' => 'primary',
  'container'      => false,
  'items_wrap'     => '<ul>%3$s</ul>',
  'menu_class'     => '',
  'walker'         => new HTML_Walker_Nav_Menu(),
  'fallback_cb'    => false,
]);
```

### Custom Walker

`HTML_Walker_Nav_Menu` extends `Walker_Nav_Menu` and overrides `start_el` to:
- Emit `<li>` with no class attribute.
- Emit `<a>` with only `href` (and `aria-current` when WP adds it via html5 support).
- Strip all `menu-item-*`, `current-*`, `page-item-*` classes.

### Fallback

If no menu is assigned to a location, render nothing (set `fallback_cb => false`). Do not auto-list pages — that introduces uncontrolled output.

## theme.json

Minimal configuration. Purpose: disable WP-generated CSS bloat and editor UI for features the theme doesn't honor.

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

Rationale:
- `appearanceTools: false` — hides WP's spacing/border/typography UI in the inspector. Authors styling individual blocks is fine; we just don't expose the heavy global UI.
- Defaults disabled — strips WP's preset palette, font sizes, gradients, duotones, and spacing scale. Editor inherits browser defaults.
- `contentSize` / `wideSize` — set so block layout (alignment, wide/full alignment) has sensible widths without further CSS.
- `styles: {}` empty — theme.json contributes no `style` rules. Browser defaults render content.

## CSS Strategy

Single `style.css` file containing the required WordPress theme header followed by structural CSS only.

```css
/*!
Theme Name: HTML
Theme URI: …
Author: …
Description: A minimal WordPress theme that uses HTML primitives.
Version: 0.1.0
Requires at least: 6.5
Tested up to: 6.7
Requires PHP: 7.4
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: html
Tags: minimal, classic-theme, accessibility-ready
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

Constraints:
- No font declarations.
- No color declarations beyond `color-scheme` (which opts in to UA dark mode rendering with system colors).
- No decorative styling.
- Media queries are allowed for layout-only rules (e.g., switching grid template, adjusting `max-width` at narrow screens). Add only when needed.

The stylesheet is enqueued normally. Inlining is acceptable as a future optimization but not part of the initial implementation.

## functions.php / inc/setup.php

`functions.php` is a thin bootstrap that requires `inc/setup.php` and `inc/class-html-walker-nav-menu.php`.

`inc/setup.php` registers, on `after_setup_theme`:

- `load_theme_textdomain('html', get_template_directory() . '/languages')`
- `add_theme_support('title-tag')`
- `add_theme_support('post-thumbnails')`
- `add_theme_support('automatic-feed-links')`
- `add_theme_support('custom-logo', [...])`
- `add_theme_support('responsive-embeds')`
- `add_theme_support('html5', ['comment-list', 'comment-form', 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets'])`
- `register_nav_menus(['primary' => …, 'footer' => …])`
- Set `$content_width` global to a sensible value (e.g., 800).

On `wp_enqueue_scripts`:

- Enqueue `style.css`.
- Do **not** dequeue core stylesheets (`wp-block-library`, `wp-block-library-theme`, `global-styles`, `classic-theme-styles`). The block editor is enabled with no block restrictions, so authors can freely insert layout-bearing blocks (Columns, Gallery, Cover, Buttons, Media&Text, image alignments) — these depend on `wp-block-library` to render correctly on the front end. Stripping it produces broken layouts, not minimal output. Where a stylesheet is genuinely unwanted, opt out by not enabling the feature (e.g., omit `add_theme_support('wp-block-styles')` to keep `wp-block-library-theme` from loading), rather than dequeuing after the fact.
- Remove emoji scripts/styles via `remove_action`/`remove_filter` (`print_emoji_detection_script`, `print_emoji_styles`, etc.). This is not a stylesheet dequeue and is the only WP default the theme actively suppresses.

The block editor is left enabled (no `use_block_editor_for_post` filter). No block-type allowlist.

## Comments

**Initial approach:** `comments.php` calls `comment_form()` and `wp_list_comments()` with default options. The `html5` theme support reduces the markup verbosity of both. Output is accepted as-is even though it includes some classes; the alternative (custom-rendered comments) is deferred.

**Deferred alternative:** replace `comment_form()` with a hand-rolled `<form>` and `wp_list_comments` with a custom `Walker_Comment` to emit pure semantic HTML. Considered if the initial approach's output proves too noisy in practice. Tracked in Implementation Phases as Phase 4.

The `comments.php` template wraps the comment block in a `<section aria-label="Comments">` landmark.

## Templates — Detailed Behavior

### `index.php`
Fallback template. Renders post list (loop with article excerpts) for any context not handled by a more specific template.

### `home.php`
Blog index. Loop of posts as `<article>` blocks with `<header>` (title, date, author), excerpt, and "read more" link.

### `front-page.php`
Used when the site is set to a static front page. Renders a single page's content. If no static front is configured, this template is unused (WP falls back to `home.php`).

### `single.php`
Single post. Outputs `<article>` with full content, post meta in header, tags/categories in footer, then `comments_template()` if comments are enabled for the post.

### `page.php`
Single page. Like `single.php` but no post meta and no tags/categories. Comments still rendered when enabled.

### `archive.php`
Used for category, tag, author, and date archives. Renders archive title (from `the_archive_title()`) and post loop. Pagination via `the_posts_pagination()` with `aria_label` set.

### `search.php`
Search results. Renders the search query, then loop of matching posts. If no results, prompts to refine search and offers a search form.

### `404.php`
Renders an `<h1>` with the not-found message and a link back home, plus a search form. No fancy graphics.

### `header.php`
Opens document, emits `<head>` (with `wp_head()`), opens `<body>` with `body_class()` (which adds WP-managed body classes — accepted as a WP integration point), emits skip link, site `<header>` with title/tagline/custom-logo, and primary `<nav>`.

### `footer.php`
Footer `<nav>` (footer menu), copyright `<p>`, `wp_footer()`, closing tags.

## wordpress.org Compliance

### Required
- Proper `style.css` header with all required fields.
- `readme.txt` with required headers.
- GPL-compatible license (GPLv2+).
- All output escaped (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` for known-good HTML content).
- All strings translatable with text domain `html`.
- Screenshot at 1200×900.
- Tested in current and previous WP versions.
- Internationalization-ready: `languages/` directory with `.pot` file generated.
- No remote requests from theme code.
- No admin pages, no telemetry, no tracking.

### Accessibility (theme tag: `accessibility-ready`)
- Skip link to `#main`.
- All form fields have associated `<label>` (via `comment_form()` defaults and html5 support).
- Sufficient color contrast — N/A given we ship no colors. UA defaults apply.
- Keyboard navigation: relies on default browser behavior; no JS to break it.
- `aria-current="page"` on active nav items (provided by WP html5 support).
- `aria-label` on `<nav>` elements when multiple are on the page.
- Heading hierarchy preserved (one `<h1>` per document scope).
- Focus visible: rely on UA `:focus-visible` defaults; do not override.

## Implementation Phases

### Phase 1 — Skeleton
- File scaffolding, `style.css` header, `readme.txt`.
- `functions.php` + `inc/setup.php` with theme supports, menu registration, asset enqueue.
- `header.php`, `footer.php`, `index.php` minimal but functional.
- Custom nav walker.
- `theme.json`.

### Phase 2 — Templates
- `single.php`, `page.php`, `archive.php`, `search.php`, `404.php`, `home.php`, `front-page.php`.
- `comments.php` using `comment_form()` and `wp_list_comments()`.

### Phase 3 — Polish
- Structural CSS rules (only as needed when something looks visually broken).
- Screenshot.
- `.pot` translation file.
- Cross-version testing.

### Phase 4 — Conditional iterations
- Custom-rendered comments (replacing `comment_form()`) if Phase 1 comments feel too noisy.
- Inlined critical CSS as a performance optimization.

## Open Questions / Future Decisions

These are intentionally unresolved and may be revisited:

- **Author byline rendering**: with or without avatar? Avatars introduce `<img>` from gravatar, considered acceptable but not required.
- **Pagination style**: numeric (`the_posts_pagination`) vs. prev/next (`the_posts_navigation`). Default to numeric.
- **Date format**: WP site default; not theme-controlled.
- **Custom post types**: not explicitly supported in templates; fall through to `index.php`. If a future need arises, add `single-{type}.php` then.

## Success Criteria

1. Activating the theme on a default WP install renders a working site with no console errors and no visible broken layout.
2. View source on a sample post: aside from the skip link's `screen-reader-text` class, the `body_class()` output, the `post_class()` output on `<article>` (added for wp.org compliance — see Deviations), and any classes inside `the_content()` (author's responsibility), the theme-emitted markup contains zero theme-specific class attributes and zero `id` attributes (except `id="main"` on `<main>` for the skip link target).
3. The theme passes the wp.org Theme Check plugin with zero REQUIRED issues and zero WARNINGS. RECOMMENDED items are reviewed and either implemented or documented as deliberate omissions.
4. No JavaScript is enqueued by the theme.
5. The only stylesheet enqueued by the theme is `style.css`.

## Implementation Deviations

Recorded after implementation (2026-04-29):

1. **Repo structure**: theme files were moved into a `html/` subdirectory at the repo root, separate from test infrastructure (`composer.json`, `phpunit.xml.dist`, `tests/`, `vendor/`, `docs/`). Required by Theme Check, which flags non-theme files in the theme directory as REQUIRED issues. Also aligns the directory name with the text-domain (`html`) per wp.org convention. The "html" directory is what ships; the rest is dev-only.

2. **`post_class()` on `<article>` tags**: added to `single.php`, `page.php`, `index.php`, `home.php`, `front-page.php`, `archive.php`, and `search.php`. Theme Check flags absence of `post_class()` as a REQUIRED issue. This emits classes like `post-X type-post status-publish hentry category-Y` on `<article>` — a deliberate concession to wp.org compliance. The success criterion was relaxed to permit this.

3. **`wp_link_pages()` after `the_content()`**: added to `single.php`, `page.php`, and `front-page.php` for posts paginated with `<!--nextpage-->`. Theme Check requirement.

4. **Theme tags**: revised from speculative `minimal, classic-theme, accessibility-ready, blog` to wp.org-recognized `blog, accessibility-ready, custom-logo, custom-menu, featured-images, threaded-comments, translation-ready`. Theme Check flagged unrecognized tags.

5. **Comment template `<section>`**: rendered inside `single.php`/`page.php` after `</article>` rather than alongside, to keep the comments landmark properly nested under `<main>`. (Trivial — matched what was already shipped.)

6. **Smoke test scope**: the "no theme-emitted classes" verification (`tests/smoke/check-no-classes.sh`) excludes WP-managed regions (`<head>`, `<body class>`, `<article>` interior, `wp_list_comments`/`comment_form` output, `get_search_form` output, `the_posts_pagination` output) since these are emitted via WP APIs invoked by the theme but not authored by it.

7. **Rolled back stylesheet dequeues (2026-04-30)**: the original spec (and initial implementation) dequeued `wp-block-library`, `wp-block-library-theme`, `global-styles`, and `classic-theme-styles`. Playwright testing against a post containing Columns, Gallery, Cover, Buttons, Media&Text, and aligned-image blocks showed this broke their front-end layout (columns stacked, cover overlay text invisible, buttons rendered as plain links, etc.). Since the theme allows the block editor with no block restrictions, stripping `wp-block-library` is incompatible with that stance — the "structural-only CSS" rule governs theme-authored CSS, not core's layout CSS for blocks the author chose to use. The dequeue function was removed entirely. Where a stylesheet is genuinely unwanted, the correct opt-out is not declaring the corresponding theme support (e.g., `wp-block-library-theme` only loads if `add_theme_support('wp-block-styles')` is declared, which this theme deliberately does not do).

8. **Open follow-up (2026-04-30)**: `alignwide` does not extend past content width — needs `add_theme_support('align-wide')` and `theme.json` `layout.contentSize`/`wideSize`. Surfaced during the dequeue-rollback testing; tracked as a follow-up rather than rolled into the same change.
