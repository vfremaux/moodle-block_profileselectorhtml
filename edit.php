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
 * @package   block_profileselectorhtml
 * @category  blocks
 * @author    Wafa Adham (admin@adham.ps)
 * @author    Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright 2012 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/blocks/profileselectorhtml/content_edit_form.php');
require_once($CFG->dirroot.'/blocks/profileselectorhtml/block_profileselectorhtml.php');


$id = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);

$blockid = $id;

$PAGE->requires->css('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/skins/dhtmlxaccordion_dhx_web.css');
$PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxcommon.js');
$PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxaccordion.js');
$PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxcontainer.js');

if (!$instance = $DB->get_record('block_instances', array('id' => $id))) {
    print_error('errorbadblockinstance', 'block_editablecontenthtml');
}

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('invalidcourseid');
}

// Security.

require_login($course);

$theblock = block_instance('editablecontenthtml', $instance);

$blockcontext = context_block::instance($id);
$coursecontext = context_course::instance($course->id);
require_capability('block/profileselectorhtml:editcontent', $blockcontext);

$mform = new ProfileSelectorHtmlEditForm();

if ($mform->is_cancelled()) {
    if ($course->id != SITEID) {
        redirect(new moodle_url('/course/view.php', array('id' => $courseid)));
    } else {
        redirect($CFG->wwwroot.'/index.php');
    }
}

if ($data = $mform->get_data()) {
    $theblock = new block_profileselectorhtml();
    $i = 1;
    $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));

    foreach ($rules as $rule) {

        $res = $theblock->check_rule_match($rule);
        if (!$res) {
            continue;
        }

        $tm = 'text_match_'.$i;
        $draftideditor = file_get_submitted_draft_itemid($tm);
        $rule->text_match  = file_save_draft_area_files($draftideditor, $blockcontext->id, 'block_profileselectorhtml',
                                                        'text_match', $i, null, $data->{$tm}['text']);
        $DB->update_record('block_profileselectorhtml_r', $rule);

        $i++;
    }

    if ($courseid != SITEID) {
        redirect(new moodle_url('/course/view.php', array('id' => $course->id)));
    } else {
        redirect($CFG->wwwroot.'/index.php');
    }
}

$PAGE->navbar->add(get_string('pluginname', 'block_profileselectorhtml'), null);
$PAGE->navbar->add(get_string('editcontent', 'block_profileselectorhtml'), null);

$params = array('sesskey' => sesskey(), 'bui_editid' => $blockid, 'course' => $courseid, 'id' => $id);
$url = new moodle_url('/blocks/profileselectorhtml/edit.php', $params);
$PAGE->set_url($url);
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->shortname);
$systemcontext = context_system::instance();

$PAGE->set_context($systemcontext);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('editcontent', 'block_profileselectorhtml'));

$data = new stdClass();
$data->id = $id;
$data->course = $courseid;

// Load the rule.

if (!empty($theblock->config->lockcontent) && !has_capability('moodle/course:manageactivities', $coursecontext)) {
    echo $OUTPUT->box(get_string('contentislocked', 'block_editablecontenthtml'));
    echo '<br/>';
    echo $OUTPUT->continue_button(new moodle_url('/course/view.php', array('id' => $courseid)));
} else {
    $mform->set_data($data);
    $mform->display();
}
echo $OUTPUT->footer($course);
