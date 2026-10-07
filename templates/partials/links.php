<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var list<array{title: string, url: string}> $links
 */
if ($links === []) {
    return;
}
?>
<ul class="link-list">
    <?php foreach ($links as $link): ?>
        <li><a href="<?= $this->e($link['url']) ?>"><?= $this->e($link['title']) ?></a></li>
    <?php endforeach ?>
</ul>
