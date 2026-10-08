# The PHP USERGROUP DRESDEN e.V. website

This is the website of the PHP USERGROUP DRESDEN e.V.: **[https://phpug-dresden.org](https://phpug-dresden.org)**

## Found a bug or a typo?

This website is open source, so please feel free to edit and send us a pull request.

All you need is PHP 8.2 or newer – there are no other dependencies.

```bash
php build.php && php -S 127.0.0.1:8000 -t docs
```

- Events, talks, sponsors and the team are maintained in [`data/`](data/README.md) as JSON.
- Text pages live in `content/` as Markdown.
- Images and downloads live in `static/`.

GitHub Pages serves the generated `docs/` directory from `master`, so commit it together with your changes.
See the **[contribution guide](.github/CONTRIBUTING.md)**.

## You want to speak at our user group?

We're always looking for (lightning) talks for our meetups.

**[Please propose your talk idea here.](https://github.com/php-usergroup-dresden/talks)**
