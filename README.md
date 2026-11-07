# FeuerwehrManagementSystem – Homepage

Eine kompakte, responsive Produkt-Homepage im Design des FeuerwehrManagementSystems. Die Seite verwendet dessen Logo und roten Schaltflächenstil, hat aber keine eigene Datenbank.

## Zugangsanfragen

Das Formular wird serverseitig an den FMS-REST-Endpunkt gesendet. Die Homepage speichert keine Formulardaten. Das FMS speichert die Anfragen in seiner Datenbank; Betreiber sehen und bearbeiten sie im Betreiberportal unter **Zugangsanfragen**. Für eine Missbrauchsbegrenzung speichert das FMS zusätzlich eine mit dem gemeinsamen Schlüssel erzeugte, pseudonymisierte IP-Prüfsumme. Einträge lassen sich im Betreiberportal löschen.

Für den Betrieb PHP mit cURL und HTTPS verwenden. In einer nicht versionierten `config.local.php` im Homepage-Verzeichnis diese Werte setzen:

```php
<?php
define('FMS_ZUGANGSANFRAGE_API_URL', 'https://app.feuerwehrmanagementsystem.de/zugangsanfrage_api.php');
define('FMS_ZUGANGSANFRAGE_SCHLUESSEL', 'HIER_EINEN_64_STELLIGEN_HEX_SCHLUESSEL_EINTRAGEN');
```

In der `config.local.php` des FMS denselben Schlüssel setzen:

```php
<?php
define('FMS_ZUGANGSANFRAGE_SCHLUESSEL', 'HIER_DENSELBEN_64_STELLIGEN_HEX_SCHLUESSEL_EINTRAGEN');
```

Einen Schlüssel einmalig auf einem vertrauenswürdigen Rechner mit PHP erzeugen:

```sh
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```

Danach im FMS einmalig `sql/zugangsanfragen.sql` einspielen. Der Endpunkt akzeptiert nur signierte HTTPS-Anfragen mit engem Zeitfenster; er hat keinen Lesezugriff auf FMS-Daten. Ohne Schlüssel oder Schema weist das FMS die Anfrage sichtbar zurück.

## Lokal ansehen

Mit PHP im Homepage-Verzeichnis starten:

```sh
php -S localhost:8000
```

Zum tatsächlichen Absenden wird eine konfigurierte FMS-API-URL und cURL benötigt. Vor dem öffentlichen Betrieb noch die für den Betreiber geltenden Impressums- und Datenschutzhinweise ergänzen.
