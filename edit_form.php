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
 * @package   block_profilespecifichtml
 * @copyright 2012 Valery Fremaux (valery.fremaux@gmail.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/profileselectorhtml/block_profileselectorhtml.php');

class block_profileselectorhtml_edit_form extends block_edit_form {

    protected function specific_definition($mform) {
        global $COURSE, $DB, $CFG, $PAGE;

        block_profileselectorhtml::check_jquery();
        $PAGE->requires->css('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/skins/dhtmlxaccordion_dhx_web.css');
        $PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxcommon.js');
        $PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxaccordion.js');
        $PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxcontainer.js');

        $rc = optional_param('rc', null, PARAM_INT);
        $id = $COURSE->id;
        $blockid = optional_param('bui_editid', null, PARAM_INT);

        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $id, 'blockid' => $blockid));
        $rulesarr = array_values($rules);

        if($rc) {
            $rules_count = $rc;
        } else if (count($rules) > 0) {
            $rules_count = count($rules);
            $this->block->config->rulescount = $rules_count;
        } else {
            $rules_count = 1;
            // Unset config.
            unset($this->block->config);
        }

        $PAGE->requires->js('/blocks/profileselectorhtml/js/init.php?rc='.$rc.'&id='.$id.'&bui_editid='.$blockid);

        $mform->addElement('html', '<div id="rules_cont" style="width:100%;"> </div>');

        // Fields for editing HTML block title and contents.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));

        $userfieldcats = $DB->get_records('user_info_category', array(), 'sortorder');

        $fieldoptions = array(
            'username' => get_string('username'),
            'institution' => get_string('institution'),
            'department' => get_string('department'),
            'confirmed' => get_string('confirmed'),
            'city' => get_string('city'),
            'country' => get_string('country'),
            'email' => get_string('email')
        );

        foreach ($userfieldcats as $cat) {
            $fieldoptions = $fieldoptions + $DB->get_records_menu('user_info_field', array('categoryid' => $cat->id), 'sortorder', 'id,name');
        }

        $fieldopoptions['=='] = '=';
        $fieldopoptions['!='] = '!=';
        $fieldopoptions['>'] = '>';
        $fieldopoptions['<'] = '<';
        $fieldopoptions['>='] = '>=';
        $fieldopoptions['<='] = '<=';
        $fieldopoptions['~='] = '~= (like)';

        $clauseopoptions[0] = get_string('minus', 'block_profileselectorhtml');
        $clauseopoptions['&&'] = get_string('and', 'block_profileselectorhtml');
        $clauseopoptions['||'] = get_string('or', 'block_profileselectorhtml');

        $mform->addElement('text', 'config_title', get_string('configtitle', 'block_profileselectorhtml'));
        $mform->setType('config_title', PARAM_MULTILANG);

        $mform->addElement('checkbox', 'config_lockcontent', get_string('configlockcontent', 'block_profileselectorhtml'));
        $mform->setType('config_lockcontent', PARAM_BOOL);

        $button = '<div style="text-align:right;">';
        $params = array('id' => $id, 'sesskey' => sesskey(), 'bui_editid' => $blockid, 'rc' => ($rules_count + 1));
        if ($COURSE->id > SITEID) {
            $newruleurl = new moodle_url('/course/view.php', $params);
        } else if ($PAGE->pagetype == 'my-index') {
            $newruleurl = new moodle_url('/my/index.php', $params);
        } else {
            $newruleurl = new moodle_url('/index.php', $params);
        }

        for ($i = 1; $i <= $rules_count + 1; $i++) {

            $mform->addElement('header', 'configheader'.$i, get_string('rule', 'block_profileselectorhtml').$i);
            $mform->addElement('hidden', 'ruleid_'.$i);
            $mform->setType('ruleid_'.$i, PARAM_RAW); 

            $mform->addElement('text', 'config_rulename'.$i, get_string('configrulename', 'block_profileselectorhtml'));
            $mform->setType('config_rulename'.$i, PARAM_MULTILANG);

            $group1[0] = &$mform->createElement('select', 'config_field1_'.$i, '', $fieldoptions);
            $group1[1] = &$mform->createElement('select', 'config_op1_'.$i, '', $fieldopoptions);
            $group1[2] = &$mform->createElement('text', 'config_value1_'.$i, '', array('size' => 30));
            $mform->setType('config_field1_'.$i, PARAM_RAW);
            $mform->setType( 'config_op1_'.$i, PARAM_RAW);
            $mform->setType('config_value1_'.$i, PARAM_RAW);
            $mform->addGroup($group1, 'group1_'.$i, get_string('configprofilefield1', 'block_profileselectorhtml'), array('&nbsp;'), false);

            $mform->addElement('select', 'config_op'.$i, get_string('configprofileop', 'block_profileselectorhtml'), $clauseopoptions);
            $mform->setType('config_op'.$i, PARAM_RAW); 

            $group2[0] = &$mform->createElement('select', 'config_field2_'.$i, '', $fieldoptions);
            $group2[1] = &$mform->createElement('select', 'config_op2_'.$i, '', $fieldopoptions);
            $group2[2] = &$mform->createElement('text', 'config_value2_'.$i, '', array('size' => 30));
            $mform->setType('config_field2_'.$i, PARAM_RAW); 
            $mform->setType( 'config_op2_'.$i, PARAM_RAW); 
            $mform->setType('config_value2_'.$i, PARAM_RAW); 

            $mform->addGroup($group2, 'group2_'.$i, get_string('configprofilefield2', 'block_profileselectorhtml'), array('&nbsp;'), false);

            $editoroptions = array('maxfiles' => EDITOR_UNLIMITED_FILES, 'noclean' => true, 'text_match' => $this->block->context);
            $mform->addElement('editor', 'config_text_match_'.$i, get_string('configcontentwhenmatch', 'block_profileselectorhtml'), null, $editoroptions);
            $mform->setType('config_text_match_'.$i, PARAM_RAW); // XSS is prevented when printing the block contents and serving files.

            $mform->disabledIf('group1_'.$i, 'config_rulename_'.$i, 'eq', '');
            $mform->disabledIf('group2_'.$i, 'config_rulename_'.$i, 'eq', '');

            if ($i < count($rulesarr) + 1) {
                // Note : You may only delete recorded rules.
                $deletehtml = '<div style="text-align:right;">';
                $label = get_string('delete', 'block_profileselectorhtml');
                $params = array('id' => $id, 'bui_editid' => $blockid, 'sesskey' => sesskey(), 'delete' => $rulesarr[$i - 1]->id);
                $deleteurl = new moodle_url('/blocks/profileselectorhtml/delete.php', $params);
                $deletehtml .= '<a href="'.$deleteurl.'">';
                $deletehtml .= '<input type="button" name="delete" class="btn_del" rule="'.$i.'" value="'.$label.'" />';
                $deletehtml .= '</a>';
                $deletehtml .= '</div>';
                $mform->addElement('html', $deletehtml);
            }
        }

        // Last one will let you add a new slot.
        $label = get_string('add_rule', 'block_profileselectorhtml');
        $button .= '<input type="button" value="'.$label.'" onclick="window.location=\''.$newruleurl.'\'" />';
        $button .= '<div>';
        $mform->addElement('html', $button);

        // Rules count.

        $mform->addElement('hidden','config_rulescount', $rules_count);
        $mform->addElement('hidden', 'rc', $rules_count);
        $mform->addElement('hidden', 'blockid', $blockid);

        $mform->setType('config_rulescount', PARAM_RAW); 
        $mform->setType( 'rc', PARAM_RAW); 
        $mform->setType('blockid', PARAM_RAW); 

        $mform->addElement('header', 'otheroptions', get_string('other_options','block_profileselectorhtml'));
        $mform->addElement('editor', 'config_text_nomatch', get_string('configcontentwhennomatch', 'block_profileselectorhtml'), null, $editoroptions);
        $mform->setType('config_text_nomatch', PARAM_RAW); // XSS is prevented when printing the block contents and serving files

        $mform->addElement('editor', 'config_text_all', get_string('configcontentforall', 'block_profileselectorhtml'), null, $editoroptions);
        $mform->setType('config_text_all', PARAM_RAW); // XSS is prevented when printing the block contents and serving files

    }

    public function set_data($defaults, &$files = null) {
        global $COURSE, $DB;

        $rc = optional_param('rc', null, PARAM_INT);
        $id = optional_param('id', null, PARAM_INT);
        $blockid = optional_param('bui_editid', null, PARAM_INT);

        if (!isset($this->block->config)) {
            $this->block->config = new StdClass;
            $this->block->config->text_all = '';
            $this->block->config->text_nomatch = '';
        }

        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $id, 'blockid' => $blockid));
        if ($rc) {
               $this->block->config->rulescount = $rc;
        } else if (count($rules) > 0) {
               $this->block->config->rulescount = count($rules);
        } else {
             $this->block->config->rulescount = 1;
        }

        $text_all = '';
        $text_match = '';
        $text_nomatch = '';

        // Handle now (text_all , text_notmatch) they are stored in the config.
        if (!empty($this->block->config) && is_object($this->block->config)) {

            // Draft file handling for all.
            $text_all = $this->block->config->text_all;
            $draftid_editor = file_get_submitted_draft_itemid('config_text_all');
            if (empty($text_all)) {
                $currenttext = '';
            } else {
                $currenttext = $text_all;
            }

            $defaults->config_text_all['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_profileselectorhtml', 'text_all', 0, array('subdirs' => true), $currenttext);
            $defaults->config_text_all['itemid'] = $draftid_editor;
            $defaults->config_text_all['format'] = @$this->block->config->format;

            // Draft file handling for no matching.
            $text_nomatch = $this->block->config->text_nomatch;
            $draftid_editor = file_get_submitted_draft_itemid('config_text_nomatch');
            if (empty($text_nomatch)) {
                $currenttext = '';
            } else {
                $currenttext = $text_nomatch;
            }
            $defaults->config_text_nomatch['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_profileselectorhtml', 'text_nomatch', 0, array('subdirs' => true), $currenttext);
            $defaults->config_text_nomatch['itemid'] = $draftid_editor;
            $defaults->config_text_nomatch['format'] = @$this->block->config->format;

            if (!$this->block->user_can_edit() && !empty($this->block->config->title)) {
                // If a title has been set but the user cannot edit it format it nicely.
                $title = $this->block->config->title;
                $defaults->config_title = format_string($title, true, $this->page->context);
                // Remove the title from the config so that parent::set_data doesn't set it.
                unset($this->block->config->title);
            }
        }

        // Draft file handling for matching (all rules).
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $id, 'blockid' => $this->block->instance->id));

        if ($rules) {

            $i = 1;

            foreach ($rules as $rule) {
                $text_match = $rule->text_match;
                $draftid_editor = file_get_submitted_draft_itemid('config_text_match_'.$i);
                if (empty($text_match)) {
                    $currenttext = '';
                } else {
                    $currenttext = $text_match;
                }
                $ctm = 'config_text_match_'.$i;
                $defaults->{$ctm}['text'] = file_prepare_draft_area($draftid_editor, $this->block->context->id, 'block_profileselectorhtml', 'text_match', $i, array('subdirs' => true), $currenttext);
                $defaults->{$ctm}['itemid'] = $draftid_editor;
                $defaults->{$ctm}['format'] = @$this->block->config->format;

                $tm = "text_match_".$i;

                $this->block->config->{$tm}['text'] = $text_match;

                $rulename = "rulename".$i;
                $defaults->{$rulename} = $rule->name;
                $this->block->config->{$rulename} = $rule->name;

                $field1 = "field1_".$i;
                $defaults->{$field1} = $rule->field1;
                $this->block->config->{$field1} = $rule->field1;

                $op1 = "op1_".$i;
                $defaults->{$op1} = $rule->op1;
                $this->block->config->{$op1} = $rule->op1;

                $value1 = "value1_".$i;
                $defaults->{$value1} = $rule->value1;
                $this->block->config->{$value1} = $rule->value1;

                $op = "op".$i;
                $defaults->{$op}=$rule->operation;
                $this->block->config->{$op} = $rule->operation;

                $field2= "field2_".$i;
                $defaults->{$field2}=$rule->field2;
                $this->block->config->{$field2} = $rule->field2;
 
                $op2 = "op2_".$i;
                $defaults->{$op2}=$rule->op2;
                $this->block->config->{$op2} = $rule->op2;

                $value2 = "value2_".$i;
                $defaults->{$value2} = $rule->value2;
                $this->block->config->{$value2} = $rule->value2 ;

                $ruleid = "ruleid_".$i;
                $defaults->{$ruleid} = $rule->id;
                $this->block->config->{$ruleid} = $rule->id;

                $i++;

                unset($this->block->config->$tm);
            }
        }

        /*
         * have to delete text here, otherwise parent::set_data will empty content.
         * of editor
         */
        unset($this->block->config->text_all);
        unset($this->block->config->text_nomatch);
        parent::set_data($defaults);

        // Restore $text in each.
        $this->block->config = new StdClass;
        $this->block->config->text_all = $text_all;
        $this->block->config->text_nomatch = $text_nomatch;

        if (isset($title)) {
            // Reset the preserved title.
            $this->block->config->title = $title;
        }
    }
}
