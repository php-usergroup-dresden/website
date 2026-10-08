# Changelog

All notable changes to the website are documented in this file.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

When tagging a release, rename `[Unreleased]` to the version (e.g. `## [2.0.0] - 2026-10-08`) and push the tag
`v2.0.0`. The `release` job of `.github/workflows/test-build-deploy.yml` publishes that section as the GitHub Release
and fails if the section is missing.

## [Unreleased]

## [2.0.1] - 2026-10-08

### Added

- Light/dark toggle in the header. The choice is stored in the browser; without a choice the site follows the system
  setting.

### Fixed

- The logo in the header is always shown on a white background, so it stays readable in dark mode.

## [2.0.0] - 2026-10-08

### Added

- Dependency-free PHP build (PHP ≥ 8.2) with plain PHP templates, a small Markdown renderer and a test suite.
- Events, talks, sponsors, team and partners maintained as JSON in `data/`, text pages as Markdown in `content/`.
- iCalendar feed `events.ics` and a generated `sitemap.xml`.
- PHP Developer Day 2026 with program and talks.
- External links open in a new window and announce it.

### Changed

- Redesign with PHP indigo palette, self-hosted Recursive font and dark mode.
- Privacy policy and imprint updated (self-hosted assets, current legal references).
- The site is built, tested and deployed to GitHub Pages by GitHub Actions; the generated `docs/` is no longer
  committed.
- GitHub Releases are created from this changelog when a version tag is pushed.

### Removed

- Static page generator tooling (Composer), frontend libraries (FullCalendar, Moment.js) and Travis CI.

### Fixed

- Date of Meetup I/2025.

## [1.4.0] - 2021-09-16

### Changed

- Ongoing meetup and content updates, new board members and main address.

## [1.3.1] - 2017-04-27

### Changed

- PHP Developer Day 2017: ticket sale widget, sponsor and speaker updates.

## [1.3.0] - 2017-04-23

### Changed

- PHP Developer Day 2017: ticket links and sponsor packages.

## [1.2.0] - 2017-04-22

### Added

- PHP Developer Day 2017 website (German and English).

## [1.1.0] - 2017-04-04

### Added

- Page about hosting a meetup, map marker for the next event location, newsletter archive entries.

## [1.0.1] - 2016-11-08

### Fixed

- Base URL for the custom domain (`CNAME`).

## [1.0.0] - 2016-11-07

- Initial release of the website.
