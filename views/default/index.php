<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @var array $urls
 */

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
              xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
              xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    <?php foreach ($urls as $url): ?>
        <sitemap>
            <loc><?= yii\helpers\Url::to($url['loc'], true) ?></loc>
        </sitemap>
    <?php endforeach; ?>
</sitemapindex>
