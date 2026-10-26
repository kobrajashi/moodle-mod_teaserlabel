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
 * Lists all teaserlabel instances in a course.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);
require_course_login($course);

$PAGE->set_url('/mod/teaserlabel/index.php', ['id' => $id]);
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_pagelayout('incourse');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('modulenameplural', 'mod_teaserlabel'));

$teaserlabels = get_all_instances_in_course('teaserlabel', $course);

if (empty($teaserlabels)) {
    notice(
        get_string('nonewmodules', 'moodle'),
        new moodle_url('/course/view.php', ['id' => $course->id])
    );
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';
$table->head = [];
$table->align = [];

if ($course->format === 'weeks') {
    $table->head[] = get_string('week');
    $table->align[] = 'center';
} else if ($course->format === 'topics') {
    $table->head[] = get_string('topic');
    $table->align[] = 'center';
}

$table->head[] = get_string('name');
$table->align[] = 'left';

foreach ($teaserlabels as $teaserlabel) {
    $row = [];

    if ($course->format === 'weeks' || $course->format === 'topics') {
        $row[] = $teaserlabel->section;
    }

    $url = new moodle_url('/mod/teaserlabel/view.php', ['id' => $teaserlabel->coursemodule]);
    $name = format_string($teaserlabel->name);

    if (!$teaserlabel->visible) {
        $row[] = html_writer::link($url, $name, ['class' => 'dimmed']);
    } else {
        $row[] = html_writer::link($url, $name);
    }

    $table->data[] = $row;
}

echo html_writer::table($table);
echo $OUTPUT->footer();
