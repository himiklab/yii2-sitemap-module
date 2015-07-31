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

        if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
            $sitemapData = $module->buildSitemap();
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/xml');
        if ($module->enableGzip) {
            $sitemapData = gzencode($sitemapData);
            $headers->add('Content-Encoding', 'gzip');
            $headers->add('Content-Length', strlen($sitemapData));
        }
        return $sitemapData;
    }
}
