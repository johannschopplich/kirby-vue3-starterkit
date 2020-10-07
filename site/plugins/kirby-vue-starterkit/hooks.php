<?php

$flush = function () {
    kirby()->cache('johannschopplich.kirby-vue-starterkit')->flush();
};

return [
    'page.*:after' => $flush,
    'site.*:after' => $flush
];
