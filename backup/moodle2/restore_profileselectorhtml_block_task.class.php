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
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/blocks/profileselectorhtml/backup/moodle2/restore_profileselectorhtml_stepslib.php');

require_once 'restore_profileselectorhtml_stepslib.php';

/**
 * Specialised restore task for the html block
 * (requires encode_content_links in some configdata attrs)
 *
 * TODO: Finish phpdocs
 */
class restore_profileselectorhtml_block_task extends restore_block_task {

    protected function define_my_settings() {
    }

    protected function define_my_steps() {
<<<<<<< HEAD
        $this->add_step(new restore_profileselectorhtml_block_structure_step('profileselectorhtml_structure', 'profileselectorhtml.xml'));
=======
        $step = new restore_profileselectorhtml_block_structure_step('profileselectorhtml_structure', 'profileselectorhtml.xml');
        $this->add_step($step);
>>>>>>> MOODLE_32_STABLE
    }

    public function get_fileareas() {
        return array('text_nomatch', 'text_all', 'text_match');
    }

    public function get_configdata_encoded_attributes() {
<<<<<<< HEAD
        return array('text_nomatch', 'text_all'); // We need to encode some attrs in configdata
=======
        return array('text_nomatch', 'text_all'); // We need to encode some attrs in configdata.
>>>>>>> MOODLE_32_STABLE
    }

    static public function define_decode_contents() {
        $contents = array();
<<<<<<< HEAD

        $contents[] = new restore_profileselectorhtml_block_decode_content('block_instances', 'configdata');

=======
        $contents[] = new restore_profileselectorhtml_block_decode_content('block_instances', 'configdata');
>>>>>>> MOODLE_32_STABLE
        return $contents;
    }

    static public function define_decode_rules() {
        return array();
    }
}

/**
 * Specialised restore_decode_content provider that unserializes the configdata
 * field, to serve the configdata->text content to the restore_decode_processor
 * packaging it back to its serialized form after process
 */
class restore_profileselectorhtml_block_decode_content extends restore_decode_content {

    // Temp storage for unserialized configdata.
    protected $configdata;

    protected function get_iterator() {
        global $DB;

        // Build the SQL dynamically here
        $fieldslist = 't.' . implode(', t.', $this->fields);
        $sql = "SELECT t.id, $fieldslist
                  FROM {" . $this->tablename . "} t
                  JOIN {backup_ids_temp} b ON b.newitemid = t.id
                 WHERE b.backupid = ?
                   AND b.itemname = ?
                   AND t.blockname = 'html'";
        $params = array($this->restoreid, $this->mapping);
        return ($DB->get_recordset_sql($sql, $params));
    }

    protected function preprocess_field($field) {
        $this->configdata = unserialize(base64_decode($field));
        return isset($this->configdata->text) ? $this->configdata->text : '';
    }

    protected function postprocess_field($field) {
        $this->configdata->text = $field;
        return base64_encode(serialize($this->configdata));
    }
}
