# Änderungsprotokoll

Alle nennenswerten Änderungen an der Homepage werden hier festgehalten. Neueste Einträge zuerst, Datumsformat `JJJJ-MM-TT`.

## Unveröffentlicht

### Hinzugefügt
- Suchmaschinenoptimierung (#11): `robots.txt` und `sitemap.xml` (dynamisch über `robots.php`/`sitemap.php`), Canonical-Links, Open-Graph-/Twitter-Metadaten, JSON-LD (WebSite, SoftwareApplication), `theme-color`, Apple-Touch-Icon und Logo-Alt-Text. Neue optionale Konstante `FMS_HOMEPAGE_URL`.
- Impressum (`impressum.php`) mit Betreiberangaben aus der `config.local.php`; Footer-Links auf Startseite und Datenschutzseite.
- Optionale Konstante `FMS_BETREIBER_TELEFON` für das Impressum.
- `.htaccess`: URLs ohne `.php`, Sperrung von `config.local*.php`, `*.md` und versteckten Dateien (`.git` usw.), keine Verzeichnisauflistung.
- `CLAUDE.md` (Projekthinweise) und dieses Änderungsprotokoll.

## 2026-11-13

### Geändert
- Datenschutzerklärung (DSGVO) umgesetzt und überarbeitet; Button zum Mitgliederportal ergänzt.

## 2026-11-12

### Geändert
- Divera entfernt, Spendenhinweis ergänzt, Dark Mode eingeführt, Menü schließt sich automatisch, kundenspezifische Implementierungen aufgenommen.

## 2026-11-11

### Geändert
- Funktionen aktualisiert, Historie als Timeline dargestellt.

## 2026-11-07

### Hinzugefügt
- Responsive Homepage im FMS-Design (Initialversion), überarbeitetes Branding und Feature-Highlights.
