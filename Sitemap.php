<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
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
    //About limit - https://support.google.com/webmasters/answer/75712
    const URLS_ON_PAGE = 1000;

    public $controllerNamespace = 'himiklab\sitemap\controllers';

    /** @var int */
    public $cacheExpire = 86400;

    /** @var Cache|string */
    public $cacheProvider = 'cache';

    /** @var string */
    public $cacheKey = 'sitemap';

    /** @var boolean Use php's gzip compressing. */
    public $enableGzip = false;

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
     */
    public function buildSitemap($name = '', $page = null)
    {
        /** @var behaviors\SitemapBehavior $model */
        if (is_array($this->models)) {
            $model = new $this->models['class'];
            if (isset($this->models['behaviors'])) {
                $model->attachBehaviors($this->models['behaviors']);
            }
        } else {
            $model = new $this->models;
        }

        $sitemapData = $this->generateSitemap($model, $name, $page);

        $this->cacheProvider->set($this->cacheKey . $name . $page ? : '', $sitemapData, $this->cacheExpire);

        return $sitemapData;
    }

    public function buildSitemapIndex()
    {
        $maps = [];
        if (!empty($this->urls)) {
            $maps[] = 'urls';
        }
        $maps += array_merge($maps, array_keys($this->models));

        foreach ($maps as $item) {
            $urls[]['loc'] = '/sitemap_' . $item . '.xml';
        }
        $sitemapData = $this->createControllerByID('default')->renderPartial('index', [
            'urls' => $urls
        ]);
        $this->cacheProvider->set($this->cacheKey, $sitemapData, $this->cacheExpire);

        return $sitemapData;
    }

    public function buildSitemapUrl($name)
    {
        $urls = $this->urls;

        $sitemapData = $this->createControllerByID('default')->renderPartial('sitemap', [
            'urls' => $urls
        ]);
        $this->cacheProvider->set($this->cacheKey, $sitemapData, $this->cacheExpire);

        return $sitemapData;
    }

    private function generateSitemap($model, $name = '', $page = null)
    {
        $countUrls = $model::find()->count();
        if ($countUrls > self::URLS_ON_PAGE) {
            if (is_numeric($page) && $page >= 0) {
                //Create a page of sitemaps

                $urls = $model->generateSiteMap(self::URLS_ON_PAGE, $page);
                $sitemapData = $this->createControllerByID('default')->renderPartial('sitemap', [
                    'urls' => $urls
                ]);
            } else {
                //Create common sitemap
                $pages = floor($countUrls / self::URLS_ON_PAGE);
                foreach (range(0, $pages) as $index) {
                    $urls[]['loc'] = '/sitemap_' . $name . '_' . $index . '.xml';
                }
                $sitemapData = $this->createControllerByID('default')->renderPartial('index', [
                    'urls' => $urls
                ]);
            }
        } else {
            $urls = $model->generateSiteMap();
            $sitemapData = $this->createControllerByID('default')->renderPartial('sitemap', [
                'urls' => $urls
            ]);
        }

        return $sitemapData;
    }
}
