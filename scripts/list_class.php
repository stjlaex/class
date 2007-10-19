<?php 
/**												scripts/list_class.php	
 * lists a teacher's classes
 */
	if(!isset($r)){$r=-1;}
	if($r>-1){
		$rbid=$respons[$r]['subject_id'];
		$rcrid=$respons[$r]['course_id'];
		$d_cids=mysql_query("SELECT id FROM class WHERE
		subject_id LIKE '$rbid' AND course_id LIKE '$rcrid' ORDER BY id");
		}
	else{$d_cids=mysql_query("SELECT class_id  FROM tidcid 
				WHERE teacher_id='$tid' ORDER BY class_id");
		 }
	$nocids=mysql_num_rows($d_cids)+1;
	if($nocids>6){$nocids=6;}
?>

<input name="tid" type="hidden" value="<?php print $tid;?>">
<input name="current" type="hidden" value="class_view.php">		
<select name="cids[]" size="<?php print $nocids; ?>"
		  multiple="multiple" onchange="document.classchoice.submit();">
<?php
   while($scids=mysql_fetch_array($d_cids,MYSQL_NUM)){
		print '<option ';
		if(in_array($scids[0], $cids)){print 'selected="selected"';}
		print ' value="'.$scids[0].'">'.$scids[0].'</option>';
		}
?>
</select>
