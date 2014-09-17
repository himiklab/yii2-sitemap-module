XML Sitemap Module for Yii2
========================
Yii2 module for automatically generation [XML Sitemap](http://www.sitemaps.org/protocol.html).

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require "himiklab/yii2-sitemap-module" "*"
```

or add

```json
"himiklab/yii2-sitemap-module" : "*"
```

to the require section of your application's `composer.json` file.

* Configure the `cache` component of your application's configuration file, for example:

```php
'components' => [
    ...
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    ...
]
```

* Add a new module in `modules` section of your application's configuration file.

```php
'modules' => [
    ...
    'sitemap' => [
        'class' => 'himiklab\sitemap\Sitemap',
        'models' => [
            'app\modules\page\models\Page',
            'app\modules\news\models\News',
            ...
        ],
        'urls'=> [
            [
                'loc'=>'/news/index',
                'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                'priority' => 0.8
            ],
        ],
        'enableGzip' => true,
    ],
    ...
],
```

* Add behavior in the AR models:

```php
use himiklab\sitemap\SitemapBehavior;

public function behaviors()
{
    return [
        'sitemap' => [
            'class' => SitemapBehavior::className(),
            'dataClosure' => function ($model) {
                /** @var self $model */
                return [
                    'loc' => Url::to(model->url, true),
                    'lastmod' => strtotime($model->lastmod),
                    'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8
                ];
            }
        ],
    ];
}
```

* Add a new rule for `urlManager` of your application's configuration file.

```php
'urlManager' => [
    'rules' => [
        '/<id:sitemap.xml>' => 'sitemap/default/index',
        ...
    ],
    ...
],
```
