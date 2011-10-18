<?php
/**									student_list.php
 *
 *   	Lists students identified in array sids.
 */

$action='student_list.php';
$choice='student_list.php';

include('scripts/sub_action.php');
if($sub=='select' or isset($_POST['selsavedview'])){$savedview=$_POST['selsavedview'];}
elseif(isset($_SESSION['savedview'])){$savedview=$_SESSION['savedview'];}
else{$savedview='';}
if(isset($_POST['colno'])){$displayfields_no=$_POST['colno'];}
if(isset($_POST['title'])){$title=$_POST['title'];}else{$title=$_SESSION['infolisttitle'];}
if(isset($_POST['umnfilter'])){$umnfilter=$_POST['umnfilter'];}else{$umnfilter='%';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
$_SESSION['savedview']=$savedview;
$_SESSION['infolisttitle']=$title;

$sort_types='';
$displayfields=array();
$extra_studentfields=array();
$application_steps=array('AP','ATD','AT','RE','CA','ACP','AC','WL');

if($savedview=='form'){
	$displayfields[]='Gender';
	$displayfields[]='DOB';
	$displayfields[]='Nationality';
	$displayfields_no=3;
	if($comid!=''){
		$com=get_community($comid);
		$fid=$com['name'];
		$tutor_users=(array)list_community_users($com,array('r'=>1,'w'=>1,'x'=>1));
		}
	}
elseif($savedview=='year'){
	$displayfields[]='RegistrationGroup';
	$displayfields[]='Gender';
	$displayfields[]='DOB';
	$displayfields_no=3;
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
elseif($savedview!='' and $sub=='select'){
	$d_c=mysql_query("SELECT comment FROM categorydef WHERE name='$savedview' AND type='col';");
	$taglist=mysql_result($d_c,0);
	$displayfields=(array)explode(':::',$taglist);
	$displayfields_no=sizeof($displayfields);
	}

if(!isset($displayfields_no)){
	$displayfields[]='EnrolmentStatus';
	$displayfields[]='RegistrationGroup';
	$displayfields[]='DOB';
	$displayfields_no=3;
	}

if($savedview=='' or $sub!='select'){
	for($dindex=0;$dindex < ($displayfields_no);$dindex++){
		if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
		}
	$savedview='';
	}

if(isset($_POST['extracol']) and $_POST['extracol']=='yes'){
	$displayfields_no++;
	$displayfields[]='';
	$savedview='';
	}

/* Approximate to saving 40% of table width for fixed columns. */
$displayfields_width=60/$displayfields_no.'%';

/* Include any general assessments plus their re-enrolment status for next year. */
$enrolyear=get_curriculumyear()+1;
$ReenrolAssDefs=fetch_enrolmentAssessmentDefinitions('','RE',$enrolyear);
$EnrolAssDefs=array_merge(fetch_enrolmentAssessmentDefinitions(),$ReenrolAssDefs); 
if(sizeof($EnrolAssDefs)>0){
	foreach($EnrolAssDefs as $AssDef){
		$extra_studentfields['Assessment'.$AssDef['id_db']]=$AssDef['Description']['value'];
		}
	}

$extrabuttons=array();
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
	$displayname='DisplayFullSurname';
	$extrabuttons['message']=array('name'=>'current',
								   'title'=>'message',
								   'value'=>'message.php');
   	$extrabuttons['addresslabels']=array('name'=>'current',
										 'title'=>'printaddresslabels',
										 'value'=>'print_labels.php');
   	$extrabuttons['exportstudentrecords']=array('name'=>'current',
												'title'=>'exportstudentrecords',
												'value'=>'export_students.php');
	}
else{
	$displayname='DisplayFullName';
	}

$extrabuttons['exportstudentrecords']=array('name'=>'current',
											'title'=>'exportstudentrecords',
											'value'=>'export_students.php');

two_buttonmenu($extrabuttons,$book);
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
		  <th rowspan="2" style="border:0;text-align:left;">
		  </th>

<?php
	if(isset($tutor_users)){
?>
		<th rowspan="2"  style="width:30%;">
		<label><?php print_string('formgroup'); ?></label>
		<?php print $fid.' &nbsp;&nbsp;';?>
		<?php 
			   foreach($tutor_users as $tutor_user){
				   print $tutor_user['forename'][0].' '. $tutor_user['surname'];
				   emaillink_display($tutor_user['email']);
				   }
?>
		</th>
<?php
		}
	elseif(isset($title) and $title!=''){
?>
		<th rowspan="2"   style="width:30%;"><label><?php print $title; ?></label></th>
<?php
		}
	else{
?>
		<th rowspan="2" style="width:30%;">
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
			$sortno=$dno+3;
			$sort_types.=",'s'";
?>
		<th style="width:<?php print $displayfields_width;?>;">
<?php include('scripts/list_studentfield.php');?>
		</th>
<?php
			}
		}
	else{
?>
	<th colspan="<?php print $displayfields_no;?>">&nbsp</th>
<?php
		}
?>
		</tr>
		<tr>
<?php

		foreach($displayfields as $dno => $displayfield){
			$sortno=$dno+3;
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

		</tr>
	</thead>
	<tbody>
<?php	
	$rown=1;
	foreach($sids as $sid){
		$Student=fetchStudent_short($sid);
		$field=fetchStudent_singlefield($sid,'EnrolmentStatus');
		$Student=array_merge($Student,$field);

		if($umnfilter=='%' or $Student['EnrolmentStatus']['value']==$umnfilter or ($umnfilter=='A' and in_array($Student['EnrolmentStatus']['value'],$application_steps))){

		if($Student['EnrolmentStatus']['value']=='C'){$enrolclass='';}
		elseif($Student['EnrolmentStatus']['value']=='P'){$enrolclass=' class="lowlite"';}
		elseif($Student['EnrolmentStatus']['value']=='AC' or $Student['EnrolmentStatus']['value']=='ACP'){$enrolclass=' class="gomidlite"';}
		elseif($Student['EnrolmentStatus']['value']=='CA' or $Student['EnrolmentStatus']['value']=='RE'){$enrolclass=' class="nolite lowlite"';}
		else{$enrolclass=' class="pauselite"';}
?>
		<tr id="sid-<?php print $sid;?>" <?php print $enrolclass;?>>
		  <td>
			<input type="checkbox" name="sids[]" value="<?php print $sid;?>" />
			<?php print $rown++;?>
		  </td>
		  <td>
<?php
		if($_SESSION['role']!='support'){
			/* Consider support staff to be not priviliged to access. */
				$comment=comment_display($sid);
?>
			<span title="<?php print $comment['body'];?>">
		<a href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>"
				class="<?php print $comment['class'];?>">C</a> 
			</span>
		<a href="infobook.php?current=incidents_list.php&sid=<?php print $sid;?>">I</a>
<?php		if($Student['SENFlag']['value']=='Y'){ ?>
		<a href="infobook.php?current=student_view_sen.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>&bid=G">S</a>
<?php			}
		if($Student['MedicalFlag']['value']=='Y'){ ?>
		<a href="infobook.php?current=student_view_medical.php&sid=<?php print $sid;?>&sids[]=<?php print $sid;?>&bid=G">M</a>
<?php			}
			}
		else{
			print '&nbsp';
			}
?>
		  </td>
		  <td class="student">
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>">
<?php 
		print $Student[$displayname]['value'];
?>
			</a>
			<div id="merit-<?php print $sid;?>"></div>
		  </td>
<?php
		foreach($displayfields as $displayfield){
			if(!array_key_exists($displayfield,$Student)){
				$field=fetchStudent_singlefield($sid,$displayfield);
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
			elseif($displayfield!=''){
				$displayout=$Student[$displayfield]['value'];
				}
			else{
				$displayout='';
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
		  <th colspan="3">
		  <div class="rowaction">
			<label><?php print_string('all',$book);?></label>
			<input title="<?php print_string('filter',$book);?>" 
				  type="radio" name="umnfilter"
				  value="%" <?php if($umnfilter=='%'){print 'checked';}?>
				  onchange="processContent(this);" />
		  </div>
		  <div class="rowaction">
			<label><?php print_string('current',$book);?></label>
			<input title="<?php print_string('filter',$book);?>" 
				  type="radio" name="umnfilter"
				  value="C" <?php if($umnfilter=='C'){print 'checked';}?>
				  onchange="processContent(this);" />
		  </div>
		  <div class="rowaction">
			<label><?php print_string('applied',$book);?></label>
			<input title="<?php print_string('filter',$book);?>" 
				  type="radio" name="umnfilter"
				  value="A" <?php if($umnfilter=='A'){print 'checked';}?>
				  onchange="processContent(this);" />
		  </div>
		  <div class="rowaction">
			<label><?php print_string('previous',$book);?></label>
			<input title="<?php print_string('filter',$book);?>" 
				  type="radio" name="umnfilter"
				  value="P" <?php if($umnfilter=='P'){print 'checked';}?>
				  onchange="processContent(this);" />
		  </div>
		</th>
		  <th colspan="<?php print $displayfields_no-1;?>">
		  <div class="rowaction">
<?php
	$d_c=mysql_query("SELECT DISTINCT name AS id, name AS name FROM categorydef WHERE type='col' ORDER BY name;");
	$listname='selsavedview';$selsavedview=$savedview;$listlabel='';$liststyle='width:16em;';
	include('scripts/set_list_vars.php');
	list_select_db($d_c,$listoptions,$book);
?>
		  </div>
		  <div class="rowaction">
<?php
	$buttons=array();
	$buttons['selectview']=array('name'=>'sub','value'=>'select');
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
		  </div>
		  <div class="rowaction">
<?php
	$buttons=array();
	if($savedview==''){
		$buttons['saveview']=array('title'=>'saveview','name'=>'current','value'=>'column_save.php');
		}
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
		  </div>
		</th>
		<th>
		  <div class="rowaction">
<?php
	$buttons=array();
	$buttons['addcolumn']=array('title'=>'addcolumn','name'=>'extracol','value'=>'yes');
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
		  </div>
		</th>
	  </tr>
	</tfoot>
	</table> 

	  <input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>


<script type="text/javascript">
	var TSort_Data = new Array ('sidtable', '', '', ''<?php print $sort_types;?>);
		tsRegister();
</script> 



<?php
include('scripts/studentlist_extra.php');
?> 
