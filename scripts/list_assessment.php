<?php
/**										scripts/list_assessment.php
 *
 * $multi>1 returns eids[] or $multi=1 returns eid (default=10)
 * set $required='no' to make not required (default=yes)
 * first call returns eid, second call returns eid1
 */

	$cohorts=array();
	$cohids=array();
	if($r>-1){
		if($rcrid=='%'){
			$d_cridbid=mysql_query("SELECT DISTINCT course_id FROM cridbid WHERE
						subject_id='$rbid' ORDER BY course_id"); 
			while($course=mysql_fetch_array($d_cridbid,MYSQL_ASSOC)){
				$cohorts[]=array('id'=>'','course_id'=>$course['course_id'],'stage'=>'%','year'=>'%');
				}
			}
		else{
			$cohorts[]=array('id'=>'','course_id'=>$rcrid,'stage'=>'%','year'=>'%');
			}
		}
	elseif(sizeof($ryids)>0){
   		while(list($index,$ryid)=each($ryids)){
			$comcohorts=(array)list_community_cohorts(array('id'=>'','type'=>'year','name'=>$ryid));
			while(list($index,$cohort)=each($comcohorts)){
				$cohorts[$cohort['id']]=$cohort;
				}
			}
		reset($ryids);
		}
	elseif(sizeof($rfids)>0){
		$cohorts=list_community_cohorts(array('id'=>'','type'=>'form','name'=>$rfids[0]));
		}

	if(!isset($required)){$required='yes';}
	if(!isset($multi)){$multi='10';}
	if(!isset($ieid)){$ieid='';}else{$ieid++;}
?>
	<label for="Assessments"><?php print_string('assessment');?></label>
	<select style="width:30em;" id="Assessments" tabindex="<?php print $tab++;?>"
		<?php if($required=='yes'){ print ' class="required" ';} ?>
		size="<?php print $multi;?>"
		<?php if($multi>1){print ' name="eids'.$ieid.'[]" multiple="multiple"';}
				else{print ' name="eid'.$ieid.'"';}?> >
		<option value=""></option>
<?php
		$eids=array();
   		while(list($index,$cohort)=each($cohorts)){
			$AssDefs=array();
			$AssDefs=(array)fetch_cohortAssessmentDefinitions($cohort);
			while(list($index,$AssDef)=each($AssDefs)){
				if(!array_key_exists($AssDef['id_db'],$eids)){
					$eids[$AssDef['id_db']]=$AssDef['id_db'];
?>
		<option value="<?php print $AssDef['id_db'];?>">
				 <?php print 
					$AssDef['Course']['value']. 
				 ' ('.$AssDef['Stage']['value'].' '.$AssDef['Year']['value'].') '.
					$AssDef['Description']['value'];?>
		</option>
<?php
					}
				}
			}
?>
	</select>
<?php
unset($required);
unset($multi);
unset($cohorts);
unset($eids);
unset($AssDefs);
?>