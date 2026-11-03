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
 * Backup task for mod_teaserlabel.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Tobias Sanders
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/teaserlabel/backup/moodle2/backup_teaserlabel_stepslib.php');

/**
 * Provides the steps to perform one complete backup of the teaserlabel activity.
 */
class backup_teaserlabel_activity_task extends backup_activity_task {
    /**
     * No specific settings for this activity.
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Defines backup steps for the teaserlabel activity.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_teaserlabel_activity_structure_step('teaserlabel_structure', 'teaserlabel.xml'));
    }

    /**
     * Encodes URLs to the view.php and index.php scripts.
     *
     * @param string $content Some HTML text to encode
     * @return string The encoded content
     */
    public static function encode_content_links($content) {
        global $CFG;

        $base = preg_quote($CFG->wwwroot, '/');

        // Link to the list of teaserlabels.
        $search = '/(' . $base . '\/mod\/teaserlabel\/index\.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@TEASERLABELINDEX*$2@$', $content);

        // Link to teaserlabel view by module id.
        $search = '/(' . $base . '\/mod\/teaserlabel\/view\.php\?id\=)([0-9]+)/';
        $content = preg_replace($search, '$@TEASERLABELVIEWBYID*$2@$', $content);

        return $content;
    }
}
