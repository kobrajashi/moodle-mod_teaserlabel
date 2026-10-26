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
 * Restore task for mod_teaserlabel.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot . '/mod/teaserlabel/backup/moodle2/restore_teaserlabel_stepslib.php');

/**
 * Provides the steps to restore one teaserlabel activity.
 */
class restore_teaserlabel_activity_task extends restore_activity_task {

    /**
     * No specific settings for this activity.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define the restore steps.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_teaserlabel_activity_structure_step('teaserlabel_structure', 'teaserlabel.xml'));
    }

    /**
     * Define the contents in the activity that must be decoded.
     *
     * @return array
     */
    public static function define_decode_contents() {
        $contents = [];
        $contents[] = new restore_decode_content('teaserlabel', ['intro', 'teasertext', 'fullcontent'], 'teaserlabel');
        return $contents;
    }

    /**
     * Define the decode rules for restoring links.
     *
     * @return array
     */
    public static function define_decode_rules() {
        $rules = [];
        $rules[] = new restore_decode_rule('TEASERLABELVIEWBYID', '/mod/teaserlabel/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('TEASERLABELINDEX', '/mod/teaserlabel/index.php?id=$1', 'course');
        return $rules;
    }
}
