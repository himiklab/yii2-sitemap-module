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
        header('Content-type: application/xml');
        $cacheProvider = $module->cacheProvider;
        if (!$sitemapData = $cacheProvider->get($module->cacheKey)) {
            $sitemapData = $module->buildSitemap();
            if ($module->enableGzip) {
                $sitemapData = gzencode($sitemapData);
            }

            $cacheProvider->set($module->cacheKey, $sitemapData, $module->cacheExpire);
        }

        if ($module->enableGzip) {
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($sitemapData));
        }

        echo $sitemapData;
    }
}
