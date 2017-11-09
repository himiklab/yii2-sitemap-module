<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014-2017 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Module;
use yii\caching\Cache;

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

    /** @var Cache|string */
    public $cacheProvider = 'cache';

    /** @var string */
    public $cacheKey = 'sitemap';

    /** @var boolean Use php's gzip compressing. */
    public $enableGzip = false;

    /** @var boolean */
    public $enableGzipedCache = false;

    /** @var array */
    public $models = [];

    /** @var array */
    public $urls = [];

    public function init()
    {
        parent::init();

        if (is_string($this->cacheProvider)) {
            $this->cacheProvider = Yii::$app->{$this->cacheProvider};
        }

        if (!$this->cacheProvider instanceof Cache) {
            throw new InvalidConfigException('Invalid `cacheKey` parameter was specified.');
        }
    }

    /**
     * Build and cache a site map.
     * @return string
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\base\InvalidParamException
     */
    public function buildSitemap()
    {
        $urls = $this->urls;
        foreach ($this->models as $modelName) {
            /** @var behaviors\SitemapBehavior|\yii\db\ActiveRecord $model */
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

        $sitemapData = $this->createControllerByID('default')->renderPartial('index', ['urls' => $urls]);
        if ($this->enableGzipedCache) {
            $sitemapData = gzencode($sitemapData);
        }
        $this->cacheProvider->set($this->cacheKey, $sitemapData, $this->cacheExpire);

        return $sitemapData;
    }

    public function clearCache()
    {
        $this->cacheProvider->delete($this->cacheKey);
    }
}
