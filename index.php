<?php

use Kirby\Cms\App;

App::plugin('smallsystems/vuek-seo', [
    'fields' => require __DIR__ . '/src/extensions/fields.php',
    'translations' => require __DIR__ . '/src/extensions/translations.php',
    'blueprints' => [
        'vuek-seo/site' => __DIR__ . '/src/blueprints/tab-site.yml',
        'vuek-seo/page' => __DIR__ . '/src/blueprints/tab-page.yml',
    ],
    'pageMethods' => [
        'getMetaTitle' => function () {
            if ($this->intendedTemplate()->name() === 'home') {
                return site()->title()->value() . ' – ' . $this->metaTitle()->or($this->title())->value();
            }
            return $this->metaTitle()->or($this->title() . ' – ' . site()->title())->value();
        },
        'getMetaDescription' => function () {
            return $this->metaDescription()->or($this->writer()->excerpt(140))->or(site()->metaDescription())->value();
        },
        'getMetaImage' => function () {
            return $this->metaImage()->or($this->top()->or(site()->metaImage()))->toFile()->url() ?? '';
        },
    ],
    'siteMethods' => [
        'getFavicon' => function () {
            return ['url' => site()->favicon()->toFile()->url() ?? '', 'type' => 'image/' . site()->favicon()->toFile()->extension() ?? ''];
        },
    ],
]);
