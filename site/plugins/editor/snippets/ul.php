<?php if ($prev === null || $prev->type() !== 'ul'): ?>
<ul>
<?php endif ?>
<li><?= $content ?></li>
<?php if ($next === null || $next->type() !== 'ul'): ?>
</ul>
<?php endif ?>
