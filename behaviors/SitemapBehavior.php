<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014-2017 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap\behaviors;

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

    const BATCH_MAX_SIZE = 100;

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
        if (!is_callable($this->dataClosure) && !is_array($this->dataClosure)) {
            throw new InvalidConfigException('SitemapBehavior::$dataClosure isn\'t callable or array.');
        }
    }

    public function generateSiteMap()
    {
        $result = [];
        $n = 0;

        /** @var \yii\db\ActiveRecord $owner */
        $owner = $this->owner;
        $query = $owner::find();
        if (is_array($this->scope)) {
            if (is_callable($this->owner->{$this->scope[1]}())) {
                call_user_func($this->owner->{$this->scope[1]}(), $query);
            }
        } else {
            if (is_callable($this->scope)) {
                call_user_func($this->scope, $query);
            }
        }

        foreach ($query->each(self::BATCH_MAX_SIZE) as $model) {
            if (is_array($this->dataClosure)) {
                $urlData = call_user_func($this->owner->{$this->dataClosure[1]}(), $model);
            } else {
                $urlData = call_user_func($this->dataClosure, $model);
            }

            if (empty($urlData)) {
                continue;
            }

            $result[$n]['loc'] = $urlData['loc'];

            if (!empty($urlData['lastmod'])) {
                $result[$n]['lastmod'] = $urlData['lastmod'];
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

            if (isset($urlData['news'])) {
                $result[$n]['news'] = $urlData['news'];
            }
            if (isset($urlData['images'])) {
                $result[$n]['images'] = $urlData['images'];
            }
            
            if (isset($urlData['xhtml:link'])) {
                $result[$n]['xhtml:link'] = $urlData['xhtml:link'];
            }

            ++$n;
        }
        return $result;
    }
}
