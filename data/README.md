# Inhalte pflegen

Alle wiederkehrenden Inhalte stehen in JSON-Dateien in diesem Verzeichnis. Startseite, Eventliste, Talk-Archiv,
Sponsoring-Seite und Kalender (`/events.ics`) werden daraus erzeugt – nichts muss kopiert werden.

Nach einer Änderung lokal prüfen:

```bash
php build.php && php -S 127.0.0.1:8000 -t docs
```

Der Build bricht mit einer verständlichen Fehlermeldung ab, wenn Pflichtfelder fehlen oder ein Talk auf ein
unbekanntes Event verweist.

Danach das neu erzeugte Verzeichnis `docs/` zusammen mit der Änderung committen – GitHub Pages veröffentlicht genau
diesen Ordner. „Nächstes Event“ auf der Startseite springt erst nach einem neuen Build weiter.

Felder mit `markdown` erlauben Markdown (Absätze, Listen, Links, **fett**, *kursiv*). Bilder liegen unter
`static/images/…` und werden mit `/images/…` referenziert.

## `events.json` – Meetups, Stammtische, Developer Days

Eine Liste, neueste Events zuerst. Das Datum ist der eindeutige Schlüssel eines Events.

```json
{
    "date": "2025-12-17",
    "time": "18:30",
    "title": "Meetup III/2025 – X-MAS Special: What's new in PHP 8.5",
    "location": "Tyclipso GmbH, Grundstraße 1, 01326 Dresden",
    "map": "https://maps.app.goo.gl/…",
    "url": "https://www.meetup.com/php-usergroup-dresden/events/…",
    "image": "/images/events/meetups/2025-12-17.webp",
    "description": "markdown",
    "agenda": [
        { "time": "18:30", "title": "Doors Open" },
        { "time": "21:00", "title": "Community + Socializing" }
    ],
    "links": [
        { "title": "Videos auf YouTube", "url": "https://…" }
    ]
}
```

| Feld          | Pflicht | Bedeutung                                                         |
|---------------|---------|-------------------------------------------------------------------|
| `date`        | ja      | `YYYY-MM-DD`, eindeutig                                           |
| `title`       | ja      | Titel des Events                                                  |
| `time`        | nein    | Beginn `HH:MM` (für Kalender, Standard 19:00)                     |
| `location`    | nein    | Ort als Text                                                      |
| `map`         | nein    | Link zur Karte                                                    |
| `url`         | nein    | Anmeldung (z. B. meetup.com) – wird als „Platz sichern“ angezeigt |
| `image`       | nein    | Bild zum Event                                                    |
| `description` | nein    | markdown                                                          |
| `agenda`      | nein    | Programmpunkte ohne Talk; werden mit den Talks nach Zeit sortiert |
| `links`       | nein    | Weiterführende Links (Fotos, Videos, Bericht)                     |

## `talks.json` – Vorträge

Jeder Talk verweist über `event` auf das Datum eines Events.

```json
{
    "event": "2025-12-17",
    "time": "19:45",
    "title": "What's new in PHP 8.5",
    "speaker": "Jane Doe",
    "speakerUrl": "https://phpc.social/@jane",
    "format": "Main Talk",
    "language": "Englisch",
    "abstract": "markdown",
    "links": [
        { "title": "Folien", "url": "/downloads/meetups/2025-12-17-php85.pdf" }
    ]
}
```

| Feld         | Pflicht | Bedeutung                                                 |
|--------------|---------|-----------------------------------------------------------|
| `event`      | ja      | Datum des Events aus `events.json`                        |
| `title`      | ja      | Titel                                                     |
| `speaker`    | nein    | Name(n), mehrere mit „&“ trennen                          |
| `speakerUrl` | nein    | Profil-Link                                               |
| `time`       | nein    | `HH:MM` – Position im Programm                            |
| `format`     | nein    | z. B. „Main Talk“, „Lightning Talk“, „Workshop“           |
| `language`   | nein    | z. B. „Deutsch“, „Englisch“                               |
| `abstract`   | nein    | markdown                                                  |
| `links`      | nein    | Folien, Video, Code                                       |

## `sponsors.json` – aktuelle Sponsoren

Reihenfolge der Liste = Reihenfolge auf der Webseite. Ehemalige Sponsoren einfach entfernen.

```json
{
    "name": "move elevator GmbH",
    "url": "https://www.move-elevator.de",
    "logo": "/images/sponsors/move-elevator.png",
    "claim": "we.move:growth",
    "description": "markdown"
}
```

Pflicht: `name`, `url`, `logo`. `claim` und `description` erscheinen auf der Sponsoring-Seite.

## `team.json` – Orga-Team

```json
{ "name": "Jan Männig", "role": "Orgamitglied, Hosts", "image": "/images/orgateam/jmaennig.webp" }
```

Pflicht: `name`, `image`. Optional: `role`.

## `partners.json` – Kooperationen und Community-Partner

```json
{ "name": "Softwerkskammer Sachsen", "url": "https://…", "logo": "/images/softwerkskammer.png", "kind": "community" }
```

`kind` ist `community` oder `cooperation`. Pflicht: alle vier Felder.

## `site.json` – Seitenweite Einstellungen

Name, Basis-URL, Social-Media-Links, Hauptnavigation und die Liste der Textseiten aus `content/`.
