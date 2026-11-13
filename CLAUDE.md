# CLAUDE.md

Produkt-Homepage des FeuerwehrManagementSystems (FMS). Reines PHP ohne Framework, ohne Build-Schritt, ohne eigene Datenbank. Sprache von Code-Kommentaren, Texten und Commit-Nachrichten: Deutsch.

## Struktur

- `index.php` – Startseite inkl. Formular „Zugang anfragen" (Session, CSRF-Token, Honeypot, serverseitiger cURL-Versand an die FMS-API)
- `impressum.php`, `datenschutz.php` – Rechtsseiten; Betreiberdaten kommen aus Konstanten
- `assets/` – `homepage.css`, `theme.js` (Hell/Dunkel), `menu.js`, Logo
- `.htaccess` – blendet `.php` in URLs aus (301-Umleitung + internes Rewrite) und sperrt `config.local*.php`. Mit `php -S` wirkungslos, dort funktionieren nur die `.php`-URLs; Links im Code daher mit `.php` belassen.
- `config.local.php` – **nicht versioniert** (siehe `.gitignore`), enthält Schlüssel und Betreiberdaten; Vorlage: `config.local.beispiel.php`

## Entwickeln

```sh
php -S localhost:8000
```

Es gibt keine Tests und keinen Linter. Der tatsächliche Formularversand braucht PHP mit cURL und einen gesetzten `FMS_ZUGANGSANFRAGE_SCHLUESSEL`.

## Konventionen

- Jede Seite lädt `config.local.php` (falls vorhanden) und setzt Standardwerte mit `defined(...) || define(...)`. Neue Konfigurationswerte immer so anlegen und in `config.local.beispiel.php` dokumentieren.
- Betreiberdaten: `FMS_BETREIBER_NAME`, `FMS_BETREIBER_ANSCHRIFT`, `FMS_BETREIBER_EMAIL`, `FMS_BETREIBER_TELEFON` (optional), `FMS_HOSTER`. Nicht in Seitentexte hartcodieren.
- Ausgaben immer über `h()` escapen. Jede Seite definiert `h()` selbst (kein gemeinsames Include).
- Jede Seite setzt Sicherheits-Header inkl. strenger Content-Security-Policy. Externe Ressourcen nur von `cdn.jsdelivr.net`, mit SRI-Hash (`integrity`). Neue externe Quellen erfordern Anpassung der CSP **und** der Datenschutzerklärung.
- Header, Navigation und Footer sind auf den Seiten dupliziert. Änderungen (z. B. Footer-Links) in `index.php`, `impressum.php` und `datenschutz.php` gleichzeitig vornehmen.
- Design: Bootstrap 5.3.3 + Bootstrap Icons per CDN, eigene Styles in `assets/homepage.css` (Klassen wie `btn-fw`, `btn-fw-outline`, `legal`, `legal-body`).

## Änderungsprotokoll

Jede inhaltliche Änderung in `CHANGE.md` unter „Unveröffentlicht" eintragen (Hinzugefügt / Geändert / Behoben / Entfernt). Beim Release den Abschnitt mit Datum benennen.

## Datenschutz und Sicherheit

- Die Homepage speichert keine Formulardaten; sie leitet sie nur an die FMS-API weiter. Keine Logs, Cookies oder Tracker hinzufügen, ohne die Datenschutzerklärung anzupassen.
- Den Schlüssel `FMS_ZUGANGSANFRAGE_SCHLUESSEL` niemals committen oder in Ausgaben/Logs schreiben.
- Das Backend (FMS, `zugangsanfrage_api.php`, `sql/zugangsanfragen.sql`) liegt in einem anderen Repository.
