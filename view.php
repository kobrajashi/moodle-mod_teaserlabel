<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays the full content of a teaserlabel instance.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Tobias Sanders
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/teaserlabel/lib.php');

$id = required_param('id', PARAM_INT); // Course Module ID.

[$course, $cm] = get_course_and_cm_from_cmid($id, 'teaserlabel');
$teaserlabel = $DB->get_record('teaserlabel', ['id' => $cm->instance], '*', MUST_EXIST);

require_course_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/teaserlabel:view', $context);

// Trigger course module viewed event.
$event = \mod_teaserlabel\event\course_module_viewed::create([
    'objectid' => $teaserlabel->id,
    'context' => $context,
]);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('teaserlabel', $teaserlabel);
$event->trigger();

// Mark completion.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Set up page.
$PAGE->set_url('/mod/teaserlabel/view.php', ['id' => $cm->id]);
$PAGE->set_title($teaserlabel->name);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$courseurl = new moodle_url('/course/view.php', ['id' => $course->id]);
echo '<div class="teaserlabel-detail-container">';
echo '<div class="teaserlabel-backlink teaserlabel-backlink-top">';
echo '<a href="' . $courseurl . '">' . get_string('backtocourse', 'mod_teaserlabel') . '</a>';
echo '</div>';

// Display teaser section with same layout as on course page (position + size).
if (!empty($teaserlabel->teasertext)) {
    $teasertext = file_rewrite_pluginfile_urls(
        $teaserlabel->teasertext,
        'pluginfile.php',
        $context->id,
        'mod_teaserlabel',
        'teasertext',
        0
    );

    // Use lightbox=true so images are clickable.
    $mediahtml = teaserlabel_get_teaser_media_html($teaserlabel, $context, true);

    echo '<div class="teaserlabel-teaser teaserlabel-detail teaserlabel-media-' .
        s($teaserlabel->teasermediaposition) . '">';

    if ($mediahtml && in_array($teaserlabel->teasermediaposition, ['left', 'top'])) {
        echo '<div class="teaserlabel-media teaserlabel-size-' .
            s($teaserlabel->teasermediasize) . '">' . $mediahtml . '</div>';
    }

    echo '<div class="teaserlabel-text">' .
        format_text(
            $teasertext,
            $teaserlabel->teasertextformat,
            ['context' => $context, 'noclean' => true]
        ) .
        '</div>';

    if ($mediahtml && in_array($teaserlabel->teasermediaposition, ['right', 'bottom', 'center'])) {
        echo '<div class="teaserlabel-media teaserlabel-size-' .
            s($teaserlabel->teasermediasize) . '">' . $mediahtml . '</div>';
    }

    echo '</div>';
}

// Display full content.
$fullcontent = file_rewrite_pluginfile_urls(
    $teaserlabel->fullcontent,
    'pluginfile.php',
    $context->id,
    'mod_teaserlabel',
    'fullcontent',
    0
);
echo '<div class="teaserlabel-fullcontent">';
// Noclean=true: keep inline SVG and other markup HTML Purifier would strip. Editor fields
// are only writable by users who may edit the activity, as with core activity intros.
echo format_text(
    $fullcontent,
    $teaserlabel->fullcontentformat,
    ['context' => $context, 'noclean' => true]
);
echo '</div>';

// Lightbox modal for teaser image.
echo teaserlabel_get_lightbox_modal_html($teaserlabel, $context);

// Bottom bar: back to course link and print button.
echo '<div class="teaserlabel-backlink teaserlabel-bottombar">';
echo '<a href="' . $courseurl . '">' . get_string('backtocourse', 'mod_teaserlabel') . '</a>';
echo '<button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.teaserlabelPrintPage()">' .
    get_string('printpage', 'mod_teaserlabel') . '</button>';
echo '</div>';

echo '</div>'; // Closes .teaserlabel-detail-container.

// Print script: opens a new window with teaser + content in A4 layout.
echo '<script>
window.teaserlabelPrintPage = function() {
    var teaser = document.querySelector(".teaserlabel-detail-container .teaserlabel-teaser");
    var content = document.querySelector(".teaserlabel-detail-container .teaserlabel-fullcontent");
    var w = window.open("", "_blank");
    if (!w) return;
    var css = "@page { size: A4; margin: 20mm; }"
        + "body { font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;"
        + " font-size: 12pt; line-height: 1.6; color: #333; max-width: 170mm; margin: 0 auto; }"
        + ".teaserlabel-teaser { display: flex; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem; }"
        + ".teaserlabel-media-left { flex-direction: row; }"
        + ".teaserlabel-media-right { flex-direction: row-reverse; }"
        + ".teaserlabel-media-top { flex-direction: column; }"
        + ".teaserlabel-media-bottom { flex-direction: column-reverse; }"
        + ".teaserlabel-media-center { flex-direction: column; align-items: center; text-align: center; }"
        + ".teaserlabel-text { flex: 1; min-width: 200px; }"
        + ".teaserlabel-size-small .teaserlabel-image { max-width: 150px; }"
        + ".teaserlabel-size-medium .teaserlabel-image { max-width: 300px; }"
        + ".teaserlabel-size-large .teaserlabel-image { max-width: 500px; }"
        + ".teaserlabel-size-original .teaserlabel-image { max-width: 100%; }"
        + ".teaserlabel-image, .teaserlabel-video { border-radius: 4px; }"
        + "img { max-width: 100%; height: auto; }"
        + "a { color: inherit; text-decoration: none; pointer-events: none; }";
    w.document.write("<!DOCTYPE html><html><head><title>" + document.title + "</title>"
        + "<style>" + css + "</style></head><body>");
    if (teaser) w.document.write(teaser.outerHTML);
    if (content) w.document.write(content.outerHTML);
    w.document.write("</body></html>");
    w.document.close();
    w.onload = function() { w.print(); };
};
</script>';

echo $OUTPUT->footer();
