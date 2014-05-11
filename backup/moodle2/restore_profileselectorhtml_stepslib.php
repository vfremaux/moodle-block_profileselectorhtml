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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2003 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that wll be used by the restore_profileselectorhtml_block_task
 */

/**
 * Define the complete profileselectorhtml structure for restore
 */
class restore_profileselectorhtml_block_structure_step extends restore_structure_step {

    protected function define_structure() {

        $paths = array();

        $paths[] = new restore_path_element('block', '/block', true);
        // $paths[] = new restore_path_element('rules', '/block/rules');
        $paths[] = new restore_path_element('rule', '/block/rules/rule');

        return $paths;
    }

    public function process_block($data) {
        global $DB;

		// nothing to do yet here
    }
    
    /*
    // We cannot do anything with that. 
    public function process_rules($data) {
    }
    */

	/*
	*
	*/
    public function process_rule($data) {
        global $DB;

        $data  = (object) $data;
        $oldid = $data->id;

        $data->course = $this->task->get_courseid();
        $data->blockid = $this->task->get_blockid();

        $ruleid = $DB->insert_record('block_profileselectorhtml_r', $data);
    }

    protected function after_execute() {
        // Add profileselectorhtml related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('block_profileselectorhtml', 'text_match', 'rule');
        $this->add_related_files('block_profileselectorhtml', 'text_nomatch', 'block');
        $this->add_related_files('block_profileselectorhtml', 'text_all', 'block');
    }
}
