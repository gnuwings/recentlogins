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

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('locallib.php');
require_once('filter_form.php');

admin_externalpage_setup('reportrecentlogins', '', null, '', array('pagelayout' => 'report'));

$requestedcourse = optional_param('course', 0, PARAM_INT);
$reqduration = optional_param('duration', '', PARAM_TEXT);

if ($requestedcourse) {
    $params['course'] = $requestedcourse;
}
if ($reqduration) {
    $params['duration'] = $reqduration;
}

require_login();

$context = context_system::instance();
$title = get_string('pluginname', 'report_recentlogins');
$heading = $SITE->fullname;
$PAGE->set_url('/report/reportbase/index.php');
$PAGE->set_pagelayout('report');
$PAGE->set_context($context);
$PAGE->set_title($title);
$PAGE->set_heading($heading);
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('recentlogins', 'report_recentlogins'));
if ($requestedcourse > 0) {
    $filterform = new filter_form(null, $params);
} else {
    $filterform = new filter_form(null);
}

$filterform->display();
$searchclauses = array();
$enrolledusers = '';
$table = new html_table();
$table->tablealign = "left";
$table->head  = array(
get_string('firstname', 'report_recentlogins'),
get_string('lastname', 'report_recentlogins'),
get_string('course'),
get_string('duration', 'report_recentlogins'),
get_string('accessed_time', 'report_recentlogins'),
);
$table->colclasses = array('leftalign');
$table->align = array('centre');
$table->width = '50%';
$table->attributes['class'] = 'generaltable';
$table->data = array();
$createrid = 0;
if ($requestedcourse != 0) {
    $sql = "SELECT l.id,l.userid,l.timecreated ,u.firstname,u.lastname, c.fullname FROM {logstore_standard_log}  l
    join {user} u on u.id = l.userid
    join {course} c on c.id=l.courseid
    where l.courseid=$requestedcourse
    ORDER BY l.timecreated DESC";
} else {
    $sql = "SELECT l.id,l.userid,l.timecreated ,u.firstname,u.lastname, c.fullname FROM {logstore_standard_log}  l
    join {user} u on u.id = l.userid
    join {course} c on c.id=l.courseid
    ORDER BY l.timecreated DESC ";
}
$id = 0;
$activities = $DB->get_records_sql($sql);
foreach ($activities as $activity) {
    $duration = get_duration($activity->id, $activity->userid, $activity->timecreated, $reqduration);
    if ($createrid != $activity->userid) {
        $createrid = $activity->userid;
        $row = array();
        $row[] = $activity->firstname;
        $row[] = $activity->lastname;
        $row[] = $activity->fullname;
        $row[] = $duration;
        $row[] = userdate($activity->timecreated);
        $table->data[] = $row;
    }
}
echo html_writer::start_tag('div', array('id' => ''));
echo html_writer::table($table);
echo html_writer::end_tag('div');
echo $OUTPUT->footer();

