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
 * The mod_teaserlabel instance creation/editing form.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

/**
 * Module instance settings form.
 */
class mod_teaserlabel_mod_form extends moodleform_mod {

    /**
     * Defines the form fields.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // ---- General section ----
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // ---- Teaser settings section (always visible, directly after name) ----
        $mform->addElement('header', 'teasersettings',
            get_string('teasersettings', 'mod_teaserlabel'));
        $mform->setExpanded('teasersettings', true);

        // Teaser text editor.
        $mform->addElement('editor', 'teasertext_editor',
            get_string('teasertext', 'mod_teaserlabel'),
            ['rows' => 10],
            $this->get_editor_options());
        $mform->addHelpButton('teasertext_editor', 'teasertext', 'mod_teaserlabel');

        // Teaser media file picker.
        $mform->addElement('filemanager', 'teasermedia',
            get_string('teasermedia', 'mod_teaserlabel'),
            null,
            [
                'subdirs' => 0,
                'maxfiles' => 1,
                'accepted_types' => ['image', 'video'],
            ]);
        $mform->addHelpButton('teasermedia', 'teasermedia', 'mod_teaserlabel');

        // Media position.
        $positions = [
            'left' => get_string('position_left', 'mod_teaserlabel'),
            'right' => get_string('position_right', 'mod_teaserlabel'),
            'top' => get_string('position_top', 'mod_teaserlabel'),
            'bottom' => get_string('position_bottom', 'mod_teaserlabel'),
            'center' => get_string('position_center', 'mod_teaserlabel'),
        ];
        $mform->addElement('select', 'teasermediaposition',
            get_string('mediaposition', 'mod_teaserlabel'), $positions);
        $mform->addHelpButton('teasermediaposition', 'mediaposition', 'mod_teaserlabel');
        $mform->setDefault('teasermediaposition', 'left');

        // Media size.
        $sizes = [
            'small' => get_string('size_small', 'mod_teaserlabel'),
            'medium' => get_string('size_medium', 'mod_teaserlabel'),
            'large' => get_string('size_large', 'mod_teaserlabel'),
            'original' => get_string('size_original', 'mod_teaserlabel'),
        ];
        $mform->addElement('select', 'teasermediasize',
            get_string('mediasize', 'mod_teaserlabel'), $sizes);
        $mform->addHelpButton('teasermediasize', 'mediasize', 'mod_teaserlabel');
        $mform->setDefault('teasermediasize', 'medium');

        // ---- Content section ----
        $mform->addElement('header', 'contentsettings',
            get_string('contentsettings', 'mod_teaserlabel'));
        $mform->setExpanded('contentsettings', true);

        $mform->addElement('editor', 'fullcontent_editor',
            get_string('fullcontent', 'mod_teaserlabel'),
            ['rows' => 20],
            $this->get_editor_options());
        $mform->addHelpButton('fullcontent_editor', 'fullcontent', 'mod_teaserlabel');

        // ---- Standard course module elements ----
        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Pre-process form data for display.
     *
     * @param array $defaultvalues Form default values
     */
    public function data_preprocessing(&$defaultvalues) {
        if ($this->current->instance) {
            // Prepare teaser text editor.
            $draftitemid = file_get_submitted_draft_itemid('teasertext_editor');
            $defaultvalues['teasertext_editor']['text'] = file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_teaserlabel',
                'teasertext',
                0,
                $this->get_editor_options(),
                $defaultvalues['teasertext'] ?? ''
            );
            $defaultvalues['teasertext_editor']['format'] = $defaultvalues['teasertextformat'] ?? FORMAT_HTML;
            $defaultvalues['teasertext_editor']['itemid'] = $draftitemid;

            // Prepare teaser media file manager.
            $draftitemid = file_get_submitted_draft_itemid('teasermedia');
            file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_teaserlabel',
                'teasermedia',
                0,
                ['subdirs' => 0, 'maxfiles' => 1]
            );
            $defaultvalues['teasermedia'] = $draftitemid;

            // Prepare full content editor.
            $draftitemid = file_get_submitted_draft_itemid('fullcontent_editor');
            $defaultvalues['fullcontent_editor']['text'] = file_prepare_draft_area(
                $draftitemid,
                $this->context->id,
                'mod_teaserlabel',
                'fullcontent',
                0,
                $this->get_editor_options(),
                $defaultvalues['fullcontent'] ?? ''
            );
            $defaultvalues['fullcontent_editor']['format'] = $defaultvalues['fullcontentformat'] ?? FORMAT_HTML;
            $defaultvalues['fullcontent_editor']['itemid'] = $draftitemid;
        }
    }

    /**
     * Returns the editor options.
     *
     * @return array
     */
    private function get_editor_options(): array {
        return [
            'subdirs' => true,
            'maxfiles' => -1,
            'maxbytes' => 0,
            'context' => $this->context,
        ];
    }
}
