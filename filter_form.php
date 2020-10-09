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
 * Form to filter the recent logins report
 *
 * @package   report_recentlogins
 * @copyright 2017 Davo Smith, Synergy Learning
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/report/recentlogins/locallib.php');

/**
 * Class filter_form form to filter the results by date
 * @package report_reportbase
 */
class filter_form extends \moodleform {
    /**
     * Form definition
     * @throws \HTML_QuickForm_Error
     * @throws \coding_exception
     */
    protected function definition() {
        global $DB;
        $mform =& $this->_form;

        $courses = getallcourses();
        if (isset($this->_customdata['course'])) {
            $mform->setDefault('course', $this->_customdata['course']);
            $requesteddata = $this->_customdata['course'];
        }
        $duration = array('' => 'Select', 'lessthanhour' => 'Less Than Hour', 'greaterthanhour' => 'Greater Than Hour');
        $mform->addElement('select', 'course', get_string('course', 'report_recentlogins') , $courses,
        array('style' => 'width:30%'));
        $mform->addElement('select', 'duration', get_string('duration', 'report_recentlogins') , $duration,
        array('style' => 'width:30%'));
        $this->add_action_buttons(false, get_string('report', 'report_recentlogins'));
    }
    public function validation($data, $files) {
        global $CFG, $DB;
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
