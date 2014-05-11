<?php 

class block_profileselectorhtml extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_profileselectorhtml');
    }

    function applicable_formats() {
        return array('all' => true);
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(get_string('newhtmlblock', 'block_profileselectorhtml'));
    }

    function instance_allow_multiple() {
        return true;
    }

    function content_is_trusted() {
        global $SCRIPT;

        if (!$context = get_context_instance_by_id($this->instance->parentcontextid)) {
            return false;
        }

        //find out if this block is on the profile page
        if ($context->contextlevel == CONTEXT_USER) {
            if ($SCRIPT === '/my/index.php') {
                // this is exception - page is completely private, nobody else may see content there
                // that is why we allow JS here
                return true;
            } else {
                // no JS on public personal pages, it would be a big security issue
                return false;
            }
        }
        return true;
    }

    function get_content() {
    	global $USER, $DB, $COURSE, $CFG;
    	
        if ($this->content !== NULL) {
            return $this->content;
        }
        
        if (!isloggedin()){
        	return '';
        }

        $filteropt = new stdClass;
        $filteropt->overflowdiv = true;
        if ($this->content_is_trusted()) {
            // fancy html allowed only on course, category and system blocks.
            $filteropt->noclean = true;
        }
        
        $blockcontext = context_block::instance($this->instance->id);
        $coursecontext = context_course::instance($COURSE->id);
        $streditcontent = get_string('editcontent', 'block_profileselectorhtml');
        
        $this->content = new stdClass;
        
        if (!isset($this->config)) $this->config = new StdClass;

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
        }

        unset($filteropt); // memory footprint
        return $this->content;
    }

    /**
     * Serialize and store config data
     */
    function instance_config_save($data, $nolongerused = false) {
        global $DB, $COURSE;

        $config = clone($data);

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

        parent::instance_config_save($config, $nolongerused);
    }

    /*
     * Hide the title bar when none set..
     */
    function hide_header(){
        return empty($this->config->title);
    }

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
                $uservalue = $DB->get_field('user_info_data', 'data', array('fieldid' => $rule->field1, 'userid' => $USER->id)); 
            } else {
                $stduserfield = $rule->field1;
                $uservalue = $USER->$stduserfield;
            }
        }

        if ($rule->op1 == '~=') {
            $expr = "\$res1 = preg_match('/{$rule->value1}/', '{$uservalue}') ;";
        } else {        
            $expr = "\$res1 = '{$uservalue}' {$rule->op1} '{$rule->value1}' ;";
        }
             
        $res = null;  
        @eval($expr);
     
        if (@$rule->operation){

            if (!empty($rule->field2)){
                if (is_numeric($rule->field2) && $rule->field2 > 0){
                    $uservalue = $DB->get_field('user_info_data', 'data', array('fieldid' => $rule->field2, 'userid' => $USER->id)); 
                } else {
                    $stduserfield = $rule->field2;
                    $uservalue = $USER->$stduserfield;
                }
            }
                        
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
            @eval($finalexpr);
        } else {
            $res = @$res1;
        }
                
        return $res;
    }    
}

?>