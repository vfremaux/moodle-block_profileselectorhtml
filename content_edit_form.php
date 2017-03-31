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
 * Form for editing HTML block instances.
 *
 * @package   block_profileselectorhtml
 * @category  blocks
 * @author    Wafa Adham (admin@adham.ps)
 * @author    Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright 2012 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/blocks/profileselectorhtml/block_profileselectorhtml.php');
require_once($CFG->libdir.'/formslib.php');

class ProfileSelectorHtmlEditForm extends moodleform {

    protected function definition() {
        global $COURSE, $DB, $CFG, $PAGE;

        $mform = $this->_form;

        // Check JQuery.
        block_profileselectorhtml::check_jquery();

        $rc = optional_param('rc', null, PARAM_INT);
        $courseid = $COURSE->id;
        $blockid = optional_param('id', null, PARAM_INT);

        $blockcontext = context_block::instance($blockid);
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));

        if ($rc) {
              $rulescount = $rc;
        } else if (count($rules) > 0) {
              $rulescount = count($rules);
        } else {
            // New rule.
              $rulescount = 1;
        }

        $theblock = new block_profileselectorhtml();
        $PAGE->requires->js('/blocks/profileselectorhtml/js/init.php?rc='.$rc.'&id='.$courseid.'&bui_editid='.$blockid);

        $i = 1;

        foreach ($rules as $rule) {

            $res = $theblock->check_rule_match($rule);
            if (!$res) {
                continue;
            }

            $mform->addElement('hidden', 'ruleid_'.$i);
            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true);
            $label = get_string('configcontentwhenmatch', 'block_profileselectorhtml');
            $label .= '<br><div style="font-weight:bold;">'.$rule->name.'</div>';
            $mform->addElement('editor', 'text_match_'.$i, $label, null, $editoroptions);
            $mform->setType('text_match_'.$i, PARAM_RAW); // XSS is prevented when printing the block contents and serving files.
            $i++;
        }

        $mform->addElement('hidden', 'rc', $rulescount);
        $mform->addElement('hidden', 'id', $blockid);
        $mform->addElement('hidden', 'course', $courseid);
        $this->add_action_buttons();
    }

    public function set_data($defaults, &$files = null) {
        global $COURSE, $DB;

        $rc = optional_param('rc', null, PARAM_INT);
        $courseid = optional_param('course', null, PARAM_INT);
        $blockid = optional_param('id', null, PARAM_INT);

        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));
        $blockcontext = context_block::instance($blockid);
        $theblock = new block_profileselectorhtml();

        // Draft file handling for matching (all rules).
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));

        if ($rules) {
            $i = 1;
            foreach ($rules as $rule) {

                $res = $theblock->check_rule_match($rule);
                if (!$res) {
                    continue;
                }

                $textmatch = $rule->text_match;
                $draftideditor = file_get_submitted_draft_itemid('text_match_'.$i);
                if (empty($textmatch)) {
                    $currenttext = '';
                } else {
                    $currenttext = $textmatch;
                }
                $tm = "text_match_".$i;
                $defaults->{$tm}['text'] = file_prepare_draft_area($draftideditor, $blockcontext->id, 'block_profileselectorhtml',
                                                                   'text_match', $i, array('subdirs' => true), $currenttext);
                $defaults->{$tm}['itemid'] = $draftideditor;
                $defaults->{$tm}['format'] = FORMAT_HTML;
                $i++;
            }
        }

        /*
         * have to delete text here, otherwise parent::set_data will empty content
         * of editor
         */
        parent::set_data($defaults);

        if (isset($title)) {
            // Reset the preserved title.
            $this->block->config->title = $title;
        }
    }
}
