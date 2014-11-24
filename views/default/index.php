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
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
    <?php foreach ($urls as $url): ?>
        <url>
            <loc><?= $url['loc'] ?></loc>
            <?php if (isset($url['lastmod'])): ?>
                <lastmod><?= $url['lastmod'] ?></lastmod>
            <?php endif; ?>
            <?php if (isset($url['changefreq'])): ?>
                <changefreq><?= $url['changefreq'] ?></changefreq>
            <?php endif; ?>
            <?php if (isset($url['priority'])): ?>
                <priority><?= $url['priority'] ?></priority>
            <?php endif; ?>
            <?php
            if (isset($url['news'])) {
                echo '<news:news>';
                if (isset($url['news']['publication'])) {
                    echo '<news:publication>';
                    echo(isset($url['news']['publication']['name']) ?
                        '<news:name>' . $url['news']['publication']['name'] . '</news:name>' : '');
                    echo(isset($url['news']['publication']['language']) ?
                        '<news:language>' . $url['news']['publication']['language'] . '</news:language>' : '');
                    echo '</news:publication>';
                }
                echo(isset($url['news']['access']) ? '<news:access>' . $url['news']['access'] . '</news:access>' : '');
                echo(isset($url['news']['genres']) ? '<news:genres>' . $url['news']['genres'] . '</news:genres>' : '');
                echo(isset($url['news']['publication_date']) ?
                    '<news:publication_date>' . $url['news']['publication_date'] . '</news:publication_date>' : '');
                echo(isset($url['news']['title']) ?
                    '<news:title>' . $url['news']['title'] . '</news:title>' : '');
                echo(isset($url['news']['keywords']) ?
                    '<news:keywords>' . $url['news']['keywords'] . '</news:keywords>' : '');
                echo(isset($url['news']['stock_tickers']) ?
                    '<news:stock_tickers>' . $url['news']['stock_tickers'] . '</news:stock_tickers>' : '');
                echo '</news:news>';
            }

            if (isset($url['images'])) {
                foreach ($url['images'] as $image) {
                    echo '<image:image>';
                    echo(isset($image['loc']) ? '<image:loc>' . $image['loc'] . '</image:loc>' : '');
                    echo(isset($image['caption']) ? '<image:caption>' . $image['caption'] . '</image:caption>' : '');
                    echo(isset($image['geo_location']) ?
                        '<image:geo_location>' . $image['geo_location'] . '</image:geo_location>' : '');
                    echo(isset($image['title']) ? '<image:title>' . $image['title'] . '</image:title>' : '');
                    echo(isset($image['license']) ? '<image:license>' . $image['license'] . '</image:license>' : '');
                    echo '</image:image>';
                }
            }
            ?>
        </url>
    <?php endforeach; ?>
</urlset>
