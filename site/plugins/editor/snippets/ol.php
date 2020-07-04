<?php if ($prev === null || $prev->type() !== 'ol'): ?>
<ol>
<?php endif ?>
<li><?= $content ?></li>
<?php if ($next === null || $next->type() !== 'ol'): ?>
</ol>
<?php endif ?>
