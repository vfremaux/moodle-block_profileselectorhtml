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

$delete = required_param('delete', PARAM_INT);
$blockid = optional_param('bui_editid', null, PARAM_INT);
$cid = optional_param('id', null, PARAM_INT);

if (!$course = $DB->get_record('course', array('id' => $cid))) {
    print_error('coursemisconf');
}

// Security.

$blockcontext = context_block::instance($blockid);
require_login($course);
require_capability('block/profileselectorhtml:editcontent', $blockcontext);

$DB->delete_records('block_profileselectorhtml_r', array('id' => $delete));

redirect(new moodle_url('/course/view.php', array('id' => $cid, 'sesskey' => sesskey(), 'bui_editid' => $blockid)));
