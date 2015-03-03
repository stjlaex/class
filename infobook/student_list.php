<?php
/**									student_list.php
 *
 *   	Lists students identified in array sids.
 */
$action='student_list.php';
$choice='student_list.php';
include ('scripts/sub_action.php');

if($sub=='select' or isset($_POST['selsavedview'])){$savedview=$_POST['selsavedview'];}elseif(isset($_SESSION['savedview'])){$savedview=$_SESSION['savedview'];}else{$savedview='';}
if(isset($_POST['colno'])){$displayfields_no=$_POST['colno'];}
if(isset($_POST['title'])){$title=$_POST['title'];}else{$title=$_SESSION['infolisttitle'];}
if(isset($_POST['umnfilter'])){$umnfilter=$_POST['umnfilter'];}else{$umnfilter='%';}
if(isset($_POST['privfilter'])){$privfilter=$_POST['privfilter'];}else{$privfilter='visible';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}

$_SESSION['savedview']=$savedview;
$_SESSION['infolisttitle']=$title;
$displayfields=array();
$extra_studentfields=array();
$application_steps=array('AP','AT','RE','CA','ACP','AC','WL');

if($savedview=='form'){
	if(isset($CFG->schooltype) or $CFG->schooltype!='ela'){
		$displayfields[]='Gender';
		$displayfields[]='EntryDate';
		$displayfields[]='LeavingDate';
		}
	else{
		$displayfields[]='Gender';
		$displayfields[]='DOB';
		$displayfields[]='Nationality';
		}
	$displayfields_no=3;
	if($comid!=''){
		$com=get_community($comid);
		$fid=$com['name'];
		$tutor_users=(array)list_community_users($com,array('r'=>1,'w'=>1,'x'=>1));
		}
	}
elseif($savedview=='year'){
	if(isset($CFG->schooltype) or $CFG->schooltype!='ela'){
		$displayfields[]='RegistrationGroup';
		$displayfields[]='EntryDate';
		$displayfields[]='LeavingDate';
		}
	else{
		$displayfields[]='RegistrationGroup';
		$displayfields[]='Gender';
		$displayfields[]='DOB';
		}

	$displayfields_no=3;
	}
elseif($savedview=='section'){
	$displayfields[]='YearGroup';
	$displayfields[]='RegistrationGroup';
	$displayfields[]='Gender';
	$displayfields[]='DOB';
	$displayfields_no=4;
	}
elseif($savedview=='club'){
	$displayfields[]='RegistrationGroup';
	$displayfields[]='Gender';
	$displayfields[]='Transport';
	$displayfields_no=3;
	}
elseif($savedview=='transport'){
	$displayfields[]='RegistrationGroup';
	$displayfields[]='Gender';
	$displayfields[]='Club';
	$displayfields_no=3;
	}
elseif($savedview=='enrolment'){
	$displayfields[]='Gender';
	$displayfields[]='EntryDate';
	$displayfields[]='EnrolmentYearGroup';
	$displayfields[]='EnrolmentStatus';
	$displayfields[]='EnrolmentApplicationDate';
	$displayfields_no=5;
	}
elseif($savedview!=''/* and $sub=='select'*/ and $savedview!='default'){
	$d_c=mysql_query("SELECT comment FROM categorydef WHERE name='$savedview' AND type='col';");
	$taglist=mysql_result($d_c,0);
	$displayfields=(array)explode(':::',$taglist);
	$displayfields_no=sizeof($displayfields);
	}
if(!isset($displayfields_no) or $savedview=='default'){
	$displayfields=array();
	$displayfields[]='EnrolmentStatus';
	$displayfields[]='RegistrationGroup';
	$displayfields[]='DOB';
	if($savedview!='default'){$displayfields_no=3;}
	}
if($savedview=='' or $sub!='select'){
	for($dindex=0;$dindex<($displayfields_no);$dindex++){
		if(isset($_POST['displayfield'.$dindex])){
			$displayfields[$dindex]=$_POST['displayfield'.$dindex];
			$_SESSION['displayfields'][$dindex]=$_POST['displayfield'.$dindex];
			}
		}
	$savedview='';
	}
if(isset($_POST['extracol']) and $_POST['extracol']=='yes'){
	$_SESSION['displayfields'][]='';
	$displayfields_no++;
	$displayfields[]='';
	$savedview='';
	}

if(isset($_SESSION['displayfields']) and count($_SESSION['displayfields'])>0 and $savedview!=''){
	unset($_SESSION['displayfields']);
	}
if(isset($_SESSION['displayfields']) and count($_SESSION['displayfields'])>0 and $savedview=='' and $sub!='select'){
	$ds=$_SESSION['displayfields'];
	$displayfields=array();
	foreach($ds as $dindex=>$d){
		$displayfields[$dindex]=$d;
		}
	$displayfields_no=sizeof($displayfields);
	unset($_SESSION['savedview']);
	$savedview='';
	}

/* Approximate to saving 40% of table width for fixed columns. */
$displayfields_width=60/$displayfields_no.'%';
/* Include any general assessments plus their re-enrolment status for next year. */
$enrolyear=get_curriculumyear()+1;
$ReenrolAssDefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);

$EnrolAssDefs=array_merge(fetch_enrolmentAssessmentDefinitions(),$ReenrolAssDefs); 
$EnrolAssDefs=array_merge(fetch_enrolmentAssessmentDefinitions('','M'),$EnrolAssDefs);
if(sizeof($EnrolAssDefs)>0){
	foreach($EnrolAssDefs as $AssDef){
		$extra_studentfields['Assessment'.$AssDef['id_db']]=$AssDef['Description']['value'];
		}
	}

$extrabuttons=array();
if(($_SESSION['role']=='office' or $_SESSION['role']=='admin') and $CFG->studentname_order=='surname'){$displayname='DisplayFullSurname';} else {
$displayname='DisplayFullName';}
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){$extrabuttons['print']=array('name'=>'current','pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/infobook/','value'=>'student_profile_print.php','xmlcontainerid'=>'profile','onclick'=>'checksidsAction(this)');}
if($_SESSION['role']=='office' or $_SESSION['role']=='admin' or ($_SESSION['role']=='teacher' and $_SESSION['worklevel']>1)){$extrabuttons['message']=array('name'=>'current','title'=>'message','value'=>'message.php');$extrabuttons['addresslabels']=array('name'=>'current','title'=>'printaddresslabels','value'=>'print_labels.php');}
$extrabuttons['exportstudentrecords']=array('name'=>'current','title'=>'exportstudentrecords','value'=>'export_students.php');
?>

  <div id="heading">
	<label>
<?php
	if(isset($_POST['yeargroup'])){$yeargroup=$_POST['yeargroup'];}else{$yeargroup='';}
	if(isset($_POST['section'])){$section=$_POST['section'];}else{$section='';}
	if($yeargroup!='' and $community==''){
		$d_y=mysql_query("SELECT * FROM community WHERE name='$yeargroup' AND type='year';");
		$detail=mysql_result($d_y,0,'detail');
		if($detail!=''){$displayheader=$detail;}
		else{$displayheader='Year: '.$yeargroup;}
		}
	elseif($section!='' and $community==''){
		$section=get_sectionname($section);
		$displayheader='Section: '.$section;
		}
	elseif($comid!=''){
		$com=get_community($comid);
		if($title!=''){$displayheader=$title;}
		elseif($title=='' and $com['year']!='0000'){$displayheader=$com['name'].': '.$com['year'];}
		elseif($title=='' and $com['year']=='0000'){$displayheader=$com['name'];}
		}
	echo $displayheader;
?>
	</label>
  </div>

<?php
	two_buttonmenu($extrabuttons,$book);
?>

	<div id="viewcontent" class="content">
		<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>">
			<div class="table-scrollable">
				<table class="listmenu sidtable" id="sidtable">
				<thead>
					<tr>
						<th rowspan="2" colspan="1" class="checkall">
							<input type="checkbox" name="checkall"  value="yes" onChange="checkAll(this);" />
						</th>
						<th rowspan="2"></th>
<?php
							if(isset($tutor_users)){
?>
						<th rowspan="2"  style="width:25%;">
							<label><?php print_string('formgroup'); ?></label>
							<?php print $fid.' &nbsp;&nbsp;'; ?>
<?php
								$tutoremails='';
								foreach($tutor_users as $tutor_user){
									print $tutor_user['forename'][0].' '.$tutor_user['surname'].' ';
									$tutoremails.=$tutor_user['email'].';';
									}
								emaillink_display($tutoremails);
?>
						</th>
<?php
								}
							elseif(isset($title) and $title!=''){
?>
						<th rowspan="2" style="width:25%;"><label><?php print $title; ?></label></th>
<?php
								}
							else{
?>
						<th rowspan="2" style="width:25%;">
							<?php print_string('student'); ?>
						</th>
<?php
								}
						if($_SESSION['role']!='support'){
							/* Consider support staff to be not priviliged to access. */
							$d_catdef=mysql_query("SELECT name, subtype FROM categorydef WHERE
												type='med' AND rating='1' ORDER BY name;");
							while($medcat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
								$extra_studentfields['Medical'.$medcat['subtype']]=strtolower($medcat['name']);
								}
							foreach($displayfields as $dno => $displayfield){
?>
						<th style="width:<?php print $displayfields_width; ?>;">
							<div class="div-sortable">
<?php
								include ('scripts/list_studentfield.php');
?>
								<a href="#" class="sortable"></a>
							</div>
						</th>
<?php
								}
							}
						else{
?>
						<th colspan="<?php print $displayfields_no; ?>">&nbsp</th>
<?php
							}
?>
					</tr>
				</thead>
				<tbody>
<?php
	$rown=1;
	foreach($sids as $sid){
		$Student=fetchStudent_short($sid);
		$field=fetchStudent_singlefield($sid,'EnrolmentStatus');
		$Student=array_merge($Student,$field);
		if($CFG->schooltype=='ela'){
			$field=fetchStudent_singlefield($sid,'AnotherNumber');
			$Student=array_merge($Student,$field);
			$field=fetchStudent_singlefield($sid,'CandidateID');
			$Student=array_merge($Student,$field);
			$field=fetchStudent_singlefield($sid,'CandidateNumber');
			$Student=array_merge($Student,$field);
			$AppDate=fetchStudent_singlefield($sid,'EnrolmentApplicationDate');
			$Student['ProgrammeEndDate']=array('label'=>'', 'value'=>date('Y-m-d',strtotime($AppDate['EnrolmentApplicationDate']['value'].' +'.intval($Student['AnotherNumber']['value']) .' week')));
			$Student['LeavingDate']=array('label'=>'', 'value'=>date('Y-m-d',strtotime($Student['EntryDate']['value'].' +'.(intval($Student['CandidateID']['value']) + intval($Student['CandidateNumber']['value'])).' weeks -3 days')));
			}

		if($umnfilter=='%' or $Student['EnrolmentStatus']['value']==$umnfilter or ($umnfilter=='A' and in_array($Student['EnrolmentStatus']['value'],$application_steps))){

			if($Student['EnrolmentStatus']['value']=='C'){$enrolclass='';}
			elseif($Student['EnrolmentStatus']['value']=='P'){$enrolclass=' class="lowlite"';}
			elseif($Student['EnrolmentStatus']['value']=='AC' or $Student['EnrolmentStatus']['value']=='ACP'){$enrolclass=' class="gomidlite"';}
			elseif($Student['EnrolmentStatus']['value']=='CA' or $Student['EnrolmentStatus']['value']=='RE'){$enrolclass=' class="nolite lowlite"';}
			else{$enrolclass=' class="pauselite"';}
?>
		<tr id="sid-<?php print $sid; ?>" <?php print $enrolclass; ?>>
			<td>
				<div class="checker">
					<span>
						<input type="checkbox" name="sids[]" value="<?php print $sid; ?>">
					</span>
				</div>
				<div style="float: left"><?php print $rown++; ?></div>
			</td>
			<td>
<?php
	include ('scripts/studentlist_shortcuts.php');
?>
			</td>
			<td class="student">
				<a href="infobook.php?current=student_view.php&sid=<?php print $sid; ?>">
<?php
					print $Student[$displayname]['value'];
?>
				</a>
				<div class="miniature" id="mini-<?php echo $sid; ?>"></div>
				<div class="merit" id="merit-<?php print $sid; ?>"></div>
			</td>
<?php

		foreach($displayfields as $displayfield){
			if(!array_key_exists($displayfield,$Student)){
				$field=fetchStudent_singlefield($sid,$displayfield,$privfilter);
				$Student=array_merge($Student,$field);
				}
			if(isset($Student[$displayfield]['type_db'])  
			   and $Student[$displayfield]['type_db']=='enum'){
				$displayout=displayEnum($Student[$displayfield]['value'],$Student[$displayfield]['field_db']);
				$displayout=get_string($displayout,$book);
				}
			elseif(isset($Student[$displayfield]['type_db'])  
				   and $Student[$displayfield]['type_db']=='date'){
					$displayout=display_date($Student[$displayfield]['value']);
				}
			elseif(!isset($Student[$displayfield]['value'])){
				/* This is for the Tutor field or any which has multiple values to combine. */
				$displayout='';
				foreach($Student[$displayfield] as $displayfieldvalue){
					$displayout.=' '.$displayfieldvalue['value'];
					}
				}
			elseif($displayfield!=''){
				$displayout=$Student[$displayfield]['value'];
				}
			else{
				$displayout='';
				}
			if(substr($displayfield, 0, 10)=="Assessment"){
				$edisplayfield=str_split($displayfield, 10);
				$eid=$edisplayfield[1];
				$Assessments=(array)fetchAssessments_short($sid,$eid,'G');
				if($Assessments[0]['Comment']['value']!=""){
					$extra=$Assessments[0]['Comment']['value'];
					$displayout="<span title='$extra'>".$displayout."</span>";
					}
				}
			print '<td>'.$displayout.'</td>';
			}

?>
		</tr>
<?php
			}
		}
?>
	</tbody>
	<tfoot class="noprint">
		<tr>
			<td colspan="3">
				<div class="rowaction">
					<input title="<?php print_string('filter',$book); ?>" type="radio" name="umnfilter" value="P" <?php if($umnfilter=='P') {print 'checked'; } ?>onchange="processContent(this);" />
					<label><?php print_string('previous',$book); ?></label>
				</div>
				<div class="rowaction">
					<input title="<?php print_string('filter',$book); ?>" type="radio" name="umnfilter" value="A" <?php if($umnfilter=='A') {print 'checked'; } ?> onchange="processContent(this);" />
					<label><?php print_string('applied',$book); ?></label>
				</div>
				<div class="rowaction">
					<input title="<?php print_string('filter',$book); ?>" type="radio" name="umnfilter" value="C" <?php if($umnfilter=='C') {print 'checked'; } ?>onchange="processContent(this);" />
					<label><?php print_string('current',$book); ?></label>
				</div>
				<div class="rowaction">
					<input title="<?php print_string('filter',$book); ?>" type="radio" name="umnfilter" value="%" <?php if($umnfilter=='%') {print 'checked'; } ?> onchange="processContent(this);" />
					<label><?php print_string('all',$book); ?></label>
				</div>
			</td>
			<td colspan="<?php print ($displayfields_no==3)?2:$displayfields_no-2; ?>">
				<div class="rowaction">
<?php
			$d_c=mysql_query("SELECT DISTINCT name AS id, name AS name FROM categorydef WHERE type='col' ORDER BY name;");
			$listname='selsavedview';
			$selsavedview=$savedview;
			$listlabel='';
			$listdefaultvalue='yes';
			//$liststyle='width:16em;';
			include ('scripts/set_list_vars.php');
			list_select_db($d_c,$listoptions,$book);
?>
				</div>
				<div class="rowaction">
<?php
			$buttons=array();
			$buttons['selectview']=array('name'=>'sub','value'=>'select');
			all_extrabuttons($buttons,'infobook','processContent(this)');
?>
				</div>
				<div class="rowaction">
					<input title="<?php print get_string('private',$book).' '.get_string('visible',$book); ?>" type="radio" name="privfilter" value="visible" <?php if($privfilter=='visible') {print 'checked'; } ?> onchange="processContent(this);" />
					<label><?php print_string('visible',$book); ?></label>
				</div>
				<div class="rowaction">
					<input title="<?php print get_string('private',$book).' '.get_string('hidden',$book); ?>" type="radio" name="privfilter" value="hidden" <?php if($privfilter=='hidden') {print 'checked'; } ?>onchange="processContent(this);" />
					<label><?php print_string('private',$book); ?></label>
				</div>
			</td>
			<td colspan="2">
				<div class="rowaction">
<?php
	$buttons=array();
	$buttons['addcolumn']=array('title'=>'addcolumn','name'=>'extracol','value'=>'yes');
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
				</div>
				<div class="rowaction">
<?php
			$buttons=array();
			if($savedview=='') {
				$buttons['saveview']=array('title'=>'saveview','name'=>'current','value'=>'column_save.php');
				}
			all_extrabuttons($buttons,'infobook','processContent(this)');
?>
				</div>
			</td>
		</tr>
	</tfoot>
</table>
	</div>

<?php
		if($yeargroup!='' and $community==''){
?>
		<input type="hidden" name="yid" value="<?php print $yeargroup; ?>" />
<?php
			}
		elseif($comid!=''){
?>
		<input type="hidden" name="comid" value="<?php print $comid; ?>" />
<?php
			}
?>

		<input type="hidden" name="colno" value="<?php print $displayfields_no; ?>" />
		<input type="hidden" name="current" value="<?php print $action; ?>" />
		<input type="hidden" name="cancel" value="<?php print ''; ?>" />
		<input type="hidden" name="choice" value="<?php print $choice; ?>" />
	</form>
</div>

<?php
	include ('scripts/studentlist_extra.php');
?>

<?php
	if($CFG->tempinfosheet!=''){$profileprint=$CFG->tempinfosheet;}
	else{$profileprint="student_profile_print";}
?>
	<div id="xml-profile" style="display:none;">
		<params>
			<checkname>sids</checkname>
			<transform><?php print $profileprint;?></transform>
			<paper>portrait</paper>
		</params>
	</div>

