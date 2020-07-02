<?php
/**
 * Snippets are a great way to store code snippets for reuse or to keep your templates clean.
 * in loops or simply to keep your templates clean.
 * This gallery snippet is used in the gallery plugin (`/site/plugins/gallery`)
 * More about snippets: https://getkirby.com/docs/guide/templates/snippets
 */
?>

<section class="gallery">
  <?php foreach ($gallery->images() as $image): ?>
  <figure>
    <a href="<?= $image->link()->or($image->url()) ?>">
      <?= $image->crop(600, 800) ?>
    </a>
  </figure>
  <?php endforeach ?>
</section>
