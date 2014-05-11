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
 * Define all the backup steps that wll be used by the backup_page_module_block_task
 */

/**
 * Define the complete forum structure for backup, with file and id annotations
 */
class backup_profileselectorhtml_block_structure_step extends backup_block_structure_step {

    protected function define_structure() {
        global $DB;

        // Get the block
        $block = $DB->get_record('block_instances', array('id' => $this->task->get_blockid()));

        // Extract configdata
        $config = unserialize(base64_decode($block->configdata));

        // Define each element separated

        $rules = new backup_nested_element('profilerules');
        $rule = new backup_nested_element('profilerule', array('id'), array('name', 'field1', 'op1', 'value1', 'field2', 'operation', 'op2', 'value2', 'text_match', 'course', 'blockid'));

        // Build the tree

        $rules->add_child($rule);

        // Define sources

		$ruleinstances = $DB->get_records('block_profileselectorhtml_r', array('blockid' => $block->id));
        $rule->set_source_array($ruleinstances);

        // ID Annotations (none)

        // Annotations (files)

        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $this->task->get_courseid(), 'blockid' => $this->task->get_blockid()));

		for ($i = 1 ; $i <= count($rules); $i++){
	        $rule->annotate_files('block_profileselectorhtml', 'text_match', $i); // This file area has one itemid per rule
	    }
        $rule->annotate_files('block_profileselectorhtml', 'text_nomatch', null); // This file area hasn't itemid
        $rule->annotate_files('block_profileselectorhtml', 'text_all', null); // This file area hasn't itemid

        // Return the root element (page_module), wrapped into standard block structure
        return $this->prepare_block_structure($rules);
    }
}
