<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\sitemap\controllers;

use Yii;
use yii\web\Controller;

/**
 * @author HimikLab
 * @package himiklab\sitemap
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        /** @var \himiklab\sitemap\Sitemap $module */
        $module = $this->module;

        if (!$sitemapData = Yii::$app->cache->get($module->cacheKey)) {
            $urls = [];
            foreach ($module->models as $modelName) {
                /** @var \himiklab\sitemap\behaviors\SitemapBehavior $model */
                $model = new $modelName;
                $urls = array_merge($urls, $model->generateSiteMap());
            }

            $sitemapData = $this->renderPartial('index', [
                'urls' => $urls
            ]);
            Yii::$app->cache->set($module->cacheKey, $sitemapData, $module->cacheExpire);
        }

        header('Content-type: application/xml');
        if ($module->enableGzip) {
            $sitemapData = gzencode($sitemapData);
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($sitemapData));
        }
        echo $sitemapData;
    }
}
