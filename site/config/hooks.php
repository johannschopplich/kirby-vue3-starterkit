<?php

$flush = function () {
    kirby()->cache('templates')->flush();
};

return [
    'page.*:after' => $flush,
    'site.*:after' => $flush
];
