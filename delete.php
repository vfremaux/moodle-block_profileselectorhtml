<?php
  
  require_once('../../config.php');
  
  $delete = required_param('delete',PARAM_INT);
  $blockid = optional_param('bui_editid',null,PARAM_INT);
  $cid = optional_param('id',null,PARAM_INT);


  $course = $DB->get_record('course',array('id'=>$cid));
  
  require_login($course);

  $DB->delete_records('block_profileselectorhtml_r',array('id'=>$delete));
  
  redirect($CFG->wwwroot.'/course/view.php?id='.$cid.'&sesskey='.sesskey().'&bui_editid='.$blockid);
  
  
?>
