<?php
declare(strict_types=1);

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}
defined('FMS_HOMEPAGE_URL') || define('FMS_HOMEPAGE_URL', 'https://feuerwehrmanagementsystem.de');

header('Content-Type: application/xml; charset=utf-8');
header('X-Content-Type-Options: nosniff');
$basis = rtrim((string)FMS_HOMEPAGE_URL, '/');
$lastmod = gmdate('Y-m-d', (int)filemtime(__DIR__ . '/index.php'));
// Rechtsseiten sind bewusst noindex und gehören nicht in die Sitemap.
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
echo '  <url><loc>' . htmlspecialchars($basis, ENT_XML1 | ENT_QUOTES, 'UTF-8') . '/</loc><lastmod>' . $lastmod . "</lastmod><changefreq>monthly</changefreq><priority>1.0</priority></url>\n";
echo "</urlset>\n";
