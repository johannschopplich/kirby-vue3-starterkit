<?= '<?xml version="1.0" encoding="UTF-8"?>' ?>

<?= '<?xml-stylesheet type="text/xsl" href="sitemap.xsl"?>' ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">
<?php
foreach ($map as $id => $page):
?>
<url>
    <loc><?= $id ?></loc>
    <lastmod><?= $page['mod'] ?></lastmod>
<?php
    if(array_key_exists('lang', $page)):
        foreach ($page['lang'] as $l):
?>
    <xhtml:link rel="alternate" hreflang="<?= $l['locale'] ?>" href="<?= $l['url'] ?>" />
<?php
        endforeach;
    endif;
    if(array_key_exists('images', $page)):
        foreach ($page['images'] as $img): ?>
    <image:image>
        <image:loc><?= $img ?></image:loc>
    </image:image>
<?php
        endforeach;
    endif;
?>
</url>
<?php
endforeach;
?>
</urlset>
<!-- Sitemap generated using https://gitlab.com/cre8ivclick/sitemapper -->
