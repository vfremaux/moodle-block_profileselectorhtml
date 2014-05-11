<?php

	include_once '../../config.php';
	require_once 'content_edit_form.php';
    require_once $CFG->dirroot.'/blocks/profileselectorhtml/block_profileselectorhtml.php';
    

    $id = required_param('id', PARAM_INT);
    $courseid = required_param('course', PARAM_INT);

    $blockid = $id;  
                           
    $PAGE->requires->css('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/skins/dhtmlxaccordion_dhx_web.css');
    $PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxcommon.js');
    $PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxaccordion.js');
    $PAGE->requires->js('/blocks/profileselectorhtml/js/dhtmlx/dhtmlxAccordion/codebase/dhtmlxcontainer.js');
  
    if (!$instance = $DB->get_record('block_instances', array('id' =>  $id))){
        print_error('errorbadblockinstance', 'block_editablecontenthtml');
    }
    
    if (!$course = $DB->get_record('course', array('id' => $courseid))){
        print_error('invalidcourseid');
    }
   
	require_login($course);
 
    $theBlock = block_instance('editablecontenthtml', $instance);
    $blockcontext = context_block::instance($id);
    $coursecontext = context_course::instance($course->id);

	require_capability('block/profileselectorhtml:editcontent', $blockcontext);

	$mform = new ProfileSelectorHtmlEditForm();
	
	if ($mform->is_cancelled()){
		if ($course->id != SITEID){
			redirect($CFG->wwwroot.'/course/view.php?id='.$courseid);
		} else {
			redirect($CFG->wwwroot.'/index.php');
		}
	}

	if ($data = $mform->get_data()){
	    $theBlock = new block_profileselectorhtml();  
        $i = 1;
        $rules = $DB->get_records('block_profileselectorhtml_r', array('course' => $courseid, 'blockid' => $blockid));
  
        foreach ($rules as $rule){

            $res = $theBlock->check_rule_match($rule);
            if (!$res){
               	continue;
            }

            $tm = 'text_match_'.$i;
            $draftid_editor = file_get_submitted_draft_itemid($tm);
            $rule->text_match  = file_save_draft_area_files($draftid_editor, $blockcontext->id, 'block_profileselectorhtml', 'text_match', $i, null, $data->{$tm}['text']);
            $DB->update_record('block_profileselectorhtml_r', $rule);    

            // $config = file_postupdate_standard_editor($data, 'text_match', $mform->editoroptions, $blockcontext, 'block_profileselectorhtml', 'text_match', $i);
            $i++;        
        }
  
		if ($courseid != SITEID){
			redirect($CFG->wwwroot.'/course/view.php?id='.$course->id);
		} else {
			redirect($CFG->wwwroot.'/index.php');
		}
	}

    $PAGE->navbar->add(get_string('pluginname', 'block_profileselectorhtml'), null);    
    $PAGE->navbar->add(get_string('editcontent', 'block_profileselectorhtml'), null);    
   
    $url = new moodle_url('/blocks/profileselectorhtml/edit.php?sesskey='.sesskey().'&bui_editid='.$blockid.'&course='.$courseid.'&id='.$id);
    $PAGE->set_url($url);
    $PAGE->set_title($SITE->fullname);
    $PAGE->set_heading($SITE->shortname);   
    $system_context = context_system::instance();
    
    $PAGE->set_context($system_context);

    echo $OUTPUT->header();
    echo ($OUTPUT->heading(get_string('editcontent', 'block_editablecontenthtml')));

    $data = new stdClass();
	$data->id = $id;
    $data->course = $courseid;
	//load the rule
    
    if(!empty($theBlock->config->lockcontent) && !has_capability('moodle/course:manageactivities', $coursecontext)){
		echo $OUTPUT->box(get_string('contentislocked', 'block_editablecontenthtml'));
		echo '<br/>';
		echo $OUTPUT->continue_button($CFG->wwwroot.'/course/view.php?id='.$courseid);  	
    } else {
		$mform->set_data($data);
		$mform->display();
	}  
	echo $OUTPUT->footer($course);

?>