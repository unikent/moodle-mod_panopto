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
 * Panopto module
 *
 * @package    mod_panopto
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(dirname(__FILE__) . '/../../config.php');
require_once(dirname(__FILE__) . '/lib.php');

$id = optional_param('id', 0, PARAM_INT);

$PAGE->set_url('/mod/panopto/index.php', array(
    'id' => $id
));

if (!$cm = get_coursemodule_from_id('panopto', $id)) {
    print_error('invalidcoursemodule');
}

$course = $DB->get_record('course', array(
    'id' => $cm->course
), '*', MUST_EXIST);

$panoptovideo = $DB->get_record('panopto', array(
    'id' => $cm->instance
), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/panopto:view', $context);

$event = \mod_panopto\event\course_module_viewed::create(array(
    'context' => $context,
    'objectid' => $panoptovideo->id
));
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('panopto', $panoptovideo);
$event->trigger();

// Update 'viewed' state if required by completion system.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/panopto/view.php', array('id' => $cm->id));

echo $OUTPUT->header();
echo $OUTPUT->heading($panoptovideo->name);

echo panopto_get_embed($panoptovideo);

echo $OUTPUT->footer();