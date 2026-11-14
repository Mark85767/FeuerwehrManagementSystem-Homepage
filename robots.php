<?php
declare(strict_types=1);

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}
defined('FMS_HOMEPAGE_URL') || define('FMS_HOMEPAGE_URL', 'https://feuerwehrmanagementsystem.de');

header('Content-Type: text/plain; charset=utf-8');
header('X-Content-Type-Options: nosniff');
$basis = rtrim((string)FMS_HOMEPAGE_URL, '/');
echo "User-agent: *\nAllow: /\nDisallow: /config.local\n\nSitemap: {$basis}/sitemap.xml\n";
