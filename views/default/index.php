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
  xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" >
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
                if (isset($url['images'])) {
                    foreach($url['images'] as $image){
                        echo '<image:image>';
                            echo (isset($image['loc']) ? '<image:loc>'.$image['loc'].'</image:loc>' : '');
                            echo (isset($image['caption']) ? '<image:caption>'.$image['loc'].'</image:caption>' : '');
                            echo (isset($image['geo_location']) ? '<image:geo_location>'.$image['geo_location'].'</image:geo_location>' : '');
                            echo (isset($image['title']) ? '<image:title>'.$image['title'].'</image:title>' : '');
                            echo (isset($image['license']) ? '<image:license>'.$image['license'].'</image:license>' : '');
                        echo '</image:image>';
                    }
                }
            ?>
        </url>
    <?php endforeach; ?>
</urlset>
