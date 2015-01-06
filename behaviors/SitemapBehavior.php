<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;

/**
 * Behavior for XML Sitemap Yii2 module.
 *
 * For example:
 *
 * ```php
 * public function behaviors()
 * {
 *  return [
 *       'sitemap' => [
 *           'class' => SitemapBehavior::className(),
 *           'scope' => function ($model) {
 *               $model->select(['url', 'lastmod']);
 *               $model->andWhere(['is_deleted' => 0]);
 *           },
 *           'dataClosure' => function ($model) {
 *              return [
 *                  'loc' => Url::to($model->url, true),
 *                  'lastmod' => strtotime($model->lastmod),
 *                  'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
 *                  'priority' => 0.8
 *              ];
 *          }
 *       ],
 *  ];
 * }
 * ```
 *
 * @see http://www.sitemaps.org/protocol.html
 * @author HimikLab
 * @package himiklab\sitemap
 */
class SitemapBehavior extends Behavior
{
    const CHANGEFREQ_ALWAYS = 'always';
    const CHANGEFREQ_HOURLY = 'hourly';
    const CHANGEFREQ_DAILY = 'daily';
    const CHANGEFREQ_WEEKLY = 'weekly';
    const CHANGEFREQ_MONTHLY = 'monthly';
    const CHANGEFREQ_YEARLY = 'yearly';
    const CHANGEFREQ_NEVER = 'never';

    /** @var callable */
    public $dataClosure;

    /** @var string|bool */
    public $defaultChangefreq = false;

    /** @var float|bool */
    public $defaultPriority = false;

    /** @var callable */
    public $scope;

    public function init()
    {
        if (!is_callable($this->dataClosure)) {
            throw new InvalidConfigException('SitemapBehavior::$dataClosure isn\'t callable.');
        }
    }

    public function generateSiteMap()
    {
        $result = [];
        
        /** @var \himiklab\sitemap\Sitemap $module */
        $module = Yii::$app->getModule('sitemap');

        /** @var \yii\db\ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner::find();
        if (is_callable($this->scope)) {
            call_user_func($this->scope, $query);
        }

        foreach ($query->each($module->batchSize) as $model) {
            $urlData = call_user_func($this->dataClosure, $model);

            if (!isset($urlData['changefreq']) && $this->defaultChangefreq !== false) {
                $urlData['changefreq'] = $this->defaultChangefreq;
            }

            if (!isset($urlData['priority']) && $this->defaultPriority !== false) {
                $urlData['priority'] = $this->defaultPriority;
            }

            $result[] = $urlData;
        }

        return $result;
    }
}
