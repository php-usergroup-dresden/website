# Contributing

Contributions are **welcome** and will be fully **credited**. We accept contributions via pull requests on
[GitHub](https://github.com/php-usergroup-dresden/website).

## Edit the website

You only need PHP 8.2 or newer.

1. Fork and clone the repository.
2. Make your changes:
   - **New event or talk, sponsor, team member:** edit the JSON files in `data/` – see [`data/README.md`](../data/README.md).
   - **Text pages:** edit the Markdown files in `content/`. New pages must be registered in `data/site.json` → `pages`.
   - **Images and downloads:** add them to `static/images/` or `static/downloads/` and reference them as `/images/…`.
   - **Layout:** templates are plain PHP in `templates/`, styles in `static/css/site.css`.
3. Build and check locally:

   ```bash
   php tests/run.php
   php build.php && php -S 127.0.0.1:8000 -t docs
   ```

   Open http://127.0.0.1:8000. The build stops with a clear message if data is missing or inconsistent.
4. Commit your source changes **together with the regenerated `docs/` directory**. GitHub Pages serves `docs/`
   directly, so a change only goes live once `docs/` is rebuilt. Never edit files in `docs/` by hand.
5. Create a pull request. The site is live as soon as it is merged into `master`.

## Pull requests

- **Create topic branches** – do not ask us to pull from your master branch.
- **One pull request per feature.**
- **Send coherent history** – squash intermediate commits before submitting.
