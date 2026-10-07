<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var array $site
 * @var list<Phpugdd\Website\Event> $events events with at least one talk, newest first
 */
$byYear = [];
foreach ($events as $event) {
    $byYear[$event->year()][] = $event;
}
$talkCount = array_sum(array_map(static fn ($event): int => count($event->talks), $events));
?>
<div class="container page">
    <h1>Talks</h1>
    <p class="lead">
        <?= $talkCount ?> Vorträge seit <?= $this->e((string) array_key_last($byYear)) ?>. Du möchtest selbst sprechen?
        <a href="<?= $this->e($site['cfpUrl']) ?>">Reiche deinen Talk ein</a> – Lightning Talks sind ausdrücklich willkommen.
    </p>

    <?php foreach ($byYear as $year => $yearEvents): ?>
        <section aria-labelledby="talks-<?= $year ?>">
            <h2 id="talks-<?= $year ?>"><?= $year ?></h2>
            <ul class="talk-list">
                <?php foreach ($yearEvents as $event): ?>
                    <?php foreach ($event->talks as $talk): ?>
                        <li class="talk-list__item">
                            <h3 class="talk__title"><?= $this->e($talk->title) ?></h3>
                            <?= $this->partial('talk-meta', ['talk' => $talk]) ?>
                            <p class="small muted">
                                <a href="/events.html#<?= $this->e($event->date) ?>"><?= $this->e($event->title) ?></a>,
                                <time datetime="<?= $this->e($event->date) ?>"><?= $this->e($this->date($event->date, false)) ?></time>
                            </p>
                            <?= $this->partial('links', ['links' => $talk->links]) ?>
                        </li>
                    <?php endforeach ?>
                <?php endforeach ?>
            </ul>
        </section>
    <?php endforeach ?>
</div>
