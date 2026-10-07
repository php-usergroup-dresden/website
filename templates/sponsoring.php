<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var string $intro markdown
 * @var list<array<string, string>> $sponsors
 * @var list<array<string, string>> $partners
 */
?>
<div class="container page">
    <div class="prose"><?= $this->md($intro) ?></div>

    <section aria-labelledby="sponsors">
        <h2 id="sponsors">Unsere Sponsoren</h2>
        <ul class="sponsor-list">
            <?php foreach ($sponsors as $sponsor): ?>
                <li class="sponsor">
                    <a class="sponsor__logo" href="<?= $this->e($sponsor['url']) ?>">
                        <img src="<?= $this->e($sponsor['logo']) ?>" alt="<?= $this->e($sponsor['name']) ?>" loading="lazy">
                    </a>
                    <div>
                        <h3><?= $this->e($sponsor['claim'] ?? $sponsor['name']) ?></h3>
                        <?php if (($sponsor['description'] ?? '') !== ''): ?>
                            <div class="prose"><?= $this->md($sponsor['description']) ?></div>
                        <?php endif ?>
                    </div>
                </li>
            <?php endforeach ?>
        </ul>
    </section>

    <section aria-labelledby="partners">
        <h2 id="partners">Partner &amp; Community</h2>
        <?= $this->partial('logos', ['entries' => $partners]) ?>
    </section>
</div>
