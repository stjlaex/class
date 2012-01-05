<?php
/**										list_stage.php
 * 
 * should only be called when working with a respons
 */

if(!isset($listname)){$listname='stage';}
if(!isset($listlabel)){$listlabel='stage';}
if(!isset($liststyle)){$liststyle='width:12em;';}
if(!isset($required)){$required='yes';}
if(!isset($onchange)){$onchange='no';}

trigger_error('!!!!!!!!!'.$onchange,E_USER_WARNING);
include('scripts/set_list_vars.php');

	if($r>-1){
		$stages=array();
		$stages[]=array('id'=>'%','name'=>get_string('allstages','reportbook'));
		if($rcrid=='%'){
			$d_cridbid=mysql_query("SELECT DISTINCT course_id FROM component WHERE
						subject_id='$rbid' AND id='' ORDER BY course_id;"); 
			while($course=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$extrastages=array();
				$extrastages=(array)list_course_stages($course['course_id']);
				$stages=array_merge($stages,$extrastages);
				}
			}
		else{
			$extrastages=(array)list_course_stages($rcrid);
			$stages=array_merge($stages,$extrastages);
			}
		}
list_select_list($stages,$listoptions,$book);
unset($listoptions);
unset($stages);
?>
