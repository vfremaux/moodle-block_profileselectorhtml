<?php
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
    }
    //DebugBreak();
    print('$(document).ready(function(){');
   
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
