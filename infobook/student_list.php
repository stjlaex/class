<?php
/**									student_list.php
 *
 *   	Lists students identified in array sids.
 */

$action='student_list.php';
$choice='student_list.php';

include('scripts/sub_action.php');
if(isset($_POST['selsavedview'])){$savedview=$_POST['selsavedview'];$_SESSION['savedview']=$savedview;}
elseif(isset($_SESSION['savedview'])){$savedview=$_SESSION['savedview'];}
else{$savedview='';}
if(isset($_POST['colno'])){$displayfields_no=$_POST['colno'];}

$displayfields=array();
$extra_studentfields=array();

if($savedview=='form'){
	$displayfields[]='Gender';
	$displayfields[]='DOB';
	$displayfields[]='Nationality';
	$displayfields_no=3;
	}
elseif($savedview=='year'){
	$displayfields[]='RegistrationGroup';
	$displayfields[]='Gender';
	$displayfields[]='DOB';
	$displayfields_no=3;
	}
elseif($savedview!=''){
	$d_c=mysql_query("SELECT comment FROM categorydef WHERE name='$savedview' AND type='col';");
	$taglist=mysql_result($d_c,0);
	$displayfields=(array)explode(':::',$taglist);
	$displayfields_no=sizeof($displayfields);
	}

if(!isset($displayfields_no)){
	$displayfields[]='RegistrationGroup';
	$displayfields[]='DOB';
	$displayfields_no=2;
	}

for($dindex=0;$dindex < ($displayfields_no);$dindex++){
	if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
	}

if(isset($_POST['extracol']) and $_POST['extracol']=='yes'){
	$displayfields_no++;
	$displayfields[]='';
	$savedview='';
	}

/* Approximate to saving 40% of table width for fixed columns. */
$displayfields_width=60/$displayfields_no.'%';

$EnrolAssDefs=(array)fetch_enrolmentAssessmentDefinitions();
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
	/*
   	$extrabuttons['addresslabels']=array('name'=>'current',
										 'title'=>'printaddresslabels',
										 'onclick'=>'checksidsAction(this)',
										 'value'=>'contact_labels_print.php');
	*/
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
	<form id="formtoprocess" name="formtoprocess" 
	  method="post" action="<?php print $host;?>">
	  <table class="listmenu sidtable" id="sidtable">
		<th colspan="2"><?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
		</th>


<?php
	if($savedview=='form'){
		$Student=fetchStudent_short($sids[0]);
		$fid=$Student['RegistrationGroup']['value'];
		$tutor_user=(array)get_tutor_user($fid);
?>
		<th>
		<label><?php print_string('formgroup'); ?></label>
		<?php print $fid.' &nbsp;&nbsp;';?>
		<?php print $tutor_user['forename'][0].' '. $tutor_user['surname'];?>
		<a onclick="parent.viewBook('webmail');" target="viewwebmail" 
			href="webmail.php?recipients[]=<?php print $tutor_user['email'];?>">
			<img class="clicktoemail" title="<?php print_string('clicktoemail');?>" />
		</a>
		</th>
<?php
		}
	else{
?>
		<th><?php print_string('student'); ?></th>
<?php
		}

	if($_SESSION['role']!='support'){
		/* Consider support staff to be not priviliged to access. */
		$d_catdef=mysql_query("SELECT name, subtype FROM categorydef WHERE 
				type='med' AND rating='1' ORDER BY name;");
		while($medcat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
			$extra_studentfields['Medical'.$medcat['subtype']]=strtolower($medcat['name']);
			}

		while(list($index,$displayfield)=each($displayfields)){
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

	$rown=1;
	while(list($index,$sid)=each($sids)){
		$Student=fetchStudent_short($sid);
		if($Student['YearGroup']['value']==' '){$enrolclass=' class="lowlite"';}
		else{$enrolclass='';}
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
	reset($displayfields);
	while(list($index,$displayfield)=each($displayfields)){
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
	reset($sids);
?>
		<tr>
		  <th colspan="<?php print $displayfields_no+2;?>">
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
	</table>

	  <input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>

<?php
include('scripts/studentlist_extra.php');
?> 
