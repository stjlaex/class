<?php
/**									student_list.php
 *
 *   	Lists students with MedicalFlag set.
 */

$action='med_student_list.php';
//$choice='med_student_list.php';

include('scripts/sub_action.php');

if($list==''){$list=$_POST['list'];}

$displayfields=array();
$displayfields[]='Gender';
$displayfields[]='DOB';

if(isset($_POST['colno'])){
	$displayfields_no=$_POST['colno'];
	}
else{
	$displayfields_no=2;
	}
for($dindex=0;$dindex < ($displayfields_no);$dindex++){
	if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
	}

if(isset($_POST['extracol']) and $_POST['extracol']=='yes'){
	$displayfields_no++;
	$displayfields[]='';
	}

/* Approximate to saving 40% of table width for fixed columns. */
$displayfields_width=60/$displayfields_no.'%';

if($list=='visit'){$extrabuttons['export']=array('name'=>'current','title'=>'exportvisits','value'=>'meals_export.php');two_buttonmenu($extrabutton,'medbook');}
else{two_buttonmenu();}

	//$sids=array();

	if($medtype!='' or $newyid!=''){
		/*these are the filter vars form the sideoptions*/
		if($medtype!='' and $newyid!=''){
			mysql_query("CREATE TEMPORARY TABLE students
				(SELECT info.student_id FROM info JOIN background
				ON background.student_id=info.student_id WHERE background.type='$medtype'
				AND info.medical='Y' AND info.enrolstatus='C')");
			$d_info=mysql_query("SELECT student_id FROM students JOIN student
				ON student.id=students.student_id WHERE student.yeargroup_id='$newyid';");
			mysql_query('DROP TABLE students;');
			}
		elseif($medtype!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info JOIN background
				ON background.student_id=info.student_id WHERE background.type='$medtype'
				AND info.medical='Y' AND info.enrolstatus='C';");
			}
		elseif($newyid!=''){
			$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE student.yeargroup_id='$newyid'
				AND info.medical='Y' AND info.enrolstatus='C';");
			}
		}
	elseif($list=='all'){
		/*
		 * Just list all if requested.
		 */
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE 
				info.medical='Y' AND info.enrolstatus='C' ORDER BY student.surname;");
		}
	elseif($list=='new'){
		/*
		 * List all new students.  use filter A% to grab
		 * A,AT,ATD,AP,ACP but will exclude rejected, waiting list,
		 * cancelled etc.
		 */
		$d_info=mysql_query("SELECT info.student_id FROM info JOIN student
				ON student.id=info.student_id WHERE 
				info.medical='Y' AND (info.enrolstatus LIKE 'A%') ORDER BY student.surname;");
		}
	elseif($list=='visit'){
		$d_info=mysql_query("SELECT DISTINCT medical_log.student_id FROM medical_log JOIN student
				ON student.id=medical_log.student_id ORDER BY student.surname;");
		}

if(isset($d_info)){
	while($info=mysql_fetch_array($d_info,MYSQL_ASSOC)){
		$sids[]=$info['student_id'];
		}
	}
?>

<div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu sidtable" id="sidtable">
			<thead>
				<tr>
					<th rowspan="2" colspan="1" style="width:1em;">
						<input type="checkbox" name="checkall"  value="yes" onChange="checkAll(this);" />
						<?php print_string('checkall'); ?>
					</th>
					<th rowspan="2" style="border:0;text-align:left;"></th>
					<th rowspan="2" style="width:20%;">
						<?php print_string('student'); ?>
					</th>
					<th rowspan="2" style="width:10%;">
						<?php print_string('formgroup'); ?>
					</th>
<?php

	$d_catdef=mysql_query("SELECT name, subtype FROM categorydef WHERE type='med' AND (rating='1' OR rating='0') ORDER BY rating DESC, name;");
	while($medcat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
		$extra_studentfields['Medical'.$medcat['subtype']]=strtolower($medcat['name']);
		}
	$extra_studentfields['NextReviewDate']='nextreviewdate';
	$EnrolAssDefs=fetch_enrolmentAssessmentDefinitions('','M');
	if(sizeof($EnrolAssDefs)>0){
		foreach($EnrolAssDefs as $AssDef){
			$extra_studentfields['Assessment'.$AssDef['id_db']]=$AssDef['Description']['value'];
			}
		}

	foreach($displayfields as $dno => $displayfield){
?>
					<th style="width:<?php print $displayfields_width;?>;">
                        <div class="div-sortable">
                            <?php include('scripts/list_studentfield.php'); ?>
                            <a href="#" class="sortable"></a>
                        </div>
                    </th>
<?php
		 }
?>
				</tr>
				<!--tr-->
<?php

	foreach($displayfields as $dno => $displayfield){
		$sortno=$dno+4;
		//a=age, d=date, i=integer, s=string
		if($displayfield=='Age'){$sort_types.=",'a'";}
		elseif($displayfield=='DOB'  or $displayfield=='IdExpiryDate' or $displayfield=='LeavingDate' or $displayfield=='EntryDate' or $displayfield=='EnrolmentApplicationDate'){$sort_types.=",'d'";}
		elseif($displayfield=='YearGroup' or $displayfield=='EnrolmentYearGroup' or $displayfield=='EnrolNumber'){$sort_types.=",'i'";}
		else{$sort_types.=",'s'";}
?>
					<!--th class="noprint">
						<div class="rowaction">
							<input class="underrow" type='button' name='action' value='v' onClick='tsDraw("<?php print $sortno;?>A", "sidtable");' />
							<input class="underrow"  type='button' name='action' value='-' onClick='tsDraw("<?php print $sortno;?>U", "sidtable");' />
							<input class="underrow"  type='button' name='action' value='^' onClick='tsDraw("<?php print $sortno;?>D", "sidtable");' />
						</div>
					</th-->
		
<?php
		}
?>
				<!--/tr-->
			</thead>
			<tbody>
<?php
	foreach($sids as $sindex => $sid){
		$display='yes';
		$Student=fetchStudent_short($sid);
		$comment=comment_display($sid);
		/*		$d_senhistory=mysql_query("SELECT id, reviewdate FROM senhistory WHERE 
				student_id='$sid' ORDER BY reviewdate DESC");
		$senhistory=mysql_fetch_array($d_senhistory,MYSQL_ASSOC);
		$Student['NextReviewDate']=array();
		$Student['NextReviewDate']['label']='nextreviewdate';
		$Student['NextReviewDate']['value']=$senhistory['reviewdate'];
		*/

		if($display=='yes'){
?>
				<tr id="sid-<?php print $sid;?>">
					<td>
						<input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
						<?php print $sindex+1;?>
					</td>
					<td>
						<span title="<?php print $comment['body'];?>">
						  <a onclick="parent.viewBook('infobook');" target="viewinfobook" href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>"
							class="<?php print $comment['class'];?>"><span title="" class="fa fa-comment"></span></a> 
						</span>
					</td>
					<td class="student">
                        <?php
                          $choice='';
                          if($list=='all'){$curr='med_view.php';$choice1='med_student_list.php';}
                          elseif($list=='new'){$curr='med_view.php';$choice2='med_student_list.php';}
                          elseif($list=='visit'){$curr='med_view_visits.php';$choice3='med_student_list.php';}
                          else{$curr='med_view.php';}
                        ?>
						<a href="medbook.php?current=<?php print $curr;?>&sid=<?php print $sid;?>">
							<?php print $Student['DisplayFullName']['value']; ?>
						</a>
					</td>
					<td>
						<?php print $Student['RegistrationGroup']['value']; ?>
					</td>
<?php
	reset($displayfields);
	while(list($index,$displayfield)=each($displayfields)){
		if(array_key_exists($displayfield,$Student)){
			print '<td>'.$Student[$displayfield]['value'].'</td>';
			}
		else{
			$field=fetchStudent_singlefield($sid,$displayfield);
			if(substr($displayfield, 0, 10)=="Assessment"){
				$edisplayfield=str_split($displayfield, 10);
				$eid=$edisplayfield[1];
				$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
				if($Assessments[0]['Comment']['value']!=""){
					$extra=$Assessments[0]['Comment']['value'];
					$displayout="<span title='$extra'>".$field[$displayfield]['value']."</span>";
					$field[$displayfield]['value']=$displayout;
					}
				}
			print '<td>'.$field[$displayfield]['value'].'</td>';
			}
		}
?>
				</tr>
<?php
				  }
		}
	/* Must unset or the host page thinks a single sid is being displayed*/
	unset($sid);
?>
			</tbody>
			<tfoot class="noprint">
				<tr>
					<th colspan="<?php print $displayfields_no+3;?>">&nbsp;</th>
					<th>
						<div class="rowaction">
<?php
	$extrabuttons=array();
	$extrabuttons['addcolumn']=array('title'=>'addcolumn','name'=>'extracol','value'=>'yes');
	all_extrabuttons($extrabuttons,'infobook','processContent(this)')
?>
						</div>

					</th>
				</tr>
			</tfoot>
		</table>

		<input type="hidden" name="list" value="<?php print $list;?>" />
		<input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
</div>