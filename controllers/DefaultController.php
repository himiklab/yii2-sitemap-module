<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace katech91\sitemap\controllers;

use Yii;
use yii\web\Controller;

/**
 * @author HimikLab
 * @package katech91\sitemap
 */
class DefaultController extends Controller
{
    public function actionSitemapIndex()
    {
        /** @var \katech91\sitemap\Sitemap $module */
        $module = $this->module;

        if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
            $sitemapData = $module->buildSitemapIndex();
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

    public function actionSitemap($name, $page = null)
    {
        /** @var \katech91\sitemap\Sitemap $module */
        $module = $this->module;
        if (isset($module->models[$name])){
            $module->models = $module->models[$name];
            if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
                $sitemapData = $module->buildSitemap($name, $page);
            }
        } elseif ($name === 'urls'){
            if (!$sitemapData = $module->cacheProvider->get($module->cacheKey)) {
                $sitemapData = $module->buildSitemapUrl($page);
            }
        } else {
            throw new \yii\web\NotFoundHttpException();
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
