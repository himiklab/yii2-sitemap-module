<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\sitemap\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\InvalidParamException;

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
 *           'dataClosure' => function ($model) {
 *              return [
 *                  'loc' => Url::to(model->url, true),
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

    /** @var \Closure $dataClosure */
    public $dataClosure;

    /** @var string|bool $defaultChangefreq */
    public $defaultChangefreq = false;

    /** @var float|bool $defaultPriority */
    public $defaultPriority = false;

    public function init()
    {
        if (!$this->dataClosure instanceof \Closure) {
            throw new InvalidParamException('SitemapBehavior::$dataClosure isn`t \Closure object.');
        }
    }

    public function generateSiteMap()
    {
        $result = [];
        $n = 0;

        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        $models = $owner::find()->all();

        foreach ($models as $model) {
            $urlData = call_user_func($this->dataClosure, $model);

            if (empty($urlData)) {
                continue;
            }

            if (!isset($urlData['loc'])) {
                throw new InvalidParamException('Required param `loc` isn`t set.');
            }

            $result[$n]['loc'] = $urlData['loc'];
            if (isset($urlData['lastmod'])) {
                $result[$n]['lastmod'] = date(DATE_W3C, $urlData['lastmod']);
            }

            if (isset($urlData['changefreq'])) {
                $result[$n]['changefreq'] = $urlData['changefreq'];
            } elseif ($this->defaultChangefreq !== false) {
                $result[$n]['changefreq'] = $this->defaultChangefreq;
            }

            if (isset($urlData['priority'])) {
                $result[$n]['priority'] = $urlData['priority'];
            } elseif ($this->defaultPriority !== false) {
                $result[$n]['priority'] = $this->defaultPriority;
            }

            ++$n;
        }
        return $result;
    }
}
