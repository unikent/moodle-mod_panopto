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

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course, true);
$PAGE->set_pagelayout('incourse');

$event = \mod_panopto\event\course_module_instance_list_viewed::create(array(
    'context' => context_course::instance($course->id)
));
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/panopto/index.php', array('id' => $course->id));
$PAGE->set_title($course->shortname . ': Panopto');
$PAGE->set_heading($course->fullname);
$PAGE->navbar->add('Panopto');
echo $OUTPUT->header();
echo $OUTPUT->heading('Panopto');

if (!$videos = get_all_instances_in_course('panopto', $course)) {
    notice(get_string('thereareno', 'moodle', 'Panopto'), "$CFG->wwwroot/course/view.php?id=$course->id");
    exit;
}

$usesections = course_format_uses_sections($course->format);

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

$strname = get_string('name');
$strintro = get_string('moduleintro');
if ($usesections) {
    $strsectionname = get_string('sectionname', 'format_'.$course->format);
    $table->head  = array ($strsectionname, $strname, $strintro);
    $table->align = array ('center', 'left', 'left');
} else {
    $table->head  = array ($strlastmodified, $strname, $strintro);
    $table->align = array ('left', 'left', 'left');
}

$modinfo = get_fast_modinfo($course);
$currentsection = '';
foreach ($videos as $video) {
    $cm = $modinfo->cms[$video->coursemodule];
    if ($usesections) {
        $printsection = '';
        if ($video->section !== $currentsection) {
            if ($video->section) {
                $printsection = get_section_name($course, $video->section);
            }
            if ($currentsection !== '') {
                $table->data[] = 'hr';
            }
            $currentsection = $video->section;
        }
    } else {
        $printsection = '<span class="smallinfo">'.userdate($video->timemodified)."</span>";
    }

    $extra = empty($cm->extra) ? '' : $cm->extra;
    $icon = '';
    if (!empty($cm->icon)) {
        // Each url has an icon in 2.0.
        $icon = '<img src="'.$OUTPUT->pix_url($cm->icon).'" class="activityicon" alt="'.get_string('modulename', $cm->modname).'" /> ';
    }

    $class = $video->visible ? '' : 'class="dimmed"'; // Hidden modules are dimmed.
    $table->data[] = array (
        $printsection,
        "<a $class $extra href=\"view.php?id=$cm->id\">".$icon.format_string($video->name)."</a>",
        format_module_intro('url', $video, $cm->id));
}

echo html_writer::table($table);

echo $OUTPUT->footer();
