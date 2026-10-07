<?php
/**
 * @var Phpugdd\Website\Renderer $this
 * @var Phpugdd\Website\Talk $talk
 */
?>
<p class="talk__meta">
    <?php if ($talk->speakerUrl !== ''): ?>
        <a class="talk__speaker" href="<?= $this->e($talk->speakerUrl) ?>"><?= $this->e($talk->speaker) ?></a>
    <?php elseif ($talk->speaker !== ''): ?>
        <span class="talk__speaker"><?= $this->e($talk->speaker) ?></span>
    <?php endif ?>
    <?php if ($talk->format !== ''): ?><span class="tag"><?= $this->e($talk->format) ?></span><?php endif ?>
    <?php if ($talk->language !== ''): ?><span class="tag tag--quiet"><?= $this->e($talk->language) ?></span><?php endif ?>
</p>
