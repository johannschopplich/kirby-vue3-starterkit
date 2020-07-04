# Kirby Resolve
Fast page routing even for large content structures or multi language sites.


## Commercial Usage
This plugin is free. Please consider to [make a donation](https://www.paypal.me/lukaskleinschmidt/5eur) if you use it in a commercial project.


## How and why does it work?
Lets imagine the following content structure.
```bash
.
├─ 📁 1_photography
│  ├─ 📁 1_sky
│  ├─ 📁 2_ocean
│  ├─ 📁 3_desert
│  ├─ 📁 4_mountains
│  ├─ 📁 5_waterfall
│  ├─ 📁 6_plants
│  └─ 📁 7_landscape
│     ├─ 📄 album.de.txt # Slug: landschaft
│     └─ 📄 album.en.txt
├─ 📁 2_about
├─ 📁 3_contact
├─ 📁 error
└─ 📁 home
```
To be able to resolve `photography/landscape` Kirby needs to index all subfolders in the `1_photography` directory to find the correct folder `📁 1_photography/7_landscape`. This is not a big deal for small sites but the time needed will inevitably increase over time as you add more subpages. This gets worse on multi language websites because, in addition to indexing directories, Kirby also has to check the slugs stored in the content files.

This plugin caches the request by mapping the requested path to the resolved page. By creating the page object from a directory we can skip all the previously needed indexing.
```json
{
    "photography/landscape": {
        "dir": "1_photography/7_landscape",
        "lang": "en",
    },
    "de/fotografie/landschaft": {
        "dir": "1_photography/7_landscape",
        "lang": "de",
    }
}
```
Of course the performance gain depends on your content structure and if you have multiple languages or not.

    ❕ This plugin only reduces the time needed to resolve the initial requested page

That said, it won't speed up anything you do in your templates. So I highly recommend using the default [pages cache](https://getkirby.com/docs/guide/cache#caching-pages) in addition to this plugin to get the best results!

Following hooks will flush the cache:
- `page.changeNum:before`
- `page.changeSlug:before`
- `page.changeStatus:before`

You can also disable the plugin in your config.
```php
<?php

return [
    'lukaskleinschmidt.resolve.cache' => false,
];
```


## Caveats
- It does not work for all root pages. Have a look at the [`isResolvable`](https://github.com/lukaskleinschmidt/kirby-resolve/blob/master/index.php#L67) function if you want to know the conditions.


## Installation
### Download

Download and copy this repository to `/site/plugins/resolve`.


### Git submodule
```
git submodule add https://github.com/lukaskleinschmidt/kirby-resolve.git site/plugins/resolve
```


### Composer
```
composer require lukaskleinschmidt/kirby-resolve
```


## License
MIT


## Credits
- [Lukas Kleinschmidt](https://github.com/lukaskleinschmidt)
