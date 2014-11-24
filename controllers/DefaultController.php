<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;

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

        if ($module->flush) {
            Yii::$app->cache->delete($module->cacheKey);
        }

        if (!$sitemapData = Yii::$app->cache->get($module->cacheKey)) {
            $urls = $this->toUrl($module->urls);

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

    /**
     * toUrl function.
     *
     * @param array $urls
     * @return array
     */
    protected function toUrl($urls)
    {
        $i = 0;
        foreach ($urls as $u) {
            $urls[$i]['loc'] = Url::to($u['loc'], true);
            $i++;
        }

        return $urls;
    }
}
