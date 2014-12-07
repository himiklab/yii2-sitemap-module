<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 */

namespace himiklab\sitemap;

use Yii;
use yii\base\Module;

/**
 * Yii2 module for automatically generating XML Sitemap.
 *
 * @author HimikLab
 * @package himiklab\sitemap
 */
class Sitemap extends Module
{
    public $controllerNamespace = 'himiklab\sitemap\controllers';

    /** @var int */
    public $cacheExpire = 86400;

    /** @var string */
    public $cacheKey = 'sitemap';

    /** @var boolean */
    public $enableGzip = false;

    /** @var array */
    public $models = [];

    /** @var array */
    public $urls = [];
}
