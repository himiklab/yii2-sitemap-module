XML Sitemap Module for Yii2
==========================
Yii2 module for automatically generating [XML Sitemap](http://www.sitemaps.org/protocol.html).

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

* Either run

```
php composer.phar require --prefer-dist "himiklab/yii2-sitemap-module" "*"
```

or add

```json
"himiklab/yii2-sitemap-module" : "*"
```

to the `require` section of your application's `composer.json` file.

* Configure the `cache` component of your application's configuration file, for example:

```php
'components' => [
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
]
```

* Add a new module in `modules` section of your application's configuration file, for example:

```php
'modules' => [
        'sitemap' => [
            'class' => 'himiklab\sitemap\Sitemap',
            'models' => [
                'article' => [
                    'class' => 'common\models\Article',
                ],
                'page' => ['']
            ],
        ],
        'urls'=> [
            // your additional urls
            [
                'loc' => '/news/index',
                'changefreq' => \himiklab\sitemap\behaviors\SitemapBehavior::CHANGEFREQ_DAILY,
                'priority' => 0.8,
                'news' => [
                    'publication'   => [
                        'name'          => 'Example Blog',
                        'language'      => 'en',
                    ],
                    'access'            => 'Subscription',
                    'genres'            => 'Blog, UserGenerated',
                    'publication_date'  => 'YYYY-MM-DDThh:mm:ssTZD',
                    'title'             => 'Example Title',
                    'keywords'          => 'example, keywords, comma-separated',
                    'stock_tickers'     => 'NASDAQ:A, NASDAQ:B',
                ],
                'images' => [
                    [
                        'loc'           => 'http://example.com/image.jpg',
                        'caption'       => 'This is an example of a caption of an image',
                        'geo_location'  => 'City, State',
                        'title'         => 'Example image',
                        'license'       => 'http://example.com/license',
                    ],
                ],
            ],
        ],
        'enableGzip' => true, // default is false
        'cacheExpire' => 1, // 1 second. Default is 24 hours
    ],
],
```

* Add behavior in the AR models, for example:

```php
use himiklab\sitemap\behaviors\SitemapBehavior;

public function behaviors()
{
    return [
        'sitemap' => [
            'class' => SitemapBehavior::className(),
            'scope' => function ($model) {
                 /** @var \yii\db\ActiveQuery $model */
                 $model->select(['slug', 'updated_at', 'private', 'title', 'published_at']);
                 $model->andWhere(['status' => \frontend\modules\api\v1\resources\Article::STATUS_PUBLISHED]);
            },
            'dataClosure' => function ($model) {
                /** @var self $model */
                $result = [
                   'news' => [
                       'publication' => [
                           'name' => 'Name',
                           'language' => Yii::$app->language,
                   ],
                   'publication_date' => date('c', $model->published_at),
                   'title' => $model->title,
                ],
                'loc' => Url::to('article/' . $model->slug, true),
                'lastmod' => strtotime($model->updated_at),
                'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                'priority' => 0.8
                ];
                if ($model->private != \frontend\modules\api\v1\resources\Article::PRIVATE_OFF) {
                    $result['news']['access'] = 'Registration';
                }
                return $result;
            }
        ],
    ];
}
```

* Add a new rules for `urlManager` of your application's configuration file, for example:

```php
'urlManager' => [
    'rules' => [
        ['pattern' => 'sitemap', 'route' => 'sitemap/default/sitemap-index', 'suffix' => '.xml'],
        [
            'pattern' => 'sitemap_<name:.+?><delimetr:_+><page:(\d+)>',
            'route' => 'sitemap/default/sitemap',
            'defaults' => [
                'delimetr' => null,
                'page' => null
            ],
            'suffix' => '.xml',
        ]
    ],
],
``` 
* Sitemap creates by following scheme:
 sitemap.xml containes SitemapIndex with list of local sitemaps and url's sitemap
 local sitemaps (for example sitemap_article.xml) contains addresses of articles if number of articles is less
 than 1 000. If number of articles bigger local sitemap contains addresses of subsitemaps.
 
Resources
---------
* [XML Sitemap](http://www.sitemaps.org/protocol.html)

* [News Sitemap](https://support.google.com/news/publisher/answer/74288?hl=en)

* [Image sitemaps](https://support.google.com/webmasters/answer/178636?hl=en)
