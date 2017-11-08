<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014-2017 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

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

        if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
            $sitemapData = $module->buildSitemap();
        }

        Yii::$app->response->format = Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/xml');
        if ($module->enableGzip) {
            if (!$module->enableGzipedCache) {
                $sitemapData = gzencode($sitemapData);
            }

            $headers->add('Content-Encoding', 'gzip');
            $headers->add('Content-Length', strlen($sitemapData));
        } elseif ($module->enableGzipedCache) {
            $sitemapData = gzdecode($sitemapData);
        }

        return $sitemapData;
    }
}
