<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var array $site
 * @var DateTimeImmutable $today
 * @var string $assetVersion
 * @var string $path
 * @var string $title
 * @var ?string $description
 * @var string $main
 */
$pageTitle = $title === '' ? $site['name'] : "$title · {$site['name']}";
$canonical = $site['baseUrl'] . ($path === '/index.html' ? '/' : $path);
$logo = $today->format('m') === '12' ? '/images/logo-xmas.png' : '/images/phpugdd-logo.svg';
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($pageTitle) ?></title>
    <meta name="description" content="<?= $this->e($description ?? $site['description']) ?>">
    <link rel="canonical" href="<?= $this->e($canonical) ?>">
    <meta property="og:site_name" content="<?= $this->e($site['name']) ?>">
    <meta property="og:title" content="<?= $this->e($pageTitle) ?>">
    <meta property="og:description" content="<?= $this->e($description ?? $site['description']) ?>">
    <meta property="og:url" content="<?= $this->e($canonical) ?>">
    <meta property="og:image" content="<?= $this->e($site['baseUrl']) ?>/images/logo_500x500.png">
    <meta name="theme-color" content="#1e2140">
    <link rel="preload" href="/fonts/recursive-latin.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/images/phpugdd-logo.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/images/favicons/apple-icon-180x180.png">
    <link rel="alternate" type="text/calendar" title="Event-Kalender" href="/events.ics">
    <link rel="stylesheet" href="/css/site.css?v=<?= $this->e($assetVersion) ?>">
</head>
<body>
<a class="skip-link" href="#main">Zum Inhalt springen</a>

<header class="site-header">
    <div class="container site-header__inner">
        <a class="brand" href="/">
            <img src="<?= $logo ?>" alt="" width="48" height="48">
            <span>PHP USERGROUP <strong>DRESDEN</strong></span>
        </a>
        <nav class="main-nav" aria-label="Hauptnavigation">
            <ul>
                <?php foreach ($site['nav'] as $item): ?>
                    <li><a href="<?= $this->e($item['path']) ?>"<?= $item['path'] === $path ? ' aria-current="page"' : '' ?>><?= $this->e($item['title']) ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>
        <a class="button button--primary" href="/become-member.html">Mitglied werden</a>
    </div>
</header>

<main id="main">
    <?= $main ?>
</main>

<footer class="site-footer">
    <div class="container site-footer__grid">
        <section aria-labelledby="footer-about">
            <h2 id="footer-about" class="site-footer__title"><?= $this->e($site['name']) ?></h2>
            <p><?= $this->e($site['tagline']) ?>. Eingetragen im Vereinsregister Dresden, <?= $this->e($site['register']) ?>.</p>
            <p><a href="mailto:<?= $this->e($site['email']) ?>"><?= $this->e($site['email']) ?></a></p>
        </section>
        <nav aria-labelledby="footer-community">
            <h2 id="footer-community" class="site-footer__title">Community</h2>
            <ul class="plain-list">
                <?php foreach ($site['social'] as $link): ?>
                    <li><a href="<?= $this->e($link['url']) ?>"<?= isset($link['rel']) ? ' rel="' . $this->e($link['rel']) . '"' : '' ?>><?= $this->e($link['title']) ?></a></li>
                <?php endforeach ?>
                <li><a href="/events.ics">Event-Kalender abonnieren</a></li>
            </ul>
        </nav>
        <nav aria-labelledby="footer-association">
            <h2 id="footer-association" class="site-footer__title">Verein</h2>
            <ul class="plain-list">
                <?php foreach ($site['footerNav'] as $item): ?>
                    <li><a href="<?= $this->e($item['path']) ?>"<?= $item['path'] === $path ? ' aria-current="page"' : '' ?>><?= $this->e($item['title']) ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>
        <section aria-labelledby="footer-newsletter">
            <h2 id="footer-newsletter" class="site-footer__title">Newsletter</h2>
            <form class="newsletter" action="<?= $this->e($site['newsletterAction']) ?>" method="post" target="_blank">
                <label for="newsletter-email">E-Mail-Adresse</label>
                <div class="newsletter__row">
                    <input type="email" name="EMAIL" id="newsletter-email" autocomplete="email" required aria-required="true">
                    <button type="submit" class="button button--primary">Abonnieren</button>
                </div>
                <p class="small">Der Versand erfolgt über Mailchimp. Mit dem Absenden gelten die <a href="/privacy.html">Datenschutzhinweise</a>.</p>
            </form>
        </section>
    </div>
    <div class="container site-footer__bottom small">
        © <?= $today->format('Y') ?> <?= $this->e($site['name']) ?>
    </div>
</footer>
</body>
</html>
