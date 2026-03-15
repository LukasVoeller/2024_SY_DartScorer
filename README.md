# DartScorer

`DartScorer` ist eine Webanwendung zur Verwaltung und Durchführung von Dart-Spielen. Das Projekt basiert auf `Symfony 6.4` im Backend sowie `Vue 3` und `TypeScript` im Frontend. Neben der Spielverwaltung stehen Funktionen für Spieler, Benutzerkonten, Live-Spiele und Spielhistorien zur Verfügung.

## Funktionsumfang

- Anlegen und Verwalten von Spielern
- Erstellen und Starten neuer Dart-Matches
- Verwaltung laufender und abgeschlossener Spiele
- Spielmodi auf Basis von `X01` mit Legs und Sets
- Benutzer- und Rollenverwaltung
- Geschützte Bereiche für Login, Spielansicht und Administration
- JSON-APIs für Spieler, Spiele, Legs, Sets, Scores und Tally-Daten

## Technologie-Stack

- `PHP >= 8.2`
- `Symfony 6.4`
- `Doctrine ORM` und `Doctrine Migrations`
- `MySQL 8.0`
- `Vue 3`
- `TypeScript`
- `Webpack Encore`
- optional `Lando` für die lokale Entwicklungsumgebung
- optional `Mercure` für Echtzeit-Updates

## Projektstruktur

- `src/`: Symfony-Backend mit Controllern, Entities, Repositories und Commands
- `assets/`: Frontend-Code mit Vue-Komponenten und TypeScript-Einstiegspunkten
- `templates/`: Twig-Templates für die Server-seitigen Views
- `public/`: öffentliche Assets, CSS, Einstiegspunkt und statische Dateien
- `migrations/`: Datenbankmigrationen
- `tests/`: PHPUnit-Tests

## Voraussetzungen

Für eine lokale Entwicklung werden mindestens folgende Werkzeuge benötigt:

- `PHP 8.2` oder neuer
- `Composer`
- `Node.js` und `npm`
- `MySQL 8`

Alternativ kann die mitgelieferte `Lando`-Konfiguration verwendet werden.

## Lokales Setup

### Variante 1: Mit Lando

1. Umgebung starten:

```bash
lando start
```

2. PHP-Abhängigkeiten installieren:

```bash
lando composer install
```

3. Node-Abhängigkeiten installieren:

```bash
lando npm:install
```

4. Datenbankmigrationen ausführen:

```bash
lando console doctrine:migrations:migrate
```

5. Frontend für die Entwicklung bauen:

```bash
lando npm:build
```

Optional für laufende Frontend-Änderungen:

```bash
lando npm:watch
```

### Variante 2: Ohne Lando

1. PHP-Abhängigkeiten installieren:

```bash
composer install
```

2. Node-Abhängigkeiten installieren:

```bash
npm install
```

3. Datenbank konfigurieren:

Lege eine passende `.env.local` an und setze dort mindestens `DATABASE_URL`.

Beispiel:

```dotenv
DATABASE_URL="mysql://symfony:symfony@127.0.0.1:3306/symfony?serverVersion=8.0"
```

4. Migrationen ausführen:

```bash
php bin/console doctrine:migrations:migrate
```

5. Frontend bauen:

```bash
npm run dev
```

6. Symfony lokal starten:

```bash
symfony server:start
```

Alternativ kann auch ein eigener Webserver auf das Verzeichnis `public/` zeigen.

## Standarddienste mit Lando

Die vorhandene `Lando`-Konfiguration stellt standardmäßig folgende Dienste bereit:

- `appserver` mit `PHP 8.2`, Apache und Node.js/npm
- `database` mit `MySQL 8.0`
- `mercure` auf Port `3000`

Die Anwendung selbst verwendet `public/` als Webroot.

## Datenbank

Die Datenbankanbindung läuft über `Doctrine`. Migrationen befinden sich im Verzeichnis `migrations/`.

Wichtige Befehle:

```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:migrations:status
```

Mit Lando:

```bash
lando console doctrine:migrations:migrate
lando console doctrine:migrations:status
```

## Benutzer und Rollen

Die Anwendung verwendet Symfony Security mit Formular-Login für Webrouten und JWT-basierte Authentifizierung für `/api`-Endpunkte.

Relevante Rollen im Projekt:

- `ROLE_ADMIN`
- `ROLE_PLAYER`
- `ROLE_ASSOCIATE`

Ein initialer Administrator kann über den vorhandenen Command erstellt werden:

```bash
php bin/console make:admin
```

Mit Lando:

```bash
lando console make:admin
```

Der Command legt aktuell standardmäßig den Benutzer `admin` mit dem Passwort `admin` an. Das sollte nach dem ersten Login angepasst werden.

## Frontend-Build

Verfügbare npm-Skripte:

```bash
npm run dev
npm run watch
npm run build
npm run dev-server
```

Bedeutung:

- `npm run dev`: Development-Build
- `npm run watch`: automatischer Rebuild bei Änderungen
- `npm run build`: Production-Build
- `npm run dev-server`: Encore Dev Server

## Tests und Qualitätssicherung

PHP-Tests können mit `PHPUnit` ausgeführt werden:

```bash
php bin/phpunit
```

Coding-Standards mit `Easy Coding Standard`:

```bash
./vendor/bin/ecs
```

Mit Lando:

```bash
lando ecs
```

## Wichtige Routen

- `/`: Startseite
- `/login`: Login
- `/darts`: geschützter Dart-Bereich
- `/game/new`: neues Spiel anlegen
- `/game/{id}`: laufendes oder abgeschlossenes Spiel anzeigen
- `/player`: Spielerverwaltung
- `/user`: Benutzerverwaltung

Zusätzlich existieren mehrere API-Endpunkte unter `/api`, zum Beispiel für Spieler- und Spieldaten.

## Entwicklungshinweise

- Backend-Logik liegt hauptsächlich in `src/Controller/`, `src/Entity/` und `src/Repository/`
- Vue-Komponenten und Frontend-Logik liegen unter `assets/`
- Styles befinden sich vor allem unter `public/css/`
- Für produktive Deployments sollten Zugangsdaten, Secrets und Standardpasswörter vor dem Einsatz angepasst werden

## Beitrag und Pflege

Wenn du Änderungen an diesem Projekt vornimmst, achte darauf:

- Datenbankänderungen über Doctrine-Migrationen abzubilden
- Frontend-Änderungen mit einem passenden Build zu prüfen
- bestehende Tests auszuführen oder fehlende Tests sinnvoll zu ergänzen
- Sicherheitsrelevantes wie Rollen, Login-Flows und Standardzugänge besonders sorgfältig zu behandeln
