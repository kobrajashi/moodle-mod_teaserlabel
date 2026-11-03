# Teaser Label (mod_teaserlabel)

Activity module that shows a **teaser preview with text and an optional medium** on the course page. A
*"Read more"* link leads to the full content on a detail page – meant as a space-saving alternative to a text and
media area when longer content would otherwise clutter the course page.

Without teaser mode, the module behaves like a regular text and media area and shows the full content directly on
the course page.

## Requirements

- **Moodle 5.0 or later** (`$plugin->requires = 2025041400`). Developed against Moodle 5.1.

## Settings per instance

- **Use teaser mode** – switches between a preview with "Read more" and showing everything directly.
- **Teaser text** – the preview on the course page.
- **Teaser media** – an image or a video.
- **Media position** – left, right, top, bottom, center.
- **Media size** – small, medium, large, original.
- **Full content** – shown after clicking "Read more" (or, without teaser mode, directly on the course page).

Rendering uses two Mustache templates (`teaser_view`, `full_view`), so themes can override it.

## Capabilities

- `mod/teaserlabel:addinstance` – add a teaser label
- `mod/teaserlabel:view` – view a teaser label

## Installation

From Moodle 5.1, plugins live in the `public` directory:

```bash
cp -r mod_teaserlabel /path/to/moodle/public/mod/teaserlabel
php admin/cli/upgrade.php
```

On Moodle 5.0 the target is `/path/to/moodle/mod/teaserlabel`. Note that the target directory is called
`teaserlabel`, not `mod_teaserlabel`. Alternatively, install as an administrator via
*Site administration → Notifications*.

## Status

`MATURITY_BETA`, release 1.0.2-beta. Backup/restore is implemented (`backup/`), unit tests live in `tests/`.

## License

GNU GPL v3 or later – see [LICENSE](LICENSE).
