<?php
/**				course_respon.php
 */

  if(!($rbid=='%' and $rcrid!='')){
		$error[]=get_string('selectcourseresponsibility');
		include('scripts/results.php');
		$current=''; 
		$choice='';
		exit;
		}
?>