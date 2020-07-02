<?php if ($block->isNotEmpty()): ?>
<figure<?= attr(['class' => $attrs->css()->value()], ' ') ?>>
  <?php if ($attrs->link()->isNotEmpty()): ?>
  <a href="<?= $attrs->link()->toUrl() ?>">
    <img src="<?= $src ?>" alt="<?= $attrs->alt() ?>">
  </a>
  <?php else: ?>
  <img src="<?= $src ?>" alt="<?= $attrs->alt() ?>">
  <?php endif ?>

  <?php if ($attrs->caption()->isNotEmpty()): ?>
  <figcaption>
    <?= $attrs->caption() ?>
  </figcaption>
  <?php endif ?>
</figure>
<?php endif ?>
