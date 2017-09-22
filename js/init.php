<?php
<<<<<<< HEAD
    require_once('../../../config.php')  ;
     
    $blockid = optional_param('bui_editid',null,PARAM_INT);
    $id = optional_param('id',null,PARAM_INT);
    $rc = optional_param('rc',null,PARAM_INT);
    
    header("Content-type: text/javascript; charset=utf-8");
  
    $course_context = get_context_instance(CONTEXT_COURSE,$id);     
    require_login();
    $PAGE->set_context($course_context); 
    
    $instance = $DB->get_record('block_instances',array('id' => $blockid)); 
    $block = block_instance('block_profileselectorhtml', $instance);
    
    $rules = $DB->get_records('block_profileselectorhtml_r',array('course'=>$id,'blockid'=>$blockid));
    if (count($rules) > 0){
      $rules_count = count($rules);   
    } else {
      $rules_count = 1;  
=======
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

require('../../../config.php');

$blockid = optional_param('bui_editid',null,PARAM_INT);
$id = optional_param('id',null,PARAM_INT);
$rc = optional_param('rc',null,PARAM_INT);

header("Content-type: text/javascript; charset=utf-8");

$course_context = context_course::instance($id);

require_login();

$PAGE->set_context($course_context); 

$instance = $DB->get_record('block_instances',array('id' => $blockid)); 
$block = block_instance('block_profileselectorhtml', $instance);

$rules = $DB->get_records('block_profileselectorhtml_r',array('course' => $id,'blockid' => $blockid));
if (count($rules) > 0) {
    $rules_count = count($rules);
} else {
    $rules_count = 1;
}

echo '$(document).ready(function(){';

echo 'var dhxAccord = new dhtmlXAccordion("rules_cont",\'dhx_web\');';

if (count($rules) > 0) {
    $i = 1;

    foreach ($rules as $rule) {
        echo 'dhxAccord.addItem("a'.$i.'", "'.$rule->name.'").attachObject("configheader'.$i.'");';
        $i++;
>>>>>>> MOODLE_32_STABLE
    }
} else {
    echo 'dhxAccord.addItem("a1", "New rule").attachObject("configheader1");';
}

// Delete button.

echo '$(\'.btn_del\').click(function(){

var delete_link = "'.$CFG->wwwroot.'/blocks/profileselectorhtml/delete.php?id='.$id.'&sesskey='.sesskey().'&bui_editid='.$blockid.'&delete=";
if(confirm(\''.get_string('confirm_delete','block_profileselectorhtml').'\')){
    var index = $(this).attr(\'rule\');
    var ruleid = $(\'input[name=ruleid_\'+index+\']\').val();
  
    delete_link = delete_link + ruleid;
    window.location = delete_link;
}
   
<<<<<<< HEAD
    print('var dhxAccord = new dhtmlXAccordion("rules_cont",\'dhx_web\');');
	
    if(count($rules) > 0){
        $i = 1;
           
        foreach($rules as $rule){
            print('dhxAccord.addItem("a'.$i.'", "'.$rule->name.'").attachObject("configheader'.$i.'");');
            $i++;
        }
    } else {
		print('dhxAccord.addItem("a1", "New rule").attachObject("configheader1");');
    } 
    
    //delete button.
    print('$(\'.btn_del\').click(function(){
    
    var delete_link = "'.$CFG->wwwroot.'/blocks/profileselectorhtml/delete.php?id='.$id.'&sesskey='.sesskey().'&bui_editid='.$blockid.'&delete=";
    if(confirm(\''.get_string('confirm_delete','block_profileselectorhtml').'\')){
        var index = $(this).attr(\'rule\');
        var ruleid = $(\'input[name=ruleid_\'+index+\']\').val();
      
        delete_link = delete_link + ruleid;
        window.location = delete_link;
    }
       
    });');
    
    print('});');
?>
=======
});';

echo '});';

>>>>>>> MOODLE_32_STABLE
