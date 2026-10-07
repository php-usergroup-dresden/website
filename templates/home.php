<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var array $site
 * @var list<Phpugdd\Website\Event> $upcoming
 * @var list<Phpugdd\Website\Event> $recent
 * @var list<array<string, string>> $team
 * @var list<array<string, string>> $sponsors
 * @var list<array<string, string>> $partners
 */
$next = $upcoming[0] ?? null;
?>
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__text">
            <p class="eyebrow">PHP USERGROUP DRESDEN e.V.</p>
            <h1><?= $this->e($site['tagline']) ?></h1>
            <p class="lead">
                Wir sind eine Community von PHP-Enthusiasten aus Dresden. Ob erfahrene Entwickler:innen, Studierende
                oder Freiberufler:innen – alle sind willkommen. Wir treffen uns regelmäßig zu Meetups und Stammtischen,
                teilen Wissen und besuchen gemeinsam Konferenzen.
            </p>
            <div class="button-row">
                <a class="button button--primary" href="/events.html">Alle Events</a>
                <a class="button" href="<?= $this->e($site['cfpUrl']) ?>">Talk einreichen</a>
                <a class="button" href="/events.ics">Kalender abonnieren</a>
            </div>
        </div>

        <aside class="hero__next" aria-labelledby="next-event">
            <h2 id="next-event" class="eyebrow">Nächstes Event</h2>
            <?php if ($next !== null): ?>
                <p class="hero__date"><?= $this->e($this->date($next->date)) ?><?= $next->time !== '' ? ', ' . $this->e($next->time) . ' Uhr' : '' ?></p>
                <p class="hero__title"><a href="/events.html#<?= $this->e($next->date) ?>"><?= $this->e($next->title) ?></a></p>
                <?php if ($next->location !== ''): ?>
                    <p class="muted"><?= $this->e($next->location) ?></p>
                <?php endif ?>
                <?php if ($next->talks !== []): ?>
                    <ul class="plain-list hero__talks">
                        <?php foreach ($next->talks as $talk): ?>
                            <li><?= $this->e($talk->title) ?><?php if ($talk->speaker !== ''): ?> <span class="muted">– <?= $this->e($talk->speaker) ?></span><?php endif ?></li>
                        <?php endforeach ?>
                    </ul>
                <?php endif ?>
                <?php if ($next->url !== ''): ?>
                    <a class="button button--primary" href="<?= $this->e($next->url) ?>">Platz sichern</a>
                <?php endif ?>
            <?php else: ?>
                <p>Der nächste Termin ist in Planung. Neue Events veröffentlichen wir zuerst auf
                    <a href="<?= $this->e($site['meetupUrl']) ?>">Meetup</a>.</p>
            <?php endif ?>
        </aside>
    </div>
</section>

<?php if (count($upcoming) > 1): ?>
    <section class="section">
        <div class="container">
            <h2>Weitere Termine</h2>
            <?php foreach (array_slice($upcoming, 1) as $event): ?>
                <?= $this->partial('event', ['event' => $event, 'upcoming' => true]) ?>
            <?php endforeach ?>
        </div>
    </section>
<?php endif ?>

<section class="section section--alt">
    <div class="container">
        <div class="section__head">
            <h2>Zuletzt bei uns</h2>
            <a href="/events.html#past">Alle vergangenen Events</a>
        </div>
        <ul class="card-grid">
            <?php foreach ($recent as $event): ?>
                <li class="card">
                    <p class="muted small"><time datetime="<?= $this->e($event->date) ?>"><?= $this->e($this->date($event->date, false)) ?></time></p>
                    <h3 class="card__title"><a href="/events.html#<?= $this->e($event->date) ?>"><?= $this->e($event->title) ?></a></h3>
                    <?php if ($event->talks !== []): ?>
                        <ul class="plain-list small">
                            <?php foreach ($event->talks as $talk): ?>
                                <li><?= $this->e($talk->title) ?><?php if ($talk->speaker !== ''): ?> <span class="muted">– <?= $this->e($talk->speaker) ?></span><?php endif ?></li>
                            <?php endforeach ?>
                        </ul>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
        </ul>
    </div>
</section>

<section class="section" id="team">
    <div class="container">
        <h2>Das Orga-Team</h2>
        <ul class="team-grid">
            <?php foreach ($team as $member): ?>
                <li class="member">
                    <img src="<?= $this->e($member['image']) ?>" alt="" loading="lazy" width="160" height="160">
                    <p class="member__name"><?= $this->e($member['name']) ?></p>
                    <?php if (($member['role'] ?? '') !== ''): ?>
                        <p class="member__role"><?= $this->e($member['role']) ?></p>
                    <?php endif ?>
                </li>
            <?php endforeach ?>
            <li class="member member--you">
                <span class="member__placeholder" aria-hidden="true">Du?</span>
                <p class="member__name"><a href="/become-member.html">Mach mit!</a></p>
                <p class="member__role">Werde Teil des Vereins</p>
            </li>
        </ul>
    </div>
</section>

<section class="section section--alt" id="sponsors">
    <div class="container">
        <div class="section__head">
            <h2>Sponsoren</h2>
            <a href="/sponsoring.html">Sponsor werden</a>
        </div>
        <?= $this->partial('logos', ['entries' => $sponsors]) ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2>Partner &amp; Community</h2>
        <?= $this->partial('logos', ['entries' => $partners]) ?>
    </div>
</section>
