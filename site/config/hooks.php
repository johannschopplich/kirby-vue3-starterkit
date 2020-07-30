<?php

/**
 * Remove cached `home` page data after any changes to other pages or `site`
 * Applies only when Kirby pages cache is active
 */
return [
    'page.*:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'site.*:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    }
];
