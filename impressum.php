<?php
declare(strict_types=1);

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Angaben zum Betreiber – in config.local.php überschreiben.
defined('FMS_BETREIBER_NAME') || define('FMS_BETREIBER_NAME', '[Name des Betreibers bitte ergänzen]');
defined('FMS_BETREIBER_ANSCHRIFT') || define('FMS_BETREIBER_ANSCHRIFT', '[Straße Hausnummer, PLZ Ort]');
defined('FMS_BETREIBER_EMAIL') || define('FMS_BETREIBER_EMAIL', '[E-Mail-Adresse bitte ergänzen]');
defined('FMS_BETREIBER_TELEFON') || define('FMS_BETREIBER_TELEFON', '');
defined('FMS_ANMELDE_URL') || define('FMS_ANMELDE_URL', 'https://app.feuerwehrmanagementsystem.de/login.php');
defined('FMS_MITGLIEDERPOTAL_URL') || define('FMS_MITGLIEDERPOTAL_URL', 'https://portal.feuerwehrmanagementsystem.de/');
defined('FMS_HOMEPAGE_URL') || define('FMS_HOMEPAGE_URL', 'https://feuerwehrmanagementsystem.de');

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; font-src https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");

function h(string $wert): string
{
    return htmlspecialchars($wert, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, follow">
    <link rel="canonical" href="<?= h(rtrim((string)FMS_HOMEPAGE_URL, '/')) ?>/impressum">
    <title>Impressum – FeuerwehrManagementSystem</title>
    <link rel="icon" href="assets/fms-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous" rel="stylesheet">
    <link href="assets/homepage.css" rel="stylesheet">
    <script src="assets/theme.js"></script>
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-lg container" aria-label="Hauptnavigation">
        <a class="navbar-brand d-flex align-items-center gap-2" href="index.php#start">
            <img src="assets/fms-logo.png" alt="" width="42" height="42">
            <span>FeuerwehrManagementSystem</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hauptmenue" aria-controls="hauptmenue" aria-expanded="false" aria-label="Menü öffnen">
            <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="hauptmenue">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <a class="nav-link" href="index.php#funktionen">Funktionen</a>
                <a class="btn btn-fw ms-lg-2" href="index.php#zugang">Zugang anfragen</a>
                <a class="btn btn-fw-outline ms-lg-2" href="<?= h(FMS_MITGLIEDERPOTAL_URL) ?>">Mitgliederportal</a>
                <a class="btn btn-fw-outline ms-lg-2" href="<?= h(FMS_ANMELDE_URL) ?>">Anmelden</a>
                <button class="btn theme-toggle ms-lg-1" type="button" id="themeToggle" aria-label="Zwischen hellem und dunklem Layout wechseln" title="Layout wechseln"><i class="bi bi-moon-stars-fill"></i></button>
            </div>
        </div>
    </nav>
</header>

<main class="section container legal">
    <div class="legal-body">
        <span class="eyebrow">Rechtliches</span>
        <h1>Impressum</h1>

        <h2>Angaben gemäß § 5 DDG</h2>
        <p>
            <?= h(FMS_BETREIBER_NAME) ?><br>
            <?= h(FMS_BETREIBER_ANSCHRIFT) ?>
        </p>

        <h2>Kontakt</h2>
        <p>
            <?php if (FMS_BETREIBER_TELEFON !== ''): ?>Telefon: <?= h(FMS_BETREIBER_TELEFON) ?><br><?php endif; ?>
            E-Mail: <?= h(FMS_BETREIBER_EMAIL) ?>
        </p>

        <h2>Verantwortlich für den Inhalt nach § 18 Abs. 2 MStV</h2>
        <p>
            <?= h(FMS_BETREIBER_NAME) ?><br>
            <?= h(FMS_BETREIBER_ANSCHRIFT) ?>
        </p>

        <h2>Haftung für Inhalte</h2>
        <p>Als Diensteanbieter sind wir nach den allgemeinen Gesetzen für eigene Inhalte auf diesen Seiten verantwortlich. Wir sind jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen. Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden entsprechender Rechtsverletzungen entfernen wir diese Inhalte umgehend.</p>

        <h2>Haftung für Links</h2>
        <p>Diese Website enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber verantwortlich. Zum Zeitpunkt der Verlinkung waren keine Rechtsverstöße erkennbar. Bei Bekanntwerden von Rechtsverletzungen entfernen wir derartige Links umgehend.</p>

        <h2>Urheberrecht</h2>
        <p>Die durch den Betreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Beiträge Dritter sind als solche gekennzeichnet. Eine Vervielfältigung, Bearbeitung, Verbreitung oder Verwertung außerhalb der Grenzen des Urheberrechts bedarf der schriftlichen Zustimmung des jeweiligen Rechteinhabers.</p>

        <h2>Datenschutz</h2>
        <p>Informationen zur Verarbeitung personenbezogener Daten finden Sie in der <a href="datenschutz.php">Datenschutzerklärung</a>.</p>

        <p class="mt-4"><a class="btn btn-fw-outline" href="index.php"><i class="bi bi-arrow-left"></i> Zurück zur Startseite</a></p>
    </div>
</main>

<footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <a class="footer-brand" href="index.php#start"><img src="assets/fms-logo.png" alt="" width="30" height="30"> FeuerwehrManagementSystem</a>
        <span>© <?= date('Y') ?> FeuerwehrManagementSystem · <a href="impressum.php">Impressum</a> · <a href="datenschutz.php">Datenschutz</a></span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/menu.js"></script>
</body>
</html>
