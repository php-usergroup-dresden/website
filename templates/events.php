<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var array $site
 * @var list<Phpugdd\Website\Event> $upcoming
 * @var list<Phpugdd\Website\Event> $past
 */
$pastByYear = [];
foreach ($past as $event) {
    $pastByYear[$event->year()][] = $event;
}
?>
<div class="container page">
    <h1>Events</h1>
    <p class="lead">
        Wir versuchen, alle sechs Wochen ein Meetup auf die Beine zu stellen. Neue Termine veröffentlichen wir auf
        <a href="<?= $this->e($site['meetupUrl']) ?>">Meetup</a> – oder du abonnierst unseren
        <a href="/events.ics">Event-Kalender</a>.
    </p>

    <section aria-labelledby="upcoming">
        <h2 id="upcoming">Demnächst</h2>
        <?php if ($upcoming === []): ?>
            <p>Der nächste Termin ist in Planung.</p>
        <?php endif ?>
        <?php foreach ($upcoming as $event): ?>
            <?= $this->partial('event', ['event' => $event, 'upcoming' => true]) ?>
        <?php endforeach ?>
    </section>

    <section aria-labelledby="past">
        <h2 id="past">Vergangene Events</h2>
        <nav class="year-nav" aria-label="Jahre">
            <ul>
                <?php foreach (array_keys($pastByYear) as $year): ?>
                    <li><a href="#jahr-<?= $year ?>"><?= $year ?></a></li>
                <?php endforeach ?>
            </ul>
        </nav>
        <?php foreach ($pastByYear as $year => $events): ?>
            <h3 class="year-heading" id="jahr-<?= $year ?>"><?= $year ?></h3>
            <?php foreach ($events as $event): ?>
                <?= $this->partial('event', ['event' => $event, 'upcoming' => false, 'headingLevel' => 4]) ?>
            <?php endforeach ?>
        <?php endforeach ?>
    </section>
</div>
