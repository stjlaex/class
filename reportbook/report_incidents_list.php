<?php
/**									report_incidents_list.php
 *
 *	Finds and lists students identified as having incidents.
 */

$action='report_incidents.php';

$startdate=$_POST['date0'];
$enddate=$_POST['date1'];
if(isset($_POST['bid']) and $_POST['bid']!=''){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['catid'])){
	$category=$_POST['catid'] . ':;';
	}
else{$category='';}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['formid']) and $_POST['formid']!=''){$comid=$_POST['formid'];}
if(isset($_POST['secid']) and $_POST['secid']!=''){$secid=$_POST['secid'];}
elseif(isset($_POST['houseid'])  and $_POST['houseid']!=''){$comid=$_POST['houseid'];}else{$comid='';}

include('scripts/sub_action.php');

	if($comid!=''){
		if($yid!=''){
			$d_incidents=mysql_query("SELECT * FROM incidents WHERE
							incidents.entrydate >= '$startdate' AND incidents.entrydate<='$enddate' 
							AND incidents.subject_id LIKE '$bid' 
							AND incidents.student_id=ANY(SELECT student.id FROM student JOIN comidsid AS a ON comidsid.student_id=student.id
							WHERE student.yeargroup_id='$yid' a.community_id='$comid' 
							AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL));");
			}
		else{
			$d_incidents=mysql_query("SELECT * FROM incidents JOIN
					comidsid AS a ON a.student_id=incidents.student_id WHERE
					a.community_id='$comid' AND (a.leavingdate>'$enddate' OR a.leavingdate='0000-00-00' OR a.leavingdate IS NULL)
					AND incidents.entrydate >= '$startdate' AND incidents.entrydate<='$enddate' 
					AND incidents.subject_id LIKE '$bid' ORDER BY incidents.student_id;");
			}
		}
	elseif($yid!=''){
		$d_incidents=mysql_query("SELECT * FROM incidents JOIN
					student ON student.id=incidents.student_id WHERE
					incidents.entrydate >= '$startdate' AND incidents.entrydate<='$enddate' 
					AND incidents.subject_id LIKE '$bid' AND
					student.yeargroup_id LIKE '$yid' ORDER BY student.surname;");
		}
	elseif($secid!=''){
		if($secid==1){$section=" AND yeargroup.section_id LIKE '%' ";}
		else{$section=" AND yeargroup.section_id='$secid' ";}
		$d_incidents=mysql_query("SELECT * FROM incidents JOIN student 
					ON student.id=incidents.student_id JOIN yeargroup ON yeargroup.id=student.yeargroup_id 
					WHERE incidents.entrydate >= '$startdate' AND incidents.entrydate<='$enddate' 
					AND incidents.subject_id LIKE '$bid' $section ORDER BY student.surname;");
		}
	else{
		if($rcrid=='%'){
			/*User has a subject not a course responsibility selected*/
			$d_course=mysql_query("SELECT DISTINCT cohort.course_id FROM
				cohort JOIN component ON component.course_id=cohort.course_id WHERE
				component.subject_id='$rbid' AND component.id='' AND cohort.stage='$stage' AND cohort.year='$year'");
			$rcrid=mysql_result($d_course,0);
			}
		elseif($rcrid!=''){
			$d_community=mysql_query("SELECT community_id FROM cohidcomid JOIN
				cohort ON cohidcomid.cohort_id=cohort.id WHERE
			    cohort.stage='$stage' AND cohort.year='$year' AND
				cohort.course_id='$rcrid' LIMIT 1");
			$comid=mysql_result($d_community,0);
			$d_incidents=mysql_query("SELECT * FROM incidents JOIN
				comidsid ON comidsid.student_id=incidents.student_id
				WHERE incidents.entrydate > '$startdate' AND  incidents.entrydate<='$enddate' AND comidsid.community_id='$comid'");
			}
		else{
			$d_incidents=mysql_query("SELECT * FROM incidents
				WHERE incidents.entrydate > '$startdate'  AND incidents.entrydate<='$enddate'");
			}
		}

	if(mysql_num_rows($d_incidents)==0){
		$error[]=get_string('nonefound',$book);
		$action='report_incidents.php';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

$extrabuttons=array();
$extrabuttons['previewselected']=array('name'=>'current',
									   'value'=>'report_incidents_print.php',
									   'onclick'=>'checksidsAction(this)');
two_buttonmenu($extrabuttons,$book);
?>

	<div id="viewcontent" class="content">
		<div id="xml-checked-action" style="display:none;">
			<period>
			  <startdate><?php print $startdate;?></startdate>
			  <enddate><?php print $enddate;?></enddate>
			</period>
		</div>
		<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
			<table class="listmenu sidtable" id="sidtable">
				<thead>
					<tr>
		  				<th class="checkall">
							<input type="checkbox" name="checkall" value="yes" onChange="checkAll	(this);" />
		  				</th>
		  				<th colspan="2"><?php print_string('student');?></th>
		  				<th>
		  					<div class="div-sortable no-label">
		  						<?php print_string('formgroup');?>
                                <a href="#" class="sortable"></a>
                            </div>
		  				</th>
		  				<th>
		  					<div class="div-sortable no-label">
		  						<?php print_string('sanction');?>
                                <a href="#" class="sortable"></a>
                            </div>		  					
	  					</th>
		  				<th>
		  					<div class="div-sortable no-label">
		  						<?php print_string('date');?>
                                <a href="#" class="sortable"></a>
                            </div>		  					
	  					</th>
		  				<th>
		  					<div class="div-sortable no-label">
		  						<?php print_string('subject',$book);?>
                                <a href="#" class="sortable"></a>
                            </div>		  					
	  					</th>
		  				<th>
		  					<div class="div-sortable no-label">
		  						<?php print_string('teacher');?>
                                <a href="#" class="sortable"></a>
                            </div>		  					
	  					</th>
					</tr>
					<!--tr>
						<th colspan="3"></th>
						<?php
							$sort_types='';
							for($colno=0;$colno<5;$colno++){
									$sortno=$colno+3;
									$sort_types.=",'s'";
						?>
						<th  class="noprint">
							<div class="rowaction">
								<input class="underrow" type='button' name='action' value='v' onClick='tsDraw("<?php print $sortno;?>A", "sidtable");' />
								<input class="underrow"  type='button' name='action' value='-' onClick='tsDraw("<?php print $sortno;?>U", "sidtable");' />
								<input class="underrow"  type='button' name='action' value='^' onClick='tsDraw("<?php print $sortno;?>D", "sidtable");' />
							</div>
						</th>
						<?php
							}
						?>
						</tr-->
					</thead>
				<tbody>
<?php
					$sids=array();
					list($ratingnames,$catdefs)=fetch_categorydefs('inc');
					while($incident=mysql_fetch_array($d_incidents,MYSQL_ASSOC)){
						$sid=$incident['student_id'];

						$catdefexists=false;
						if($category!=''){
							$currentcatid=trim($incident['category'],':;');
							$catid=trim($category,':;');
							if($catdefs[$catid]['name']==$catdefs[$currentcatid]['name']){$catdefexists=true;}
							}

						if($category=='' or $catdefexists or $incident['category']==$category){
							if(array_key_exists($sid,$sids)){
								$Student=$sids[$sid];
								}
							else{
								$Student=fetchStudent_short($sid);
								$sids[$sid]=$Student;
								}
							if($incident['closed']=='N'){$styleclass=' class="midlite"';}
							else{$styleclass='';}
							$subject=get_subjectname($incident['subject_id']);
							$catid=trim($incident['category'],':;');
							if(array_key_exists($catid,$catdefs)){$sanction=$catdefs[$catid]['name'];}
							else{$sanction='';}
							
?>
					<tr <?php print $styleclass;?>>
						<td>
			  				<input type='checkbox' name='sids[]' value='<?php print $sid; ?>' />
			  			</td>
			  			<td>&nbsp;</td>
			  			<td>
							<a href="infobook.php?current=incidents_list.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>" target="viewinfobook" onclick="parent.viewBook('infobook');"> 
				  				<?php print $Student['DisplayFullName']['value']; ?>
							</a>
		  				</td>
			  			<td>
							<?php print $Student['RegistrationGroup']['value']; ?>
			  			</td>
						<?php
							print '<td>'.$sanction.'</td><td>'.$incident['entrydate'].'</td>';
							print '<td>'.$subject.'</td><td>'.$incident['teacher_id'].'</td>';
						?>
					</tr>
<?php
							}
						}
?>
				</tbody>
			</table>
		</fieldset>
			<input type="hidden" name="choice" value="<?php print $choice;?>" />
			<input type="hidden" name="cancel" value="<?php print $choice;?>" />
			<input type="hidden" name="current" value="<?php print $action;?>" />
		</form>
	</div>
