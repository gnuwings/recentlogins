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

function getallcourses() {
    global $DB;
    $courses = array('0' => "Select");
    $sql = "SELECT distinct(c.id),fullname FROM {course} c";
    $sql .= " where c.id > 1";
    $courseobj = $DB->get_records_sql($sql);
    if ($courseobj) {
        foreach ($courseobj as $course) {
            $courses[$course->id] = $course->fullname;
        }
    }
    return $courses;
}

function getusername($userid) {
    global $DB;
    $sql = "SELECT firstname,lastname FROM {user} ";
    $sql .= " where id = $userid";
    $userobj = $DB->get_records_sql($sql);
    return $userobj;
}
function get_duration($id, $userid, $timecreated, $duration) {
    global $DB;
    $time = 0;
    $timelogin = $DB->get_record('logstore_standard_log', array('id' => $id));
    $sql = "SELECT * FROM {logstore_standard_log} c";
    $sql .= " WHERE action ='loggedout'
    AND objectid=$userid AND userid=$userid AND timecreated > ".$timecreated.
    " ORDER BY timecreated ASC LIMIT 0,1";
    $timelogout = $DB->get_record_sql($sql);
    if ($timelogout) {
        $time = $timelogout->timecreated - $timelogin->timecreated;
    }
    if ($duration == 'lessthanhour') {
        if ($time < 3600) {
            return format_length($time);
        }
    } else if ($duration == 'greaterthanhour') {
        if ($time > 3600) {
            return format_length($time);
        }
    } else {
        return format_length($time);
    }
}
    /**
     * Formats time based in Moodle function format_time($totalsecs).
     * @param int $totalsecs
     * @return string
     */
function format_length($totalsecs) {
        $totalsecs = abs($totalsecs);

        $str = new stdClass();
        $str->hour = get_string('hour');
        $str->hours = get_string('hours');
        $str->min = get_string('min');
        $str->mins = get_string('mins');
        $str->sec = get_string('sec');
        $str->secs = get_string('secs');

        $hours = floor($totalsecs / HOURSECS);
        $remainder = $totalsecs - ($hours * HOURSECS);
        $mins = floor($remainder / MINSECS);
        $secs = round($remainder - ($mins * MINSECS) , 2);
    if ($secs > 30) {
        $mins = $mins + 1;
    }
        $sm = ($mins == 1) ? $str->min : $str->mins;
        $sh = ($hours == 1) ? $str->hour : $str->hours;
        $ohours = '';
        $omins = '';

    if ($hours) {
        $ohours = $hours . ' ' . $sh;
    }
    if ($mins) {
        $omins = $mins . ' ' . $sm;
    }

    if ($hours) {
        return trim($ohours . ' ' . $omins);
    }
    if ($mins) {
        return trim($omins);
    }

    return "";
}
