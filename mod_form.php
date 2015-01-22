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
 * Add panopto form
 *
 * @package    mod_panopto
 * @copyright  2014 Skylar Kelty <S.Kelty@kent.ac.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_panopto_mod_form extends moodleform_mod
{
    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        $mform->addElement('text', 'name', 'Title', array('size' => '60'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('url', 'externalid', 'Panopto Video ID', array('size' => '60'), array('usefilepicker' => false));
        $mform->setType('externalid', PARAM_URL);
        $mform->addRule('externalid', null, 'required', null, 'client');

        $this->add_intro_editor();
        $this->standard_coursemodule_elements();

        $this->add_action_buttons(true, false, null);
    }
}
