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
 * panopto module version info
 *
 * @package    mod_panopto
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @global object
 * @param object $data
 * @return bool|int
 */
function panopto_add_instance($data) {
    global $DB;

    $data->timemodified = time();
    return $DB->insert_record("panopto", $data);
}

/**
 * Delete instance.
 * 
 * @param int $id
 * @return bool true
 */
function panopto_delete_instance($id) {
    global $DB;

    if (!$obj = $DB->get_record('panopto', array('id' => $id))) {
        return false;
    }

    $DB->delete_records('panopto', array('id' => $id));

    return true;
}

/**
 * Returns an embed method for the given video.
 * 
 * @param  [type] $video [description]
 * @return [type]        [description]
 */
function panopto_get_embed($video) {
    return "<iframe src=\"{$video->url}\" width=\"450\" height=\"300\" frameborder=\"0\"></iframe>";
}

/**
 * List of features supported in URL module.
 * 
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed True if module supports feature, false if not, null if doesn't know
 */
function panopto_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_ARCHETYPE:
        return MOD_ARCHETYPE_RESOURCE;

        case FEATURE_GROUPS:
        case FEATURE_GROUPINGS:
        case FEATURE_GRADE_HAS_GRADE:
        case FEATURE_GRADE_OUTCOMES:
        return false;

        case FEATURE_MOD_INTRO:
        case FEATURE_COMPLETION_TRACKS_VIEWS:
        case FEATURE_BACKUP_MOODLE2:
        case FEATURE_SHOW_DESCRIPTION:
        return true;

        default:
        return null;
    }
}