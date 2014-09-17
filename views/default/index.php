<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014 HimikLab
 * @license http://opensource.org/licenses/MIT
 *
 * @var array $urls
 */

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($urls as $url): ?>
        <url>
            <loc><?= $url['loc'] ?></loc>
            <?php if(isset($url['lastmod'])): ?>
                <lastmod><?= $url['lastmod'] ?></lastmod>
            <?php endif; ?>
            <changefreq><?= $url['changefreq'] ?></changefreq>
            <priority><?= $url['priority'] ?></priority>
        </url>
    <?php endforeach; ?>
</urlset>
