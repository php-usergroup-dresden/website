<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var list<array<string, string>> $entries entries with name, url and logo
 */
?>
<ul class="logo-grid">
    <?php foreach ($entries as $entry): ?>
        <li>
            <a href="<?= $this->e($entry['url']) ?>">
                <img src="<?= $this->e($entry['logo']) ?>" alt="<?= $this->e($entry['name']) ?>" loading="lazy">
            </a>
        </li>
    <?php endforeach ?>
</ul>
