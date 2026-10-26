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
 * Library of interface functions and constants for mod_teaserlabel.
 *
 * @package    mod_teaserlabel
 * @copyright  2026 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the supported features.
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function teaserlabel_supports($feature) {
    return match ($feature) {
        FEATURE_MOD_INTRO => false,
        FEATURE_NO_VIEW_LINK => false,
        FEATURE_BACKUP_MOODLE2 => true,
        FEATURE_SHOW_DESCRIPTION => false,
        FEATURE_MOD_PURPOSE => MOD_PURPOSE_CONTENT,
        FEATURE_COMPLETION_TRACKS_VIEWS => true,
        default => null,
    };
}

/**
 * Adds a new teaserlabel instance.
 *
 * @param stdClass $data Form data
 * @param mod_teaserlabel_mod_form|null $mform The form instance
 * @return int The id of the newly inserted record
 */
function teaserlabel_add_instance($data, $mform = null) {
    global $DB;

    $data->timemodified = time();
    $data->useteaser = 1;
    $data->id = $DB->insert_record('teaserlabel', $data);

    $context = context_module::instance($data->coursemodule);

    // Save teaser text editor files.
    if (isset($data->teasertext_editor)) {
        $data->teasertext = file_save_draft_area_files(
            $data->teasertext_editor['itemid'],
            $context->id,
            'mod_teaserlabel',
            'teasertext',
            0,
            teaserlabel_get_editor_options($context),
            $data->teasertext_editor['text']
        );
        $data->teasertextformat = $data->teasertext_editor['format'];
    }

    // Save teaser media files.
    if (!empty($data->teasermedia)) {
        file_save_draft_area_files(
            $data->teasermedia,
            $context->id,
            'mod_teaserlabel',
            'teasermedia',
            0,
            ['subdirs' => 0, 'maxfiles' => 1]
        );
    }

    // Save full content editor files.
    if (isset($data->fullcontent_editor)) {
        $data->fullcontent = file_save_draft_area_files(
            $data->fullcontent_editor['itemid'],
            $context->id,
            'mod_teaserlabel',
            'fullcontent',
            0,
            teaserlabel_get_editor_options($context),
            $data->fullcontent_editor['text']
        );
        $data->fullcontentformat = $data->fullcontent_editor['format'];
    }

    $DB->update_record('teaserlabel', $data);

    return $data->id;
}

/**
 * Updates an existing teaserlabel instance.
 *
 * @param stdClass $data Form data
 * @param mod_teaserlabel_mod_form $mform The form instance
 * @return bool Success
 */
function teaserlabel_update_instance($data, $mform) {
    global $DB;

    $data->id = $data->instance;
    $data->timemodified = time();
    $data->useteaser = 1;

    $context = context_module::instance($data->coursemodule);

    // Update teaser text editor files.
    if (isset($data->teasertext_editor)) {
        $data->teasertext = file_save_draft_area_files(
            $data->teasertext_editor['itemid'],
            $context->id,
            'mod_teaserlabel',
            'teasertext',
            0,
            teaserlabel_get_editor_options($context),
            $data->teasertext_editor['text']
        );
        $data->teasertextformat = $data->teasertext_editor['format'];
    }

    // Update teaser media files.
    if (!empty($data->teasermedia)) {
        file_save_draft_area_files(
            $data->teasermedia,
            $context->id,
            'mod_teaserlabel',
            'teasermedia',
            0,
            ['subdirs' => 0, 'maxfiles' => 1]
        );
    }

    // Update full content editor files.
    if (isset($data->fullcontent_editor)) {
        $data->fullcontent = file_save_draft_area_files(
            $data->fullcontent_editor['itemid'],
            $context->id,
            'mod_teaserlabel',
            'fullcontent',
            0,
            teaserlabel_get_editor_options($context),
            $data->fullcontent_editor['text']
        );
        $data->fullcontentformat = $data->fullcontent_editor['format'];
    }

    return $DB->update_record('teaserlabel', $data);
}

/**
 * Deletes a teaserlabel instance.
 *
 * @param int $id Instance ID
 * @return bool Success
 */
function teaserlabel_delete_instance($id) {
    global $DB;

    if (!$DB->get_record('teaserlabel', ['id' => $id])) {
        return false;
    }

    $cm = get_coursemodule_from_instance('teaserlabel', $id);
    if ($cm) {
        $context = context_module::instance($cm->id);
        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'mod_teaserlabel');
    }

    $DB->delete_records('teaserlabel', ['id' => $id]);

    return true;
}

/**
 * Returns information for the course module info on the course page.
 *
 * @param stdClass $coursemodule The course module record
 * @return cached_cm_info|null Info to display, or null
 */
function teaserlabel_get_coursemodule_info($coursemodule) {
    global $DB;

    $teaserlabel = $DB->get_record('teaserlabel', ['id' => $coursemodule->instance],
        'id, name, teasertext, teasertextformat, teasermediaposition, teasermediasize, fullcontent, fullcontentformat');

    if (!$teaserlabel) {
        return null;
    }

    $info = new cached_cm_info();
    $info->name = $teaserlabel->name;

    $context = context_module::instance($coursemodule->id);

    $teasertext = file_rewrite_pluginfile_urls(
        $teaserlabel->teasertext,
        'pluginfile.php',
        $context->id,
        'mod_teaserlabel',
        'teasertext',
        0
    );
    // NOTE: get_coursemodule_info() runs during course modinfo cache build (holding the
    // modinfo lock). Filters may re-enter get_fast_modinfo() and deadlock the lock
    // ("Unable to acquire a lock for caching"), which freezes the whole course. Never run
    // filters here — pass filter=false. Full filtering still happens on view.php.
    // Noclean=true: HTML Purifier drops inline SVG and other markup outside its whitelist,
    // which silently mangles editor content. These fields are only writable by users who
    // may edit the activity — same trust model as format_module_intro() in core.
    $teasertext = format_text($teasertext, $teaserlabel->teasertextformat,
        ['context' => $context, 'filter' => false, 'noclean' => true]);

    $mediahtml = teaserlabel_get_teaser_media_html($teaserlabel, $context);

    $content = '<div class="teaserlabel-teaser teaserlabel-media-' .
        s($teaserlabel->teasermediaposition) . '">';

    if ($mediahtml && in_array($teaserlabel->teasermediaposition, ['left', 'top'])) {
        $content .= '<div class="teaserlabel-media teaserlabel-size-' .
            s($teaserlabel->teasermediasize) . '">' . $mediahtml . '</div>';
    }

    $modalid = 'teaserlabel-modal-' . $coursemodule->id;
    $viewurl = new moodle_url('/mod/teaserlabel/view.php', ['id' => $coursemodule->id]);
    $readmorelink = '<div class="teaserlabel-readmore">' .
        '<a href="' . $viewurl . '" data-bs-toggle="modal" data-bs-target="#' . $modalid . '">' .
        get_string('readmore', 'mod_teaserlabel') . '</a></div>';
    $content .= '<div class="teaserlabel-text">' . $teasertext . $readmorelink . '</div>';

    if ($mediahtml && in_array($teaserlabel->teasermediaposition, ['right', 'bottom', 'center'])) {
        $content .= '<div class="teaserlabel-media teaserlabel-size-' .
            s($teaserlabel->teasermediasize) . '">' . $mediahtml . '</div>';
    }

    $content .= '</div>';

    // Full content for modal.
    $fullcontent = file_rewrite_pluginfile_urls(
        $teaserlabel->fullcontent,
        'pluginfile.php',
        $context->id,
        'mod_teaserlabel',
        'fullcontent',
        0
    );
    // Filter=false: see note above — this runs during modinfo cache build.
    // Noclean=true: see note above.
    $fullcontent = format_text($fullcontent, $teaserlabel->fullcontentformat,
        ['context' => $context, 'filter' => false, 'noclean' => true]);

    // Bootstrap 5 modal with full content.
    $content .= '<div class="modal fade" id="' . $modalid . '" tabindex="-1" aria-hidden="true">' .
        '<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">' .
        '<div class="modal-content">' .
        '<div class="modal-header">' .
        '<h5 class="modal-title">' . s($teaserlabel->name) . '</h5>' .
        '<button type="button" class="btn-close" data-bs-dismiss="modal" ' .
        'aria-label="' . get_string('closebuttontitle', 'moodle') . '"></button>' .
        '</div>' .
        '<div class="modal-body teaserlabel-modal-body">' .
        $fullcontent .
        '</div>' .
        '</div></div></div>';

    // JS: make the activity title link open the modal instead of navigating to view.php.
    $content .= '<script>
(function() {
    function init() {
        var mod = document.getElementById("module-' . $coursemodule->id . '");
        if (!mod) return;
        var link = mod.querySelector("a.aalink, .activityinstance > a");
        if (!link) return;
        link.setAttribute("data-bs-toggle", "modal");
        link.setAttribute("data-bs-target", "#' . $modalid . '");
        link.addEventListener("click", function(e) { e.preventDefault(); });
    }
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", init);
    } else {
        init();
    }
})();
</script>';

    $info->content = $content;

    return $info;
}

/**
 * Returns the teaser media HTML.
 *
 * @param stdClass $teaserlabel The teaserlabel record
 * @param context_module $context The module context
 * @param bool $lightbox Whether to wrap images in a lightbox link
 * @return string HTML for the media element, or empty string
 */
function teaserlabel_get_teaser_media_html($teaserlabel, $context, bool $lightbox = false) {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_teaserlabel', 'teasermedia', 0, 'sortorder', false);

    if (empty($files)) {
        return '';
    }

    $file = reset($files);
    $url = moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename()
    );

    $mimetype = $file->get_mimetype();

    if (str_starts_with($mimetype, 'image/')) {
        $img = '<img src="' . $url . '" alt="" class="teaserlabel-image" />';
        if ($lightbox) {
            return '<a href="#" class="teaserlabel-lightbox-trigger" ' .
                'data-bs-toggle="modal" data-bs-target="#teaserlabel-lightbox-modal">' .
                $img . '</a>';
        }
        return $img;
    } else if (str_starts_with($mimetype, 'video/')) {
        return '<video controls class="teaserlabel-video">' .
            '<source src="' . $url . '" type="' . s($mimetype) . '">' .
            '</video>';
    }

    return '';
}

/**
 * Serves files for the plugin.
 *
 * @param stdClass $course The course object
 * @param stdClass $cm The course module object
 * @param context $context The context
 * @param string $filearea The file area
 * @param array $args Extra arguments
 * @param bool $forcedownload Whether to force download
 * @param array $options Additional options
 * @return bool False if file not found
 */
function teaserlabel_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel != CONTEXT_MODULE) {
        return false;
    }

    require_course_login($course, true, $cm);

    if (!has_capability('mod/teaserlabel:view', $context)) {
        return false;
    }

    $validareas = ['teasermedia', 'teasertext', 'fullcontent'];
    if (!in_array($filearea, $validareas)) {
        return false;
    }

    $fs = get_file_storage();
    $itemid = array_shift($args);
    $filename = array_pop($args);
    $filepath = $args ? '/' . implode('/', $args) . '/' : '/';

    $file = $fs->get_file($context->id, 'mod_teaserlabel', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload, $options);
}

/**
 * Returns the editor options for text areas.
 *
 * @param context $context The context
 * @return array Editor options
 */
function teaserlabel_get_editor_options($context) {
    return [
        'subdirs' => true,
        'maxfiles' => -1,
        'maxbytes' => 0,
        'context' => $context,
    ];
}

/**
 * Returns the Bootstrap 5 modal HTML for the lightbox.
 *
 * @param stdClass $teaserlabel The teaserlabel record
 * @param context_module $context The module context
 * @return string Modal HTML, or empty string if no image
 */
function teaserlabel_get_lightbox_modal_html($teaserlabel, $context) {
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_teaserlabel', 'teasermedia', 0, 'sortorder', false);

    if (empty($files)) {
        return '';
    }

    $file = reset($files);
    if (!str_starts_with($file->get_mimetype(), 'image/')) {
        return '';
    }

    $url = moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename()
    );

    return '<div class="modal fade" id="teaserlabel-lightbox-modal" tabindex="-1" ' .
        'aria-hidden="true">' .
        '<div class="modal-dialog modal-dialog-centered modal-xl">' .
        '<div class="modal-content teaserlabel-lightbox-content">' .
        '<button type="button" class="btn-close btn-close-white teaserlabel-lightbox-close" ' .
        'data-bs-dismiss="modal" aria-label="' . get_string('closebuttontitle', 'moodle') . '"></button>' .
        '<div class="modal-body p-0 text-center">' .
        '<img src="' . $url . '" alt="" class="img-fluid" />' .
        '</div></div></div></div>';
}
