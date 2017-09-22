<<<<<<< HEAD
<?php //$Id: block_profileselectorhtml.php,v 1.2 2012-04-28 10:24:54 vf Exp $
=======
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
 * Version details.
 *
 * @package     block_profileselectorhtml
 * @category    blocks
 * @author      valery fremaux (valery.fremaux@gmail.com)
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
>>>>>>> MOODLE_32_STABLE

class block_profileselectorhtml extends block_base {

    public function init() {
        $this->title = get_string('blockname', 'block_profileselectorhtml');
    }

    public function applicable_formats() {
        return array('all' => true);
    }

    public function specialization() {
        if (isset($this->config->title)) {
            $this->title = format_string($this->config->title) ;
        } else {
            $this->title = format_string(get_string('newhtmlblock', 'block_profileselectorhtml'));
        }
    }

    public function instance_allow_multiple() {
        return true;
    }

    public function content_is_trusted() {
        global $SCRIPT;

        if (!$context = context::instance_by_id($this->instance->parentcontextid)) {
            return false;
        }

<<<<<<< HEAD
        //find out if this block is on the profile page
=======
        // Find out if this block is on the profile page.
>>>>>>> MOODLE_32_STABLE
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                /*
                 * this is exception - page is completely private, nobody else may see content there
                 * that is why we allow JS here
                 */
                return true;
            } else {
                // No JS on public personal pages, it would be a big security issue.
                return false;
            }
        }
        return true;
    }

<<<<<<< HEAD
    function get_content() {
    	global $USER, $DB, $COURSE, $CFG;
    	
        if ($this->content !== NULL) {
=======
    public function get_content() {
        global $USER, $DB, $COURSE, $CFG;

        if ($this->content !== null) {
>>>>>>> MOODLE_32_STABLE
            return $this->content;
        }

        if (!isloggedin()) {
            return '';
        }

        $filteropt = new stdClass;
        $filteropt->overflowdiv = true;
        if ($this->content_is_trusted()) {
            // Fancy html allowed only on course, category and system blocks.
            $filteropt->noclean = true;
        }
<<<<<<< HEAD
        
=======

>>>>>>> MOODLE_32_STABLE
        $blockcontext = context_block::instance($this->instance->id);
        $coursecontext = context_course::instance($COURSE->id);
        $streditcontent = get_string('editcontent', 'block_profileselectorhtml');

        $this->content = new stdClass;

<<<<<<< HEAD
        $this->config->text_all = file_rewrite_pluginfile_urls(@$this->config->text_all, 'pluginfile.php', $this->context->id, 'block_profileselectorhtml', 'text_all', NULL);
        $this->config->text_nomatch = file_rewrite_pluginfile_urls(@$this->config->text_nomatch, 'pluginfile.php', $this->context->id, 'block_profileselectorhtml', 'text_nomatch', NULL);
        $this->content->text = !empty($this->config->text_all) ? format_text($this->config->text_all, FORMAT_HTML, $filteropt) : '';
      
        //now we add the matching rules text. 
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $COURSE->id, 'blockid' => $this->instance->id));
        
        if($rules){
            $res = false;
            $match_count = 0;
            foreach($rules as $rule){
                $res = self::check_rule_match($rule);
                
                if (@$res){
                    $match_count ++;
                    $rule->text_match = file_rewrite_pluginfile_urls($rule->text_match, 'pluginfile.php', $this->context->id, 'block_profileselectorhtml', 'text_match', $match_count);
	                $this->content->text .= format_text(@$rule->text_match, FORMAT_HTML, $filteropt);
                }                    
            }
			
            if (((has_capability('moodle/course:manageactivities', $coursecontext)) || (has_capability('block/profileselectorhtml:editcontent', $blockcontext) && !@$this->config->lockcontent)) && $match_count > 0){
               $this->content->footer = " <a href=\"{$CFG->wwwroot}/blocks/profileselectorhtml/edit.php?sesskey=".sesskey()."&id={$this->instance->id}&amp;course={$COURSE->id}\">$streditcontent</a>";
            }
            
           	if(!$res) {
                $this->config->text_nomatch = file_rewrite_pluginfile_urls($this->config->text_nomatch, 'pluginfile.php', $this->context->id, 'block_profileselectorhtml', 'text_nomatch', NULL);
    	        $this->content->text .= format_text(@$this->config->text_nomatch, FORMAT_HTML, $filteropt);
            }
=======
        if (!isset($this->config)) {
            $this->config = new StdClass;
        }

        $this->config->text_all = file_rewrite_pluginfile_urls(@$this->config->text_all, 'pluginfile.php', $this->context->id,
                                                               'block_profileselectorhtml', 'text_all', null);
        $this->config->text_nomatch = file_rewrite_pluginfile_urls(@$this->config->text_nomatch, 'pluginfile.php',
                                                                   $this->context->id, 'block_profileselectorhtml',
                                                                   'text_nomatch', null);
        $this->content->text = !empty($this->config->text_all) ? format_text($this->config->text_all, FORMAT_HTML, $filteropt) : '';

        // Now we add the matching rules text.
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $COURSE->id, 'blockid' => $this->instance->id));

        if ($rules) {
            $res = false;
            $match_count = 0;
            foreach ($rules as $rule) {
                $res = self::check_rule_match($rule);

                if (@$res) {
                    $match_count ++;
                    $rule->text_match = file_rewrite_pluginfile_urls($rule->text_match, 'pluginfile.php', $this->context->id,
                                                                     'block_profileselectorhtml', 'text_match', $match_count);
                    $this->content->text .= format_text(@$rule->text_match, FORMAT_HTML, $filteropt);
                }
            }

            if (((has_capability('moodle/course:manageactivities', $coursecontext)) ||
                    (has_capability('block/profileselectorhtml:editcontent', $blockcontext) &&
                            !@$this->config->lockcontent)) && $match_count > 0) {
               $params = array('sesskey' => sesskey(), 'id' => $this->instance->id, 'course' => $COURSE->id);
               $editcontenturl = new moodle_url('/blocks/profileselectorhtml/edit.php', $params);
               $this->content->footer = ' <a href="'.$editcontenturl.'">'.$streditcontent.'</a>';
            }
            if (!$res) {
                $this->config->text_nomatch = file_rewrite_pluginfile_urls($this->config->text_nomatch, 'pluginfile.php',
                                                                           $this->context->id, 'block_profileselectorhtml',
                                                                           'text_nomatch', null);
                $this->content->text .= format_text(@$this->config->text_nomatch, FORMAT_HTML, $filteropt);
            }
>>>>>>> MOODLE_32_STABLE
        }

        unset($filteropt); // Memory footprint.
        return $this->content;
    }

    /**
     * Serialize and store config data
     */
    public function instance_config_save($data, $nolongerused = false) {
        global $DB, $COURSE;

        $config = clone($data);

<<<<<<< HEAD
        //delete all 
        $DB->delete_records('block_profileselectorhtml_r', array('course' => $COURSE->id, 'blockid' => $this->instance->id));

        //store the rules 

        for($i = 1 ; $i <= optional_param('rc', 1, PARAM_INT) ; $i++){
           	$rule = new stdClass();
           
           	$rulename = 'rulename'.$i;
           	$rule->name = $data->{$rulename};
           
           	$field1 = 'field1_'.$i;
           	$rule->field1 = $data->{$field1};
          
           	$op1 = 'op1_'.$i;
           	$rule->op1 = $data->{$op1};
          
           	$value1 =  'value1_'.$i;
           	$rule->value1 = $data->{$value1};
       
           	$op = 'op'.$i;
           	$rule->operation = $data->{$op};
          
           	$field2 = 'field2_'.$i;
           	$rule->field2 = $data->{$field2};
          
           	$op2 = 'op2_'.$i;
           	$rule->op2 = $data->{$op2};
           
           	$value2 = 'value2_'.$i;
           	$rule->value2 = $data->{$value2};
          
           	$text_match = 'text_match_'.$i;
           	// $rule->text_match = $data->{$text_match}['text'];
			$rule->text_match = file_save_draft_area_files($data->{$text_match}['itemid'], $this->context->id, 'block_profileselectorhtml', 'text_match', $i, array('subdirs' => true), $data->{$text_match}['text']);
           
           	// $rule->course = $_REQUEST['courseid'];
           	$rule->course = $COURSE->id;
           	$rule->blockid = $this->instance->id;
                     
           	$DB->insert_record('block_profileselectorhtml_r', $rule);
        }
                 
        $config->text_all = file_save_draft_area_files($data->text_all['itemid'], $this->context->id, 'block_profileselectorhtml', 'text_all', 0, array('subdirs' => true), $data->text_all['text']);
        $config->format_text_all = (!isset($data->text_all['format'])) ? FORMAT_MOODLE : $data->text_all['format'];

      	// $config->text_match = file_save_draft_area_files($data->text_match['itemid'], $this->context->id, 'block_profileselectorhtml', 'match', 0, array('subdirs'=>true), $data->text_match['text']);
      	// $config->format_match = (!isset($data->text_matched['format'])) ? FORMAT_MOODLE : $data->text_matched['format'];

        $config->text_nomatch = file_save_draft_area_files($data->text_nomatch['itemid'], $this->context->id, 'block_profileselectorhtml', 'text_nomatch', 0, array('subdirs' => true), $data->text_nomatch['text']);
        $config->format_text_nomatch = (!isset($data->text_nomatch['format'])) ? FORMAT_MOODLE : $data->text_nomatch['format'] ;
=======
        // Delete all.
        $DB->delete_records('block_profileselectorhtml_r', array('course' => $COURSE->id, 'blockid' => $this->instance->id));

        // Store the rules.

        for ($i = 1; $i <= optional_param('rc', 1, PARAM_INT); $i++) {

            $rule = new stdClass();

            $rulename = 'rulename'.$i;
            $rule->name = $data->{$rulename};

            if (empty($data->{$rulename})) {
                continue;
            }

            $field1 = 'field1_'.$i;
            $rule->field1 = $data->{$field1};

            $op1 = 'op1_'.$i;
            $rule->op1 = $data->{$op1};

            $value1 =  'value1_'.$i;
            $rule->value1 = $data->{$value1};

            $op = 'op'.$i;
            $rule->operation = $data->{$op};

            $field2 = 'field2_'.$i;
            $rule->field2 = $data->{$field2};

            $op2 = 'op2_'.$i;
            $rule->op2 = $data->{$op2};

            $value2 = 'value2_'.$i;
            $rule->value2 = $data->{$value2};

            $text_match = 'text_match_'.$i;
            $rule->text_match = file_save_draft_area_files($data->{$text_match}['itemid'], $this->context->id,
                                                           'block_profileselectorhtml', 'text_match', $i, array('subdirs' => true),
                                                           $data->{$text_match}['text']);

            $rule->course = $COURSE->id;
            $rule->blockid = $this->instance->id;

            $DB->insert_record('block_profileselectorhtml_r', $rule);
        }

        $config->text_all = file_save_draft_area_files($data->text_all['itemid'], $this->context->id,
                                                       'block_profileselectorhtml', 'text_all', 0,
                                                       array('subdirs' => true), $data->text_all['text']);
        $config->format_text_all = (!isset($data->text_all['format'])) ? FORMAT_MOODLE : $data->text_all['format'];

        $config->text_nomatch = file_save_draft_area_files($data->text_nomatch['itemid'], $this->context->id,
                                                           'block_profileselectorhtml', 'text_nomatch', 0,
                                                           array('subdirs' => true), $data->text_nomatch['text']);
        $config->format_text_nomatch = (!isset($data->text_nomatch['format'])) ? FORMAT_MOODLE : $data->text_nomatch['format'];
>>>>>>> MOODLE_32_STABLE

        parent::instance_config_save($config, $nolongerused);
    }

    /*
     * Hide the title bar when none set..
     */
    public function hide_header() {
        return empty($this->config->title);
    }

<<<<<<< HEAD
	static function check_jquery(){
		global $CFG, $PAGE, $OUTPUT;
	
		if ($CFG->version >= 2013051400) return; // Moodle 2.5 natively loads JQuery

		$current = '1.10.2';
		
		if (empty($OUTPUT->jqueryversion)){
			$OUTPUT->jqueryversion = '1.10.2';
			$PAGE->requires->js('/blocks/profileselectorhtml/js/jquery-'.$current.'.min.js', true);
		} else {
			if ($OUTPUT->jqueryversion < $current){
				debugging('the previously loaded version of jquery is lower than required. This may cause issues to dashboard. Programmers might consider upgrading JQuery version in the component that preloads JQuery library.', DEBUG_DEVELOPER, array('notrace'));
			}
		}		
	}
    
    public function check_rule_match($rule){     
    	global $USER, $DB;

        if (empty($rule->field1) && empty($rule->field2)){
            $this->content->footer = '';
            return;
        }       
                
        if (!empty($rule->field1)){
            if (is_numeric($rule->field1) && $rule->field1 > 0){
=======

    public function check_rule_match($rule) {
        global $USER, $DB;

        if (empty($rule->field1) && empty($rule->field2)) {
            $this->content->footer = '';
            return;
        }
        if (!empty($rule->field1)) {
            if (is_numeric($rule->field1) && $rule->field1 > 0) {
>>>>>>> MOODLE_32_STABLE
                $uservalue = $DB->get_field('user_info_data', 'data', array('fieldid' => $rule->field1, 'userid' => $USER->id)); 
            } else {
                $stduserfield = $rule->field1;
                $uservalue = $USER->$stduserfield;
            }
        }

        if ($rule->op1 == '~=') {
            $expr = "\$res1 = preg_match('/{$rule->value1}/', '{$uservalue}') ;";
<<<<<<< HEAD
        } else {        
            $expr = "\$res1 = '{$uservalue}' {$rule->op1} '{$rule->value1}' ;";
        }
             
        $res = null;  
        @eval($expr);
     
        if (@$rule->operation){

            if (!empty($rule->field2)){
                if (is_numeric($rule->field2) && $rule->field2 > 0){
                    $uservalue = $DB->get_field('user_info_data', 'data', array('fieldid' => $rule->field2, 'userid' => $USER->id)); 
=======
        } else {
            $expr = "\$res1 = '{$uservalue}' {$rule->op1} '{$rule->value1}' ;";
        }

        $res = null;
        @eval($expr);

        if (@$rule->operation) {

            if (!empty($rule->field2)) {
                if (is_numeric($rule->field2) && $rule->field2 > 0) {
                    $params = array('fieldid' => $rule->field2, 'userid' => $USER->id);
                    $uservalue = $DB->get_field('user_info_data', 'data', $params);
>>>>>>> MOODLE_32_STABLE
                } else {
                    $stduserfield = $rule->field2;
                    $uservalue = $USER->$stduserfield;
                }
            }
<<<<<<< HEAD
                        
            if ($rule->op2 == '~='){
                $expr = "\$res2 =(int) preg_match('/{$rule->value2}/', '{$uservalue}'}) ;";
            } else {        
                $expr = "\$res2 ='{$uservalue}' {$rule->op1} '{$rule->value2}' ;";
            }
            @eval($expr);
           
            if (!$res2){
                $res2 = 0;
            }
            
            if (!$res1){
                $res1 = 0;
            }
            
            $finalexpr = "\$res =(bool) ($res1 {$rule->operation} $res2) ;"; 
=======

            if ($rule->op2 == '~=') {
                $expr = "\$res2 =(int) preg_match('/{$rule->value2}/', '{$uservalue}'}) ;";
            } else {
                $expr = "\$res2 ='{$uservalue}' {$rule->op1} '{$rule->value2}' ;";
            }
            @eval($expr);

            if (!@$res2) {
                $res2 = 0;
            }

            if (!@$res1) {
                $res1 = 0;
            }

            $finalexpr = "\$res =(bool) ($res1 {$rule->operation} $res2) ;";
>>>>>>> MOODLE_32_STABLE
            @eval($finalexpr);
        } else {
            $res = @$res1;
        }
<<<<<<< HEAD
                
        return $res;
    }    
}

?>
=======

        return $res;
    }
}
>>>>>>> MOODLE_32_STABLE
