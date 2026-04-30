# HTML — a WordPress theme

A minimal classic WordPress theme that renders content with semantic HTML primitives. Structural CSS only. No JavaScript shipped by the theme. Block editor remains enabled for content authoring; the theme owns only the frame.

## Repo layout

```
html/                  # the theme (this is what ships)
  style.css            # theme header + structural CSS
  theme.json           # disables WP defaults
  *.php                # templates: header, footer, index, single, page,
                       #   archive, search, 404, comments, home, front-page
  inc/                 # setup.php + class-html-walker-nav-menu.php
  languages/html.pot   # translation template
  screenshot.png

tests/
  unit/                # PHPUnit + Brain Monkey
  smoke/               # bash + PHP scripts run against wp-env
docs/superpowers/      # design spec + implementation plan
.wp-env.json           # local WordPress dev/test env (Docker)
composer.json          # dev-only dependencies (phpunit, brain/monkey)
phpunit.xml.dist
```

The repo root is **not** the theme. The theme is `html/`. Development tooling lives at the repo root and is excluded from any wp.org submission package.

## Dev setup

Requirements: PHP 7.4+, Composer, Docker, [`@wordpress/env`](https://www.npmjs.com/package/@wordpress/env).

```sh
composer install        # PHPUnit + Brain Monkey
wp-env start            # WordPress on http://localhost:8890
wp-env run cli wp theme activate html
```

## Verification

```sh
# Unit tests (Walker_Nav_Menu output)
./vendor/bin/phpunit

# Smoke test: confirms theme-emitted markup contains no classes/IDs
# beyond the deliberate exceptions (see tests/smoke/check-no-classes.sh).
BASE_URL=http://localhost:8890 ./tests/smoke/check-no-classes.sh

# wp.org Theme Check (must show 0 REQUIRED, 0 WARNING)
cat tests/smoke/run-theme-check.php | wp-env run cli wp eval-file -

# PHP syntax of every shipped file
find html -name '*.php' -print0 | xargs -0 -n1 php -l
```

## Packaging for wordpress.org

Ship only the contents of `html/`:

```sh
( cd html && zip -r ../html.zip . -x '*.DS_Store' )
```

The resulting `html.zip` should contain only the theme. Nothing in `tests/`, `vendor/`, `composer.*`, `phpunit.xml.dist`, `docs/`, or this README is shipped.

## Design

- Spec: [`docs/superpowers/specs/2026-04-29-html-theme-design.md`](docs/superpowers/specs/2026-04-29-html-theme-design.md)
- Plan: [`docs/superpowers/plans/2026-04-29-html-theme.md`](docs/superpowers/plans/2026-04-29-html-theme.md)

The spec's "Implementation Deviations" section records concessions made for wp.org compliance (notably `post_class()` on `<article>` and `wp_link_pages()`).

## License

GPL-2.0-or-later. See [`LICENSE`](LICENSE).
