# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Overview

Static website of the PHP USERGROUP DRESDEN e.V. (https://phpug-dresden.org). Content is German. Built by a
dependency-free PHP (≥ 8.2) script – no Composer, no npm, no CDN. Keeping it dependency-free is a deliberate goal:
do not add packages, frameworks or external resources (fonts, scripts, icon kits).

## Commands

```bash
php build.php                      # build into ./docs (committed, served by GitHub Pages)
php build.php --today=2025-12-01   # simulate another date (upcoming vs. past events) – never commit such a build
php tests/run.php                  # all tests
php tests/run.php Markdown         # tests whose file/description contains "Markdown"
make serve                         # build + php -S 127.0.0.1:8000 -t docs
```

Deployment: GitHub Pages serves `docs/` from `master` ("Deploy from a branch"). `docs/` is generated – never edit it by
hand; after changing `data/`, `content/`, `templates/`, `src/` or `static/`, run `php build.php` and commit `docs/`
together with the source change. `docs/.nojekyll` (written by the build) stops GitHub from running Jekyll.
"Nächstes Event" only advances when the site is rebuilt and committed. `.github/workflows/ci.yml` runs tests + build
on pushes and pull requests but does not deploy.

## Architecture

- `data/*.json` – structured content: `events.json`, `talks.json`, `sponsors.json`, `team.json`, `partners.json`,
  `site.json` (name, nav, social links, list of Markdown pages). The schema is documented in `data/README.md`;
  keep that file in sync when changing fields.
- Talks reference their event by date (`talks[].event` = `events[].date`); `Content::eventsWithTalks()` joins them
  and fails on unknown references or duplicate dates. All validation errors are `InvalidContent` with a
  `file[index].field` context, printed by `build.php`.
- `content/*.md` – free-text pages, registered in `site.json` → `pages` (path → source). The page title is the
  first `# H1`. `content/sponsoring.md` is only the intro of the generated sponsoring page.
- `src/Markdown.php` – intentionally small Markdown subset (see class doc). Lines starting with an HTML tag form a
  raw HTML block until the next blank line.
- `src/Build.php` – orchestrates: reset `docs/`, copy `static/`, render generated pages (`/`, `/events.html`,
  `/talks.html`, `/sponsoring.html`, `/404.html`) and Markdown pages, write `events.ics` and `sitemap.xml`.
  Every rendered page passes through `ExternalLinks`, which adds `target="_blank" rel="noopener"` to links on other
  hosts – templates and content never set `target` themselves.
- `templates/*.php` – plain PHP templates; inside them `$this` is the `Renderer` (`e()` escapes, `md()` renders
  Markdown, `date()` formats German dates, `partial()` includes `templates/partials/*`). Every page is wrapped by
  `layout.php`. Always escape data with `$this->e()`.
- `static/` – copied verbatim: `css/site.css` (the only stylesheet, design tokens in `:root`, dark mode via
  `prefers-color-scheme`), images, downloads, newsletter archive, `CNAME`.
- Existing public URLs (e.g. `/presse.html`, `/events/2016/php-developer-day.html`) must stay stable.

Tests use a tiny runner (`tests/run.php`, assertions in `tests/assert.php`): each `tests/*Test.php` returns an array
of `description => callable`.
