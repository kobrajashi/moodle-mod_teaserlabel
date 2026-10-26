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
 * Backup steps for mod_teaserlabel.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete teaserlabel structure for backup.
 */
class backup_teaserlabel_activity_structure_step extends backup_activity_structure_step {

    /**
     * Define the structure to be backed up.
     *
     * @return backup_nested_element
     */
    protected function define_structure() {
        // Define each element.
        $teaserlabel = new backup_nested_element('teaserlabel', ['id'], [
            'name',
            'intro',
            'introformat',
            'useteaser',
            'teasertext',
            'teasertextformat',
            'teasermediatype',
            'teasermediaposition',
            'teasermediasize',
            'fullcontent',
            'fullcontentformat',
            'timemodified',
        ]);

        // Define sources.
        $teaserlabel->set_source_table('teaserlabel', ['id' => backup::VAR_ACTIVITYID]);

        // Define file annotations.
        $teaserlabel->annotate_files('mod_teaserlabel', 'intro', null);
        $teaserlabel->annotate_files('mod_teaserlabel', 'teasertext', null);
        $teaserlabel->annotate_files('mod_teaserlabel', 'teasermedia', null);
        $teaserlabel->annotate_files('mod_teaserlabel', 'fullcontent', null);

        return $this->prepare_activity_structure($teaserlabel);
    }
}
