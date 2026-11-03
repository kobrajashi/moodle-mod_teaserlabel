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
 * Restore steps for mod_teaserlabel.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Tobias Sanders
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Structure step to restore one teaserlabel activity.
 */
class restore_teaserlabel_activity_structure_step extends restore_activity_structure_step {
    /**
     * Define the structure to be restored.
     *
     * @return array
     */
    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('teaserlabel', '/activity/teaserlabel');
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the teaserlabel element.
     *
     * @param array $data The data from the backup file
     */
    protected function process_teaserlabel($data) {
        global $DB;

        $data = (object) $data;
        $data->course = $this->get_courseid();
        $data->timemodified = time();

        $newitemid = $DB->insert_record('teaserlabel', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Define post-execution actions.
     */
    protected function after_execute() {
        $this->add_related_files('mod_teaserlabel', 'intro', null);
        $this->add_related_files('mod_teaserlabel', 'teasertext', null);
        $this->add_related_files('mod_teaserlabel', 'teasermedia', null);
        $this->add_related_files('mod_teaserlabel', 'fullcontent', null);
    }
}
