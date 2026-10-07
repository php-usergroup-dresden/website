<?php
/**
 * Full event with program. Talk abstracts of past events are collapsed to keep long lists scannable.
 *
 * @var Phpugdd\Website\Renderer $this
 * @var Phpugdd\Website\Event $event
 * @var bool $upcoming
 * @var int $headingLevel
 */
$h = 'h' . ($headingLevel ?? 3);
$talkHeading = 'h' . (($headingLevel ?? 3) + 1);
?>
<article class="event<?= $upcoming ? ' event--upcoming' : '' ?>" id="<?= $this->e($event->date) ?>">
    <header class="event__header">
        <time class="event__date" datetime="<?= $this->e($event->date) ?>">
            <span class="event__day"><?= $event->start()->format('d') ?></span>
            <span class="event__month"><?= $event->start()->format('m/Y') ?></span>
        </time>
        <div>
            <<?= $h ?> class="event__title"><?= $this->e($event->title) ?></<?= $h ?>>
            <p class="event__meta">
                <span><?= $this->e($this->date($event->date)) ?><?= $event->time !== '' ? ', ' . $this->e($event->time) . ' Uhr' : '' ?></span>
                <?php if ($event->location !== ''): ?>
                    <span>
                        <?php if ($event->map !== ''): ?>
                            <a href="<?= $this->e($event->map) ?>"><?= $this->e($event->location) ?></a>
                        <?php else: ?>
                            <?= $this->e($event->location) ?>
                        <?php endif ?>
                    </span>
                <?php endif ?>
            </p>
        </div>
        <?php if ($upcoming && $event->url !== ''): ?>
            <a class="button button--primary event__cta" href="<?= $this->e($event->url) ?>">Platz sichern<span class="visually-hidden"> für <?= $this->e($event->title) ?></span></a>
        <?php endif ?>
    </header>

    <?php if ($event->image !== ''): ?>
        <img class="event__image" src="<?= $this->e($event->image) ?>" alt="" loading="lazy">
    <?php endif ?>

    <?php if ($event->description !== ''): ?>
        <div class="prose"><?= $this->md($event->description) ?></div>
    <?php endif ?>

    <?php if ($event->program() !== []): ?>
        <ol class="program" aria-label="Programm">
            <?php foreach ($event->program() as $entry): ?>
                <?php if ($entry instanceof Phpugdd\Website\Talk): ?>
                    <li class="program__item program__item--talk">
                        <span class="program__time"><?= $this->e($entry->time) ?></span>
                        <div>
                            <<?= $talkHeading ?> class="talk__title"><?= $this->e($entry->title) ?></<?= $talkHeading ?>>
                            <?= $this->partial('talk-meta', ['talk' => $entry]) ?>
                            <?php if ($entry->abstract !== ''): ?>
                                <?php if ($upcoming): ?>
                                    <div class="prose"><?= $this->md($entry->abstract) ?></div>
                                <?php else: ?>
                                    <details>
                                        <summary>Abstract<span class="visually-hidden"> zu „<?= $this->e($entry->title) ?>“</span></summary>
                                        <div class="prose"><?= $this->md($entry->abstract) ?></div>
                                    </details>
                                <?php endif ?>
                            <?php endif ?>
                            <?= $this->partial('links', ['links' => $entry->links]) ?>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="program__item">
                        <span class="program__time"><?= $this->e($entry['time']) ?></span>
                        <span><?= $this->e($entry['title']) ?></span>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
        </ol>
    <?php endif ?>

    <?= $this->partial('links', ['links' => $event->links]) ?>
</article>
