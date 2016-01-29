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
            // your models
            'news' => 'app\modules\news\models\News',
            // or configuration for creating a behavior
            'news' => [
                'class' => 'app\modules\news\models\News',
                'behaviors' => [
					'sitemap' => [
						'class' => SitemapBehavior::className(),
						'scope' => function ($model) {
						    /** @var \yii\db\ActiveQuery $model */
						    $model->select(['url', 'lastmod']);
						    $model->andWhere(['is_deleted' => 0]);
						},
						'dataClosure' => function ($model) {
						    /** @var self $model */
						    return [
						        'loc' => Url::to($model->url, true),
						        'lastmod' => strtotime($model->lastmod),
						        'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
						        'priority' => 0.8
						    ];
						}
					],
                ],
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
                $model->select(['url', 'lastmod']);
                $model->andWhere(['is_deleted' => 0]);
            },
            'dataClosure' => function ($model) {
                /** @var self $model */
                return [
                    'loc' => Url::to($model->url, true),
                    'lastmod' => strtotime($model->lastmod),
                    'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.8
                ];
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
 sitemap.xml containes SitemapIndex with list of local sitemaps and url's sitemap:
```html
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
               xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
               xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    <sitemap>
        <loc>http://localhost/sitemap_urls.xml</loc>
    </sitemap>
    <sitemap>
        <loc>http://localhost/sitemap_news.xml</loc>
    </sitemap>
</sitemapindex>
```
 local sitemaps (for example sitemap_news.xml) contains addresses of news if number of articles is less than 1 000:
```html
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    <url>
        <loc>http://localhost/news/first</loc>
        <lastmod>2016-01-01T00:00:00+00:00</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>                
    </url>
    <url>
        <loc>http://localhost/news/second</loc>
        <lastmod>2016-01-11T00:00:00+00:00</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
        <news:news>
    </url>
    </urlset>
```

 If number of articles bigger local sitemap contains addresses of subsitemaps:
 ```html
 <?xml version="1.0" encoding="UTF-8"?>
 <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
               xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
               xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    <sitemap>
             <loc>http://localhost/sitemap_news_0.xml</loc>
    </sitemap>
    <sitemap>
             <loc>http://localhost/sitemap_news_1.xml</loc>
    </sitemap>
    <sitemap>
             <loc>http://localhost/sitemap_news_2.xml</loc>
    </sitemap>
 </sitemapindex>
 ```
 
Resources
---------
* [XML Sitemap](http://www.sitemaps.org/protocol.html)

* [News Sitemap](https://support.google.com/news/publisher/answer/74288?hl=en)

* [Image sitemaps](https://support.google.com/webmasters/answer/178636?hl=en)
