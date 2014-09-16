<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 */

namespace himiklab\sitemap;

use Yii;
use yii\base\Module;

/**
 * Yii2 module for automatically generation XML Sitemap.
 *
 * @author HimikLab
 * @package himiklab\sitemap
 */
class Sitemap extends Module
{
    public $controllerNamespace = 'himiklab\sitemap\controllers';

    /** @var int $cacheExpire */
    public $cacheExpire = 86400;

    /** @var string $cacheKey */
    public $cacheKey = 'sitemap';

    /** @var boolean $enableGzip */
    public $enableGzip = false;

    /** @var array $models */
    public $models = [];
}
