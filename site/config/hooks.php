<?php

/**
 * Remove `home` page from cache after any changes to other pages or `site`
 */
return [
    'page.changeNum:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'page.changeSlug:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'page.changeStatus:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'page.changeTemplate:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'page.changeTitle:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'page.update:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'site.changeTitle:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    },
    'site.update:after' => function () {
        kirby()->cache('pages')->remove('home.html');
    }
];
