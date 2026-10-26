# Teaser Label (mod_teaserlabel)

Aktivitätsmodul, das auf der Kursseite eine **Teaser-Vorschau mit Text und optionalem
Medium** anzeigt. Ein Link *„Weiterlesen"* führt zum vollständigen Inhalt auf einer
Detailseite – gedacht als platzsparende Alternative zum Textfeld, wenn längere Inhalte
die Kursseite sonst überfrachten.

Ohne Teaser-Modus verhält sich das Modul wie ein normales Textfeld und zeigt den
vollständigen Inhalt direkt auf der Kursseite.

## Einstellungen je Instanz

- **Teaser-Modus verwenden** – schaltet zwischen Vorschau mit „Weiterlesen" und
  direkter Vollanzeige.
- **Teaser-Text** – die Vorschau auf der Kursseite.
- **Teaser-Medium** – Bild oder Video.
- **Position des Mediums** – links, rechts, oben, unten, zentriert.
- **Größe des Mediums** – klein, mittel, groß, Originalgröße.
- **Vollständiger Inhalt** – wird nach Klick auf „Weiterlesen" (bzw. ohne Teaser-Modus
  direkt auf der Kursseite) ausgegeben.

Die Darstellung läuft über zwei Mustache-Templates (`teaser_view`, `full_view`), ist
also im Theme überschreibbar.

## Berechtigungen

- `mod/teaserlabel:addinstance` – Teaser Label anlegen
- `mod/teaserlabel:view` – Teaser Label ansehen

## Installation

```bash
cp -r mod_teaserlabel /pfad/zu/moodle/public/mod/teaserlabel
php admin/cli/upgrade.php
```

Ab Moodle 5.1 liegen Plugins im Verzeichnis `public`. Zu beachten: der Zielordner heißt
`teaserlabel`, nicht `mod_teaserlabel`. Alternativ als Administrator/in über
*Website-Administration → Mitteilungen*.

## Status

`MATURITY_BETA`, Release 1.0.1-beta. Backup/Restore ist implementiert (`backup/`),
Unit-Tests liegen unter `tests/`.

## Lizenz

GNU GPL v3 oder später – siehe [LICENSE](LICENSE).
