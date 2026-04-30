#!/usr/bin/env bash
# tests/smoke/check-no-classes.sh
#
# Verifies the theme-emitted markup is free of class/id attributes except
# the deliberate exceptions:
#   - class="screen-reader-text" on the skip link
#   - id="main" on <main>
#
# Strips out content authored by others (or WP):
#   - <head>...</head> (WP-managed metadata)
#   - <body class="..."> opening tag (body_class is WP-managed)
#   - <article>...</article> (author content via the_content())
#   - WP-managed pagination (the_posts_pagination outputs h2.screen-reader-text
#     and nav.navigation, both WP-emitted via the_posts_pagination args)
#
# The theme is allowed to call WP APIs that emit attributes; those are not
# considered "theme-emitted". The remaining frame (header, nav, main, footer)
# must be class/id-free except as noted.
set -euo pipefail

BASE_URL="${BASE_URL:-http://localhost:8890}"
PATHS=("/" "/?page_id=2" "/?p=1" "/?s=hello" "/no-such-page/")

failed=0
for path in "${PATHS[@]}"; do
    url="${BASE_URL}${path}"
    body="$(curl -s -L "$url")"

    # Strip well-known WP-managed regions. The theme is responsible for the
    # opening landmarks (header/nav/main/footer/section); WP fills the
    # interior of comment + form regions, which is accepted by spec.
    stripped="$(printf '%s' "$body" | perl -0777 -pe '
        s|<head\b.*?</head>||gs;
        s|<article\b.*?</article>||gs;
        s|<body\s[^>]*>|<body>|g;
        s|<nav class="navigation[^>]*>.*?</nav>||gs;
        s|<main id="main">|<main>|g;
        s|<a class="screen-reader-text"[^>]*>.*?</a>||g;
        # WP-managed: comments section interior (wp_list_comments + comment_form).
        s|<section aria-label="Comments">.*?</section>||gs;
        # WP-managed: search form (get_search_form()).
        s|<form\b[^>]*role="search"[^>]*>.*?</form>||gs;
        s|<form\b[^>]*\bclass="search-form"[^>]*>.*?</form>||gs;
        # WP-managed: scripts and styles emitted via wp_enqueue_*. The theme
        # ships no JS or inline styles of its own; any script/style tag in
        # the body comes from core (e.g., comment-reply) or other WP APIs.
        s|<script\b[^>]*>.*?</script>||gs;
        s|<style\b[^>]*>.*?</style>||gs;
    ')"

    bad="$(printf '%s' "$stripped" \
        | grep -oE '(class|id)="[^"]*"' \
        || true)"

    if [ -n "$bad" ]; then
        echo "FAIL: $url — unexpected class/id attributes:"
        printf '  %s\n' $bad
        failed=1
    else
        echo "OK:   $url"
    fi
done

exit "$failed"
