<?php

use Kirby\Toolkit\I18n;

return [
    'seo-preview' => [
        'props' => [
            'label' => fn($label = null) => I18n::translate($label, $label),
            'width' => fn($width = null) => $width,
            'faviconUrl' => fn($faviconUrl = null) => $faviconUrl,
            'siteTitle' => fn($siteTitle = null) => $siteTitle,
            'siteUrl' => fn($siteUrl = null) => $siteUrl,
            'titleSeparator' => fn($titleSeparator = '–') => $titleSeparator,
            'titleContentKey' => fn($titleContentKey = null) => is_string($titleContentKey) ? strtolower($titleContentKey) : $titleContentKey,
            'defaultTitle' => fn($defaultTitle = '') => $defaultTitle,
            'descriptionContentKey' => fn($descriptionContentKey = null) => is_string($descriptionContentKey) ? strtolower($descriptionContentKey) : $descriptionContentKey,
            'defaultDescription' => fn($defaultDescription = '') => $defaultDescription,
            'searchConsoleUrl' => fn($searchConsoleUrl = null) => $searchConsoleUrl,
            'imageContentKey' => fn($imageContentKey = null) => is_string($imageContentKey) ? strtolower($imageContentKey) : $imageContentKey,
            'overrideImage' => fn($overrideImage = '') => $overrideImage,
            'siteImage' => fn($siteImage = '') => $siteImage,
        ],
        'computed' => [
            'faviconUrl' => function () {
                return $this->tryResolveQuery($this->faviconUrl);
            },
            'siteTitle' => function () {
                /** @var \Kirby\Cms\App */
                $kirby = $this->kirby();

                return $this->tryResolveQuery($this->siteTitle, $kirby->site()->title()->value());
            },
            'siteUrl' => function () {
                /** @var \Kirby\Cms\App */
                $kirby = $this->kirby();
                return $this->tryResolveQuery($this->siteUrl, $kirby->url());
            },
            'titleSeparator' => function () {
                return $this->tryResolveQuery($this->titleSeparator);
            },
            'defaultTitle' => function () {
                return $this->tryResolveQuery($this->defaultTitle);
            },
            'defaultDescription' => function () {
                return $this->tryResolveQuery($this->defaultDescription);
            },
            'metaImage' => function () {
                if ($this->tryResolveQuery($this->overrideImage)) {
                    $file = $this->tryResolveQuery($this->overrideImage);
                    $scope = $this->model();
                } elseif ($this->model()->content()->get($this->imageContentKey)->value()) {
                    $file = $this->model()->content()->get($this->imageContentKey)->value();
                    $scope = $this->model();
                } elseif ($this->tryResolveQuery($this->siteImage)) {
                    $file = $this->tryResolveQuery($this->siteImage);
                    $scope = site();
                } else {
                    return false;
                }

                $fileObject = $scope->files()->findBy('uuid', str_replace('- ', '', $file));
                $image = $fileObject->thumb([
                    'width' => 1200,
                    'height' => 630,
                    'crop' => true,
                ]);

                if ($image) {
                    return $image->url();
                } else {
                    return false;
                }
            }
        ],
        'methods' => [
            'tryResolveQuery' => function ($value, $fallback = null) {
                if (is_string($value)) {
                    // Replace all matches of KQL parts with the query results
                    $value = preg_replace_callback('!\{\{(.+?)\}\}!', function ($matches) {
                        $result = $this->model()->query(trim($matches[1]));
                        return $result ?? '';
                    }, $value);
                }

                return $value ?? $fallback;
            }
        ]
    ]
];
