<?php
declare(strict_types=1);

if (is_file(__DIR__ . '/config.local.php')) {
    require_once __DIR__ . '/config.local.php';
}

// Angaben zum Verantwortlichen – in config.local.php überschreiben.
defined('FMS_BETREIBER_NAME') || define('FMS_BETREIBER_NAME', '[Name des Betreibers bitte ergänzen]');
defined('FMS_BETREIBER_ANSCHRIFT') || define('FMS_BETREIBER_ANSCHRIFT', '[Straße Hausnummer, PLZ Ort]');
defined('FMS_BETREIBER_EMAIL') || define('FMS_BETREIBER_EMAIL', '[E-Mail-Adresse bitte ergänzen]');
defined('FMS_HOSTER') || define('FMS_HOSTER', '[Hosting-Anbieter, Standort Deutschland bitte ergänzen]');
defined('FMS_ANMELDE_URL') || define('FMS_ANMELDE_URL', 'https://app.feuerwehrmanagementsystem.de/login.php');

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
    <title>Datenschutzerklärung – FeuerwehrManagementSystem</title>
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
                <a class="btn btn-fw-outline ms-lg-2" href="<?= h(FMS_ANMELDE_URL) ?>">Anmelden</a>
                <button class="btn theme-toggle ms-lg-1" type="button" id="themeToggle" aria-label="Zwischen hellem und dunklem Layout wechseln" title="Layout wechseln"><i class="bi bi-moon-stars-fill"></i></button>
            </div>
        </div>
    </nav>
</header>

<main class="section container legal">
    <div class="legal-body">
        <span class="eyebrow">Datenschutz</span>
        <h1>Datenschutzerklärung</h1>
        <p class="text-secondary">Stand: November 2026</p>

        <h2>1. Verantwortlicher</h2>
        <p>Verantwortlich im Sinne der Datenschutz-Grundverordnung (DSGVO) für diese Website ist:</p>
        <p>
            <?= h(FMS_BETREIBER_NAME) ?><br>
            <?= h(FMS_BETREIBER_ANSCHRIFT) ?><br>
            E-Mail: <?= h(FMS_BETREIBER_EMAIL) ?>
        </p>

        <h2>2. Hosting und Server-Logfiles</h2>
        <p>Diese Website wird bei <?= h(FMS_HOSTER) ?> in Deutschland gehostet. Beim Aufruf der Seite verarbeitet der Webserver technisch notwendige Daten, die Ihr Browser automatisch übermittelt: IP-Adresse, Datum und Uhrzeit des Zugriffs, aufgerufene Seite bzw. Datei, übertragene Datenmenge, HTTP-Statuscode, Referrer-URL sowie Browser- und Betriebssysteminformationen (User-Agent).</p>
        <p><strong>Zweck:</strong> Auslieferung der Website, Gewährleistung von Stabilität und Sicherheit, Abwehr von Angriffen und Missbrauch.<br>
        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse am sicheren und fehlerfreien Betrieb).<br>
        <strong>Speicherdauer:</strong> Die Logfiles werden nach der beim Hosting-Anbieter üblichen, kurzen Frist automatisch gelöscht, sofern sie nicht zur Aufklärung eines Sicherheitsvorfalls länger benötigt werden.</p>
        <p>Mit dem Hosting-Anbieter besteht – soweit erforderlich – ein Vertrag zur Auftragsverarbeitung nach Art. 28 DSGVO.</p>

        <h2>3. Einbindung externer Ressourcen (CDN)</h2>
        <p>Zur Darstellung der Seite laden wir Stylesheets, Schriftarten (Icons) und JavaScript-Bibliotheken (Bootstrap und Bootstrap Icons) über das Content Delivery Network <strong>jsDelivr</strong> (<code>cdn.jsdelivr.net</code>). Dabei wird Ihre IP-Adresse an die Server des CDN-Betreibers übermittelt, damit die Dateien ausgeliefert werden können. Die Dateien werden mit Integritätsprüfung (Subresource Integrity) geladen. Wir setzen darüber hinaus keine Analyse-, Tracking- oder Werbedienste ein und binden keine Karten, Videos oder Social-Media-Plugins ein.</p>
        <p><strong>Zweck:</strong> schnelle und zuverlässige Bereitstellung der Darstellungs- und Bedienelemente.<br>
        <strong>Rechtsgrundlage:</strong> Art. 6 Abs. 1 lit. f DSGVO (berechtigtes Interesse an einer performanten und einheitlichen Darstellung der Website).</p>
        <p>Der Betreiber von jsDelivr kann Ihre IP-Adresse außerhalb unseres Einflussbereichs verarbeiten. Weitere Informationen entnehmen Sie der Datenschutzerklärung des Anbieters unter <a href="https://www.jsdelivr.com/terms/privacy-policy-jsdelivr-net" rel="noopener noreferrer" target="_blank">jsdelivr.com</a>.</p>

        <h2>4. Cookies und lokale Speicherung</h2>
        <p>Diese Website verwendet ausschließlich technisch notwendige Speichermechanismen. Es werden keine Cookies zu Analyse-, Marketing- oder Tracking-Zwecken gesetzt, eine Einwilligungsabfrage (Cookie-Banner) ist daher nicht erforderlich.</p>
        <ul>
            <li><strong>Sitzungs-Cookie <code>FMS_HOMEPAGE</code>:</strong> Enthält eine zufällige Sitzungskennung und dient dem Schutz des Kontaktformulars vor missbräuchlichen Anfragen (CSRF-Schutz) sowie der Anzeige der Bestätigung nach dem Absenden. Das Cookie wird beim Schließen des Browsers gelöscht.</li>
            <li><strong>Lokaler Speicher (<code>localStorage</code>) – Eintrag <code>fms-theme</code>:</strong> Speichert Ihre Auswahl zwischen hellem und dunklem Layout ausschließlich in Ihrem Browser. Die Information wird nicht an uns übertragen.</li>
        </ul>
        <p><strong>Rechtsgrundlage:</strong> § 25 Abs. 2 Nr. 2 TDDDG (unbedingt erforderlich für den ausdrücklich gewünschten Dienst) sowie Art. 6 Abs. 1 lit. f DSGVO.</p>

        <h2>5. Zugangsanfrage über das Formular</h2>
        <p>Wenn Sie über das Formular einen Zugang zum FeuerwehrManagementSystem anfragen, verarbeiten wir folgende Angaben:</p>
        <ul>
            <li>Ihr Name (Ansprechpartner), Name der Feuerwehr / Einheit und Ort (Pflichtangaben),</li>
            <li>Ihre E-Mail-Adresse (Pflichtangabe),</li>
            <li>Ihre Telefonnummer und Ihre Nachricht (freiwillige Angaben),</li>
            <li>eine <strong>pseudonymisierte Prüfsumme Ihrer IP-Adresse</strong> (mit einem geheimen Schlüssel erzeugter Hash, aus dem die IP-Adresse nicht zurückgerechnet werden kann).</li>
        </ul>
        <p>Die Homepage selbst speichert diese Angaben nicht. Sie werden verschlüsselt (HTTPS) und signiert an das Betreiberportal des FeuerwehrManagementSystems (<code>app.feuerwehrmanagementsystem.de</code>) übermittelt und dort in einer Datenbank gespeichert. Das Portal wird vom Verantwortlichen selbst in Deutschland betrieben; eine Weitergabe an Dritte erfolgt nicht.</p>
        <p><strong>Zweck:</strong> Bearbeitung Ihrer Anfrage und persönliche Rückmeldung; die IP-Prüfsumme dient ausschließlich der Missbrauchsabwehr (z. B. Begrenzung automatisierter Massenanfragen).<br>
        <strong>Rechtsgrundlage:</strong> Ihre Einwilligung, die Sie durch Setzen des Häkchens im Formular erteilen (Art. 6 Abs. 1 lit. a DSGVO), sowie – für die Bearbeitung Ihrer Anfrage zur Anbahnung einer Nutzung – Art. 6 Abs. 1 lit. b DSGVO; für die Missbrauchsabwehr Art. 6 Abs. 1 lit. f DSGVO.<br>
        <strong>Speicherdauer:</strong> Ihre Anfrage wird gelöscht, sobald sie abschließend bearbeitet ist und keine gesetzlichen Aufbewahrungspflichten entgegenstehen. Wird ein Zugang eingerichtet, werden die Angaben im Rahmen des dann bestehenden Nutzungsverhältnisses weiterverarbeitet.</p>
        <p>Sie können Ihre Einwilligung jederzeit mit Wirkung für die Zukunft widerrufen (siehe Abschnitt 8).</p>

        <h2>6. Anmeldung im FeuerwehrManagementSystem</h2>
        <p>Der Link „Anmelden“ führt zur eigenständigen Webanwendung unter <code>app.feuerwehrmanagementsystem.de</code>. Für die dortige Verarbeitung personenbezogener Daten (z. B. Mitglieder-, Einsatz- und Ausbildungsdaten) gelten gesonderte Datenschutzhinweise bzw. Vereinbarungen zur Auftragsverarbeitung mit der jeweiligen Feuerwehr bzw. Trägerkörperschaft. Diese Erklärung betrifft nur die vorliegende Informationswebsite.</p>

        <h2>7. Empfänger und Drittlandübermittlung</h2>
        <p>Empfänger Ihrer Daten sind der Hosting-Anbieter (Abschnitt 2, als Auftragsverarbeiter) und der CDN-Betreiber (Abschnitt 3). Eine gezielte Übermittlung in Staaten außerhalb der EU/des EWR findet nicht statt; beim Abruf von Dateien über das CDN kann jedoch nicht ausgeschlossen werden, dass Daten über Server außerhalb Deutschlands geleitet werden.</p>

        <h2>8. Ihre Rechte</h2>
        <p>Sie haben gegenüber dem Verantwortlichen folgende Rechte hinsichtlich Ihrer personenbezogenen Daten:</p>
        <ul>
            <li>Auskunft (Art. 15 DSGVO),</li>
            <li>Berichtigung (Art. 16 DSGVO),</li>
            <li>Löschung (Art. 17 DSGVO),</li>
            <li>Einschränkung der Verarbeitung (Art. 18 DSGVO),</li>
            <li>Datenübertragbarkeit (Art. 20 DSGVO),</li>
            <li>Widerspruch gegen Verarbeitungen auf Grundlage berechtigter Interessen (Art. 21 DSGVO). Dazu genügt eine formlose Mitteilung an die oben genannte E-Mail-Adresse.</li>
            <li>Widerruf einer erteilten Einwilligung (Art. 7 Abs. 3 DSGVO) mit Wirkung für die Zukunft; die Rechtmäßigkeit der bis dahin erfolgten Verarbeitung bleibt unberührt.</li>
        </ul>
        <p>Außerdem haben Sie das Recht, sich bei einer Datenschutz-Aufsichtsbehörde zu beschweren (Art. 77 DSGVO), insbesondere in dem Bundesland Ihres gewöhnlichen Aufenthaltsorts, Ihres Arbeitsplatzes oder des Orts des mutmaßlichen Verstoßes. Eine Übersicht der Behörden finden Sie beim <a href="https://www.bfdi.bund.de/DE/Service/Anschriften/Laender/Laender-node.html" rel="noopener noreferrer" target="_blank">Bundesbeauftragten für den Datenschutz und die Informationsfreiheit</a>.</p>

        <h2>9. Keine automatisierte Entscheidungsfindung</h2>
        <p>Eine ausschließlich automatisierte Entscheidungsfindung einschließlich Profiling im Sinne von Art. 22 DSGVO findet nicht statt.</p>

        <h2>10. Datensicherheit</h2>
        <p>Die Übertragung der Website und der Formulardaten erfolgt per TLS-Verschlüsselung (HTTPS). Die Übermittlung an das Betreiberportal wird zusätzlich signiert und zeitlich begrenzt, um Manipulation und Missbrauch zu verhindern.</p>

        <h2>11. Änderungen dieser Erklärung</h2>
        <p>Wir passen diese Datenschutzerklärung an, wenn sich die Website, die eingesetzten Dienste oder die Rechtslage ändern. Es gilt jeweils die hier veröffentlichte aktuelle Fassung.</p>

        <p class="mt-4"><a class="btn btn-fw-outline" href="index.php"><i class="bi bi-arrow-left"></i> Zurück zur Startseite</a></p>
    </div>
</main>

<footer class="site-footer">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <a class="footer-brand" href="index.php#start"><img src="assets/fms-logo.png" alt="" width="30" height="30"> FeuerwehrManagementSystem</a>
        <span>© <?= date('Y') ?> FeuerwehrManagementSystem · <a href="datenschutz.php">Datenschutz</a></span>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="assets/menu.js"></script>
</body>
</html>
