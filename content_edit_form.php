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

defined('MOODLE_INTERNAL') || die();

/**
 * @package   block_profileselectorhtml
 * @category  blocks
 * @author    Wafa Adham (admin@adham.ps)
 * @author    Valery Fremaux (valery.fremaux@gmail.com)
 * @copyright 2012 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->dirroot.'/blocks/moodleblock.class.php');
require_once($CFG->dirroot.'/blocks/profileselectorhtml/block_profileselectorhtml.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Form for editing HTML block instances.
 *
 * @package   block_profileselectorhtml
 * @copyright 2012 Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @version   Moodle 2.x
 */

class ProfileSelectorHtmlEditForm extends moodleform {

    protected function definition() {
        global $COURSE, $DB, $CFG, $PAGE;
        $mform = $this->_form;

        // Check JQuery.
        block_profileselectorhtml::check_jquery();

        $rc = optional_param('rc', null,PARAM_INT);
        $courseid = $COURSE->id;
        $blockid = optional_param('id', null, PARAM_INT);

        $block_context = context_block::instance($blockid);
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));

        if ($rc) {
              $rules_count = $rc;  
        } elseif (count($rules) > 0) {
              $rules_count = count($rules);
        } else {
            // New rule.
              $rules_count = 1;
        }

        $theBlock = new block_profileselectorhtml();
        $PAGE->requires->js('/blocks/profileselectorhtml/js/init.php?rc='.$rc.'&id='.$courseid.'&bui_editid='.$blockid);

        $i = 1;

        foreach ($rules as $rule) {
            $res =  $theBlock->check_rule_match($rule);
            if (!$res) {
                continue;
            }

            $mform->addElement('hidden', 'ruleid_'.$i);
            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true);
            $mform->addElement('editor', 'text_match_'.$i, get_string('configcontentwhenmatch', 'block_profileselectorhtml').'<br><div style="font-weight:bold;">'.$rule->name."</div>", null, $editoroptions);
            $mform->setType('text_match_'.$i, PARAM_RAW); // XSS is prevented when printing the block contents and serving files

            $i++;
        }

        $mform->addElement('hidden', 'rc', $rules_count);
        $mform->addElement('hidden', 'id', $blockid);
        $mform->addElement('hidden', 'course', $courseid);

        $this->add_action_buttons();
    }

    public function set_data($defaults, &$files = null) {
        global $COURSE, $DB;

        $rc = optional_param('rc',null,PARAM_INT);
        $courseid = optional_param('course',null,PARAM_INT);
        $blockid = optional_param('id',null,PARAM_INT);

        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));
        $block_context = context_block::instance($blockid);
        $theBlock = new block_profileselectorhtml();

        // Draft file handling for matching (all rules).
        // Load rules.
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));

        if ($rules) {
            $i = 1;
            foreach ($rules as $rule) {
                $res =  $theBlock->check_rule_match($rule);
                if (!$res) {
                    continue;
                }

                $text_match = $rule->text_match;
                $draftid_editor = file_get_submitted_draft_itemid('text_match_'.$i);
                if (empty($text_match)) {
                    $currenttext = '';
                } else {
                    $currenttext = $text_match;
                }
                $tm = "text_match_".$i;
                $defaults->{$tm}['text'] = file_prepare_draft_area($draftid_editor, $block_context->id, 'block_profileselectorhtml', 'text_match', $i, array('subdirs' => true), $currenttext);
                $defaults->{$tm}['itemid'] = $draftid_editor;
                $defaults->{$tm}['format'] = FORMAT_HTML;

                $i++;
            }
         }

        // Have to delete text here, otherwise parent::set_data will empty content of editor.
        parent::set_data($defaults);

        if (isset($title)) {
            // Reset the preserved title.
            $this->block->config->title = $title;
        }
    }
}
