<?php
declare(strict_types=1);

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

defined('FMS_ZUGANGSANFRAGE_API_URL') || define('FMS_ZUGANGSANFRAGE_API_URL', '');
defined('FMS_ZUGANGSANFRAGE_SCHLUESSEL') || define('FMS_ZUGANGSANFRAGE_SCHLUESSEL', '');
defined('FMS_ANMELDE_URL') || define('FMS_ANMELDE_URL', 'https://app.feuerwehrmanagementsystem.de/login.php');
defined('FMS_MITGLIEDERPOTAL_URL') || define('FMS_MITGLIEDERPOTAL_URL', 'https://portal.feuerwehrmanagementsystem.de/');


$sicher = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
session_name('FMS_HOMEPAGE');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => $sicher,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://cdn.jsdelivr.net; font-src https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:; form-action 'self'; frame-ancestors 'none'; base-uri 'self'");

function h(string $wert): string
{
    return htmlspecialchars($wert, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function fms_anfrage_senden(array $daten): bool
{
    $url = (string)FMS_ZUGANGSANFRAGE_API_URL;
    $schluessel = (string)FMS_ZUGANGSANFRAGE_SCHLUESSEL;
    $urlTeile = parse_url($url);
    if (
        !function_exists('curl_init')
        || !preg_match('/^[a-f0-9]{64}$/i', $schluessel)
        || !$urlTeile
        || strtolower((string)($urlTeile['scheme'] ?? '')) !== 'https'
        || empty($urlTeile['host'])
        || (isset($urlTeile['port']) && (int)$urlTeile['port'] !== 443)
    ) {
        error_log('Homepage: Zugangsanfrage nicht gesendet; API-Konfiguration oder cURL fehlt.');
        return false;
    }

    $zeit = (string)time();
    $nachricht = [
        'request_id' => bin2hex(random_bytes(16)),
        'client_hash' => hash_hmac('sha256', (string)($_SERVER['REMOTE_ADDR'] ?? ''), $schluessel),
        'ansprechpartner' => $daten['ansprechpartner'],
        'wehr' => $daten['wehr'],
        'ort' => $daten['ort'],
        'email' => $daten['email'],
        'telefon' => $daten['telefon'],
        'nachricht' => $daten['nachricht'],
    ];
    $body = json_encode($nachricht, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($body)) {
        error_log('Homepage: Zugangsanfrage konnte nicht als JSON kodiert werden.');
        return false;
    }

    $curl = curl_init($url);
    if ($curl === false) {
        error_log('Homepage: cURL konnte für eine Zugangsanfrage nicht initialisiert werden.');
        return false;
    }
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $body,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-FMS-Zeit: ' . $zeit,
            'X-FMS-Signatur: ' . hash_hmac('sha256', $zeit . "\n" . $body, $schluessel),
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 12,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    $antwort = curl_exec($curl);
    $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $curlFehler = curl_error($curl);
    curl_close($curl);

    if (!is_string($antwort) || $status < 200 || $status >= 300) {
        error_log('Homepage: FMS-API lehnte eine Zugangsanfrage ab (HTTP ' . $status . '). ' . $curlFehler);
        return false;
    }
    $ergebnis = json_decode($antwort, true);
    if (!is_array($ergebnis) || ($ergebnis['ok'] ?? false) !== true) {
        error_log('Homepage: FMS-API lieferte eine ungültige Bestätigung für eine Zugangsanfrage.');
        return false;
    }
    return true;
}

$csrf = (string)($_SESSION['csrf'] ?? '');
if ($csrf === '') {
    $csrf = bin2hex(random_bytes(32));
    $_SESSION['csrf'] = $csrf;
}
$formular = [
    'ansprechpartner' => '',
    'wehr' => '',
    'ort' => '',
    'email' => '',
    'telefon' => '',
    'nachricht' => '',
];
$fehler = [];
$erfolg = !empty($_SESSION['anfrage_erfolg']);
unset($_SESSION['anfrage_erfolg']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($formular as $feld => $_) {
        $wert = $_POST[$feld] ?? '';
        if (!is_string($wert)) {
            $fehler[] = 'Bitte verwenden Sie die vorgesehenen Eingabefelder.';
            $formular[$feld] = '';
        } else {
            $formular[$feld] = trim($wert);
        }
    }
    $tokenRoh = $_POST['csrf'] ?? '';
    $token = is_string($tokenRoh) ? $tokenRoh : '';
    if ($token === '' || !hash_equals($csrf, $token)) {
        http_response_code(403);
        $fehler[] = 'Das Formular ist abgelaufen. Bitte laden Sie die Seite neu und versuchen Sie es erneut.';
    }
    $honeypot = $_POST['website'] ?? '';
    if (!is_string($honeypot) || trim($honeypot) !== '') {
        http_response_code(400);
        $fehler[] = 'Die Anfrage konnte nicht verarbeitet werden.';
    }
    if (mb_strlen($formular['ansprechpartner']) < 2 || mb_strlen($formular['ansprechpartner']) > 120) {
        $fehler[] = 'Bitte geben Sie einen Namen mit höchstens 120 Zeichen an.';
    }
    if (mb_strlen($formular['wehr']) < 2 || mb_strlen($formular['wehr']) > 160) {
        $fehler[] = 'Bitte geben Sie den Namen Ihrer Feuerwehr mit höchstens 160 Zeichen an.';
    }
    if (mb_strlen($formular['ort']) < 2 || mb_strlen($formular['ort']) > 120) {
        $fehler[] = 'Bitte geben Sie den Ort mit höchstens 120 Zeichen an.';
    }
    if (mb_strlen($formular['email']) > 254 || !filter_var($formular['email'], FILTER_VALIDATE_EMAIL)) {
        $fehler[] = 'Bitte geben Sie eine gültige E-Mail-Adresse an.';
    }
    if (mb_strlen($formular['telefon']) > 50) {
        $fehler[] = 'Die Telefonnummer darf höchstens 50 Zeichen enthalten.';
    }
    if (mb_strlen($formular['nachricht']) > 2000) {
        $fehler[] = 'Die Nachricht darf höchstens 2.000 Zeichen enthalten.';
    }
    if (!is_string($_POST['datenschutz'] ?? null) || $_POST['datenschutz'] !== '1') {
        $fehler[] = 'Bitte bestätigen Sie die Hinweise zur Übermittlung und Speicherung Ihrer Angaben.';
    }
    foreach ($formular as $wert) {
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $wert)) {
            $fehler[] = 'Bitte entfernen Sie nicht unterstützte Steuerzeichen aus Ihren Angaben.';
            break;
        }
    }

    if (!$fehler) {
        $formular['email'] = mb_strtolower($formular['email']);
        if (fms_anfrage_senden($formular)) {
            $_SESSION['anfrage_erfolg'] = true;
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
            header('Location: #zugang', true, 303);
            exit;
        } else {
            http_response_code(503);
            $fehler[] = 'Ihre Anfrage konnte gerade nicht übermittelt werden. Bitte versuchen Sie es später erneut.';
        }
    }
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    $csrf = $_SESSION['csrf'];
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Das FeuerwehrManagementSystem unterstützt Feuerwehren bei Mitgliederverwaltung, Einsätzen, Ausbildung und Organisation.">
    <title>FeuerwehrManagementSystem – Verwaltung, die zusammenarbeitet</title>
    <link rel="icon" href="assets/fms-logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" integrity="sha384-tViUnnbYAV00FLIhhi3v/dWt3Jxw4gZQcNoSCxCIFNJVCx7/D55/wXsrNIRANwdD" crossorigin="anonymous" rel="stylesheet">
    <link href="assets/homepage.css" rel="stylesheet">
    <script src="assets/theme.js"></script>
</head>
<body>
<header class="site-header">
    <nav class="navbar navbar-expand-lg container" aria-label="Hauptnavigation">
        <a class="navbar-brand d-flex align-items-center gap-2" href="#start">
            <img src="assets/fms-logo.png" alt="" width="42" height="42">
            <span>FeuerwehrManagementSystem</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#hauptmenue" aria-controls="hauptmenue" aria-expanded="false" aria-label="Menü öffnen">
            <i class="bi bi-list"></i>
        </button>
        <div class="collapse navbar-collapse" id="hauptmenue">
            <div class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                <a class="nav-link" href="#funktionen">Funktionen</a>
                <a class="nav-link" href="#vorteile">Für die Feuerwehr</a>
                <a class="nav-link" href="#historie">Historie</a>
                <a class="btn btn-fw ms-lg-2" href="#zugang">Zugang anfragen</a>
                <a class="btn btn-fw-outline ms-lg-2" href="<?= h(FMS_MITGLIEDERPOTAL_URL) ?>">Mitgliederportal</a>
                <a class="btn btn-fw-outline ms-lg-2" href="<?= h(FMS_ANMELDE_URL) ?>">Anmelden</a>
                <button class="btn theme-toggle ms-lg-1" type="button" id="themeToggle" aria-label="Zwischen hellem und dunklem Layout wechseln" title="Layout wechseln"><i class="bi bi-moon-stars-fill"></i></button>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="hero" id="start">
        <div class="container hero-grid">
            <div class="hero-copy">
                <span class="eyebrow"><i class="bi bi-fire"></i> Feuerwehrverwaltung, einfach organisiert</span>
                <h1>Mehr Überblick für den <span>Feuerwehralltag.</span></h1>
                <p class="hero-lead">Mitglieder, Einsätze, Ausbildung und Fahrzeuge an einem Ort – damit im Hintergrund alles läuft und im Einsatz jeder Handgriff sitzt.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-fw btn-lg" href="#zugang">Unverbindlich Zugang anfragen <i class="bi bi-arrow-right"></i></a>
                    <a class="btn btn-fw-outline btn-lg" href="#funktionen">Funktionen entdecken</a>
                </div>
                <div class="hero-note"><i class="bi bi-check-circle-fill"></i> Für Feuerwehren entwickelt. Klar strukturiert. Im Alltag zuhause.</div>
            </div>
            <div class="hero-visual" aria-hidden="true">
                <div class="visual-card visual-main">
                    <div class="visual-top"><span class="visual-mark"><i class="bi bi-fire"></i></span><span class="visual-dots">•••</span></div>
                    <span class="visual-label">FeuerwehrManagementSystem</span>
                    <strong>Alles im Blick.</strong>
                    <div class="visual-stats">
                        <div><i class="bi bi-people-fill"></i><span>Mitglieder</span><b>Verwaltung</b></div>
                        <div><i class="bi bi-calendar-check-fill"></i><span>Termine</span><b>Planung</b></div>
                        <div><i class="bi bi-truck"></i><span>Fahrzeuge</span><b>Übersicht</b></div>
                    </div>
                    <div class="visual-progress"><span></span></div>
                    <small>Gemeinsam organisiert</small>
                </div>
                <div class="visual-badge"><i class="bi bi-shield-check"></i><span>Ein System.<br><strong>Viele Aufgaben.</strong></span></div>
                <div class="visual-orbit orbit-one"></div>
                <div class="visual-orbit orbit-two"></div>
            </div>
        </div>
        <div class="hero-bottom container">
            <span><i class="bi bi-people"></i> Mitglieder</span><span><i class="bi bi-broadcast-pin"></i> Einsätze</span>
            <span><i class="bi bi-mortarboard"></i> Ausbildung</span><span><i class="bi bi-tools"></i> Organisation</span>
        </div>
    </section>

    <section class="section container" id="funktionen">
        <div class="section-heading">
            <span class="eyebrow">Weniger suchen. Mehr erledigen.</span>
            <h2>Die wichtigen Aufgaben. <span>Einfach an einem Ort.</span></h2>
            <p>Das FMS bündelt die tägliche Verwaltung Ihrer Feuerwehr in übersichtlichen Bereichen.</p>
        </div>
        <div class="row g-3">
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon red"><i class="bi bi-people-fill"></i></span><h3>Mitgliederverwaltung</h3><p>Stammdaten, Qualifikationen und Berechtigungen der Mannschaft übersichtlich verwalten.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon blue"><i class="bi bi-fire"></i></span><h3>Einsätze &amp; Berichte</h3><p>Einsätze dokumentieren, Anwesenheiten erfassen und Informationen strukturiert wiederfinden.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon green"><i class="bi bi-mortarboard-fill"></i></span><h3>Ausbildung &amp; Lehrgänge</h3><p>Ausbildungsstände und Lehrgänge im Blick behalten und nachvollziehbar planen.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon amber"><i class="bi bi-truck-front-fill"></i></span><h3>Fahrzeuge &amp; Geräte</h3><p>Fahrzeuge, Prüffristen und gemeldete Mängel übersichtlich organisieren.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon dark"><i class="bi bi-calendar-week-fill"></i></span><h3>Termine &amp; Dienste</h3><p>Dienstabende, Veranstaltungen und Beteiligung an einem Platz koordinieren.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon red"><i class="bi bi-bar-chart-fill"></i></span><h3>Auswertungen &amp; Berichte</h3><p>Wichtige Kennzahlen und Übersichten für die Arbeit Ihrer Feuerwehr zusammenstellen.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon blue"><i class="bi bi-geo-alt-fill"></i></span><h3>Karte, Hydranten &amp; Objekte</h3><p>Hydrantenverzeichnis und relevante Objekte auf der Karte darstellen und schnell vor Ort finden.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon amber"><i class="bi bi-cup-hot-fill"></i></span><h3>Getränke &amp; Abrechnung</h3><p>Getränkebestände organisieren und Ausgaben sowie Abrechnungen übersichtlich führen.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon green"><i class="bi bi-phone-fill"></i></span><h3>Mitgliederportal</h3><p>Mitglieder können wichtige Informationen und persönliche Funktionen auch mobil selbst erreichen.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon blue"><i class="bi bi-cloud-arrow-up-fill"></i></span><h3>Push-API für Ihre Homepage</h3><p>Aktuelle Termine, Einsätze und Neuigkeiten per Push-Schnittstelle automatisch auf der Website Ihrer Feuerwehr veröffentlichen.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon dark"><i class="bi bi-shield-lock-fill"></i></span><h3>Zwei-Faktor-Authentifizierung</h3><p>Zusätzlicher Schutz der Benutzerkonten durch 2FA – damit sensible Mitglieder- und Einsatzdaten sicher bleiben.</p></article></div>
            <div class="col-md-6 col-lg-4"><article class="feature-card"><span class="feature-icon amber"><i class="bi bi-puzzle-fill"></i></span><h3>Kundenspezifische Implementierungen</h3><p>Individuelle Lösungen, zum Beispiel für die Anwesenheitsdokumentation, sind auf Anfrage möglich.</p></article></div>
        </div>
    </section>

    <section class="benefits">
        <div class="container benefits-grid" id="vorteile">
            <div>
                <span class="eyebrow">Für den echten Feuerwehralltag</span>
                <h2>Weniger Zettelwirtschaft.<br><span>Mehr Zeit fürs Wesentliche.</span></h2>
                <p>Ein klarer Überblick erleichtert die Zusammenarbeit zwischen Führung, Verwaltung und Mannschaft – am Schreibtisch und unterwegs.</p>
                <a class="btn btn-fw" href="#zugang">FMS kennenlernen <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="benefit-list">
                <div><span><i class="bi bi-check-lg"></i></span><p><strong>Zusammenhängende Informationen</strong><br>Statt verteilter Listen und Einzellösungen.</p></div>
                <div><span><i class="bi bi-check-lg"></i></span><p><strong>Übersichtliche Bereiche</strong><br>Für die Aufgaben, die im Alltag wirklich anfallen.</p></div>
                <div><span><i class="bi bi-check-lg"></i></span><p><strong>Einfacher Einstieg</strong><br>Fragen Sie unverbindlich einen Zugang für Ihre Feuerwehr an.</p></div>
            </div>
        </div>
    </section>

    <section class="section container" id="historie">
        <div class="section-heading">
            <span class="eyebrow">Historie</span>
            <h2>Über 10 Jahre Erfahrung. <span>Aus der Praxis gewachsen.</span></h2>
            <p>Seit über 10 Jahren managen wir Einheiten der Freiwilligen Feuerwehr – das FMS ist aus dieser Erfahrung entstanden.</p>
        </div>
        <ol class="timeline">
            <li><span class="timeline-year">2014</span><p>Start mit einer Java-Anwendung zur Verwaltung von Einheiten der Freiwilligen Feuerwehr.</p></li>
            <li><span class="timeline-year">2015</span><p>Erster Live-Einsatz im Feuerwehralltag.</p></li>
            <li><span class="timeline-year">2017</span><p>Start der Implementierung der Divera-Schnittstelle und des Einsatzmonitors.</p></li>
            <li><span class="timeline-year">2018</span><p>Einbindung der digitalen Getränkeabrechnung.</p></li>
            <li><span class="timeline-year">2026</span><p>Transfer aller Funktionen in eine Webanwendung.</p></li>
        </ol>
    </section>

    <section class="section container donate-section" id="spenden">
        <div class="donate-card">
            <span class="donate-icon"><i class="bi bi-heart-fill"></i></span>
            <div>
                <span class="eyebrow">Spenden &amp; Kosten</span>
                <h2>Möglichst kostenlos. <span>Mit Ihrer Unterstützung.</span></h2>
                <p>Wir möchten versuchen, das Feuerwehr Management System weitestgehend kostenlos anzubieten. Allerdings sind wir auf Spenden angewiesen, um den Betrieb des Servers sicherzustellen. Wir würden uns freuen, wenn Ihr uns bei diesem Vorhaben unterstützt.</p>
            </div>
        </div>
    </section>

    <section class="section request-section" id="zugang">
        <div class="container request-grid">
            <div class="request-copy">
                <span class="eyebrow">Der nächste Schritt</span>
                <h2>Interesse am FMS?<br><span>Sprechen wir darüber.</span></h2>
                <p>Senden Sie uns die wichtigsten Angaben zu Ihrer Feuerwehr. Die Anfrage wird direkt an das Betreiberportal übermittelt – Sie erhalten anschließend eine persönliche Rückmeldung.</p>
                <div class="request-promise"><i class="bi bi-lock-fill"></i><span>Ihre Angaben werden ausschließlich zur Bearbeitung Ihrer Zugangsanfrage an das FeuerwehrManagementSystem übermittelt. Zur Missbrauchsabwehr wird dort außerdem eine pseudonymisierte Prüfsumme Ihrer IP-Adresse gespeichert. Mehr dazu in der <a href="datenschutz.php">Datenschutzerklärung</a>.</span></div>
            </div>
            <div class="request-card">
                <?php if ($erfolg): ?>
                    <div class="alert alert-success" role="status"><i class="bi bi-check-circle-fill me-2"></i><strong>Vielen Dank!</strong> Ihre Zugangsanfrage ist eingegangen. Wir melden uns bei Ihnen.</div>
                <?php endif; ?>
                <?php if ($fehler): ?>
                    <div class="alert alert-danger" role="alert"><strong>Bitte prüfen Sie Ihre Angaben:</strong><ul class="mb-0 mt-2"><?php foreach ($fehler as $meldung): ?><li><?= h($meldung) ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>
                <form method="post" action="#zugang">
                    <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
                    <div class="form-trap" aria-hidden="true"><label for="website">Website</label><input id="website" name="website" tabindex="-1" autocomplete="off"></div>
                    <div class="row g-3">
                        <div class="col-sm-6"><label class="form-label" for="ansprechpartner">Ihr Name *</label><input class="form-control" id="ansprechpartner" name="ansprechpartner" required maxlength="120" autocomplete="name" value="<?= h($formular['ansprechpartner']) ?>"></div>
                        <div class="col-sm-6"><label class="form-label" for="wehr">Feuerwehr / Einheit *</label><input class="form-control" id="wehr" name="wehr" required maxlength="160" autocomplete="organization" placeholder="z. B. Feuerwehr Musterstadt" value="<?= h($formular['wehr']) ?>"></div>
                        <div class="col-sm-6"><label class="form-label" for="ort">Ort *</label><input class="form-control" id="ort" name="ort" required maxlength="120" autocomplete="address-level2" value="<?= h($formular['ort']) ?>"></div>
                        <div class="col-sm-6"><label class="form-label" for="email">E-Mail-Adresse *</label><input class="form-control" id="email" name="email" type="email" required maxlength="254" autocomplete="email" value="<?= h($formular['email']) ?>"></div>
                        <div class="col-12"><label class="form-label" for="telefon">Telefon <span class="text-secondary">(optional)</span></label><input class="form-control" id="telefon" name="telefon" type="tel" maxlength="50" autocomplete="tel" value="<?= h($formular['telefon']) ?>"></div>
                        <div class="col-12"><label class="form-label" for="nachricht">Ihre Nachricht <span class="text-secondary">(optional)</span></label><textarea class="form-control" id="nachricht" name="nachricht" rows="3" maxlength="2000" placeholder="Was möchten Sie über das FMS erfahren?"><?= h($formular['nachricht']) ?></textarea></div>
                        <div class="col-12"><div class="form-check"><input class="form-check-input" id="datenschutz" name="datenschutz" type="checkbox" value="1" required><label class="form-check-label" for="datenschutz">Ich bin einverstanden, dass meine Angaben und eine pseudonymisierte IP-Prüfsumme zur Bearbeitung und Missbrauchsabwehr an das FeuerwehrManagementSystem übermittelt und dort gespeichert werden. Die <a href="datenschutz.php" target="_blank">Datenschutzerklärung</a> habe ich zur Kenntnis genommen. *</label></div></div>
                        <div class="col-12"><button class="btn btn-fw btn-lg w-100" type="submit">Zugang unverbindlich anfragen <i class="bi bi-arrow-right"></i></button><small class="form-text d-block mt-2">* Pflichtangaben</small></div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <a class="footer-brand" href="#start"><img src="assets/fms-logo.png" alt="" width="30" height="30"> FeuerwehrManagementSystem</a>
        <span>© <?= date('Y') ?> FeuerwehrManagementSystem · Ein System für die Organisation des Feuerwehralltags. · <a href="impressum.php">Impressum</a> · <a href="datenschutz.php">Datenschutz</a></span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/menu.js"></script>
</body>
</html>
