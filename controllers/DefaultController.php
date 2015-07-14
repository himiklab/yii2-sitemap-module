<?php
/**
 * @link      https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap\controllers;

use Yii;
use yii\rest\UrlRule;
use yii\web\Controller;
use yii\web\Request;

/**
 * @author  HimikLab
 * @package himiklab\sitemap
 */
class DefaultController extends Controller
{
    public function getAdditionalParameters()
    {
        $request = new Request();
        $request->setUrl(Yii::$app->request->url);
        $additional_parameters = Yii::$app->urlManager->parseRequest($request);
        return $additional_parameters[1];
    }

    public function printXml($sitemapData)
    {
        /** @var \himiklab\sitemap\Sitemap $module */
        $module = $this->module;

        header('Content-type: application/xml');
        if ($module->enableGzip) {
            $sitemapData = gzencode($sitemapData);
            header('Content-Encoding: gzip');
            header('Content-Length: ' . strlen($sitemapData));
        }
        echo $sitemapData;
        exit();
    }

    public function actionIndex()
    {
        /** @var \himiklab\sitemap\Sitemap $module */
        $module = $this->module;
        $additionalParameters = $this->getAdditionalParameters();
        if (!$sitemapData = $module->cacheProvider->get($module->cacheKey.$this->id.serialize($additionalParameters))) {
            $sitemapData = $module->buildSitemap($additionalParameters);
        }

        $this->printXml($sitemapData);
    }

    public function actionMain()
    {
        /** @var \himiklab\sitemap\Sitemap $module */
        $module = $this->module;

        $additionalParameters = $this->getAdditionalParameters();
        if (!$sitemapData = $module->cacheProvider->get($module->cacheKey.$this->id.serialize($additionalParameters))) {
            $sitemapData = $module->buildSitemap($this->getAdditionalParameters(), 'main', 'sitemap');
        }
        $this->printXml($sitemapData);
    }
}
