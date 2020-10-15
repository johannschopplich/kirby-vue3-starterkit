<section class="gallery">
  <?php foreach ($gallery->images() as $image): ?>
  <figure>
    <a href="<?= $image->link()->or($image->url()) ?>">
      <?= $image->crop(600, 800) ?>
    </a>
  </figure>
  <?php endforeach ?>
</section>
