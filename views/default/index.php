<?php
/**
 * @link https://github.com/himiklab/yii2-sitemap-module
 * @copyright Copyright (c) 2014-2017 HimikLab
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @var array $urls
 */

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9"
        xmlns:xhtml="http://www.w3.org/1999/xhtml">
    <?php foreach ($urls as $url): ?>
        <url>
            <loc><?= htmlspecialchars(yii\helpers\Url::to($url['loc'], true)) ?></loc>
            <?php if (isset($url['lastmod'])): ?>
                <lastmod><?= is_string($url['lastmod']) ?
                        htmlspecialchars($url['lastmod']) : date(DATE_W3C, $url['lastmod']) ?></lastmod>
            <?php endif; ?>
            <?php if (isset($url['changefreq'])): ?>
                <changefreq><?= $url['changefreq'] ?></changefreq>
            <?php endif; ?>
            <?php if (isset($url['priority'])): ?>
                <priority><?= $url['priority'] ?></priority>
            <?php endif; ?>
            <?php if (isset($url['xhtml:link'])): ?>
                <?php foreach ($url['xhtml:link'] as $link): ?>
                     <xhtml:link rel="alternate" hreflang="<?= $link['hreflang'] ?>" href="<?= $link['href'] ?>" />
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if (isset($url['news'])): ?>
                <news:news>
                    <news:publication>
                        <news:name><?= $url['news']['publication']['name'] ?></news:name>
                        <news:language><?= $url['news']['publication']['language'] ?></news:language>
                    </news:publication>
                    <?php
                    echo isset($url['news']['access']) ? ('<news:access>' .
                        htmlspecialchars($url['news']['access']) . '</news:access>') : '';
                    echo isset($url['news']['genres']) ? ('<news:genres>' .
                        htmlspecialchars($url['news']['genres']) . '</news:genres>') : '';
                    ?>
                    <news:publication_date>
                        <?= is_string($url['news']['publication_date']) ?
                            $url['news']['publication_date'] : date(DATE_W3C, $url['news']['publication_date']) ?>
                    </news:publication_date>
                    <news:title> <?= htmlspecialchars($url['news']['title']) ?></news:title>
                    <?php
                    echo isset($url['news']['keywords']) ?
                        "<news:keywords>{$url['news']['keywords']}</news:keywords>" : '';
                    echo isset($url['news']['stock_tickers']) ?
                        "<news:stock_tickers>{$url['news']['stock_tickers']}</news:stock_tickers>" : '';
                    ?>
                </news:news>
            <?php endif; ?>
            <?php if (isset($url['images'])):
                foreach ($url['images'] as $image): ?>
                    <image:image>
                        <image:loc><?= htmlspecialchars(yii\helpers\Url::to($image['loc'], true)) ?></image:loc>
                        <?php
                        echo isset($image['caption']) ?
                            ('<image:caption>' . htmlspecialchars($image['caption']) . '</image:caption>') : '';
                        echo isset($image['geo_location']) ?
                            ('<image:geo_location>' .
                                htmlspecialchars($image['geo_location']) . '</image:geo_location>') : '';
                        echo isset($image['title']) ?
                            ('<image:title>' . htmlspecialchars($image['title']) . '</image:title>') : '';
                        echo isset($image['license']) ?
                            ('<image:license>' . htmlspecialchars($image['license']) . '</image:license>') : '';
                        ?>
                    </image:image>
                <?php endforeach;
            endif; ?>
        </url>
    <?php endforeach; ?>
</urlset>
