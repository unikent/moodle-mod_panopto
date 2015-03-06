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
 * @package    mod_panopto
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the backup steps that will be used by the backup_panopto_activity_task
 */
class backup_panopto_activity_structure_step extends backup_activity_structure_step
{
    protected function define_structure() {
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $panopto = new backup_nested_element('panopto', array('id'), array(
            'name',
            'externalid',
            'intro',
            'introformat',
            'timemodified'
        ));

        // Define sources.
        $panopto->set_source_table('panopto', array('id' => backup::VAR_ACTIVITYID));

        // Return the root element (panopto), wrapped into standard activity structure.
        return $this->prepare_activity_structure($panopto);
    }
}
