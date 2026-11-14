<?php
// Als config.local.php kopieren und den geheimen Schlüssel außerhalb des Repositories setzen.
define('FMS_ZUGANGSANFRAGE_API_URL', 'https://app.feuerwehrmanagementsystem.de/zugangsanfrage_api.php');
define('FMS_ZUGANGSANFRAGE_SCHLUESSEL', '');

// Angaben für Impressum (impressum.php) und Datenschutzerklärung (datenschutz.php)
// Optional: define('FMS_BETREIBER_TELEFON', '+49 ...');
define('FMS_BETREIBER_NAME', 'Vor- und Nachname bzw. Name des Vereins');
define('FMS_BETREIBER_ANSCHRIFT', 'Musterstraße 1, 12345 Musterstadt');
define('FMS_BETREIBER_EMAIL', 'datenschutz@example.de');
define('FMS_HOSTER', 'Name des Hosting-Anbieters');

// Öffentliche Basis-URL der Homepage (ohne Slash am Ende) für Canonical, Sitemap und robots.txt
// define('FMS_HOMEPAGE_URL', 'https://feuerwehrmanagementsystem.de');
