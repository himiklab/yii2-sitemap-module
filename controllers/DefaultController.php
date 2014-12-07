<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
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
            $sitemapData = $module->buildSitemap();
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
