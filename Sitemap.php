<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap;

use Yii;
use yii\base\Module;

/**
 * Yii2 module for automatically generating XML Sitemap.
 *
 * @author HimikLab
 * @package himiklab\sitemap
 */
class Sitemap extends Module
{
    public $controllerNamespace = 'himiklab\sitemap\controllers';

    /** @var int */
    public $cacheExpire = 86400;

    /** @var string */
    public $cacheKey = 'sitemap';

    /** @var boolean Use php's gzip compressing. */
    public $enableGzip = false;

    /** @var array */
    public $models = [];

    /** @var array */
    public $urls = [];
    
    /** @var integer */
    public $batchSize = 100;
    
    /** @var array */
    public $xmlns = [
        'xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"',
    ];

    /**
     * Build and cache a site map.
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    public function buildSitemap()
    {
        $urls = $this->urls;
        foreach ($this->models as $modelName) {
            /** @var behaviors\SitemapBehavior $model */
            if (is_array($modelName)) {
                $model = new $modelName['class'];
                if (isset($modelName['behaviors'])) {
                    $model->attachBehaviors($modelName['behaviors']);
                }
            } else {
                $model = new $modelName;
            }

            $urls = array_merge($urls, $model->generateSiteMap());
        }

        $sitemapData = $this->createControllerByID('default')->renderPartial('index', [
            'urls' => $urls,
            'xmlns' => $this->xmlns,
        ]);
        Yii::$app->cache->set($this->cacheKey, $sitemapData, $this->cacheExpire);

        return $sitemapData;
    }
}
