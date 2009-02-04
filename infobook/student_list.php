<?php
/**									student_list.php
 *
 *   	Lists students identified in array sids.
 */

$action='student_list.php';
$choice='student_list.php';

include('scripts/sub_action.php');

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
$displayfields_width=60/$displayfields_no;


$extrabuttons=array();
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
	$displayname='DisplayFullSurname';
   	$extrabuttons['addresslabels']=array('name'=>'current',
										 'title'=>'printaddresslabels',
										 'onclick'=>'checksidsAction(this)',
										 'value'=>'contact_labels_print.php');
   	$extrabuttons['exportstudentrecords']=array('name'=>'current',
												'title'=>'exportstudentrecords',
												'value'=>'export_students.php');
	}
else{
	$displayname='DisplayFullName';
	}

two_buttonmenu($extrabuttons,$book);
?>

  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" 
	  method="post" action="<?php print $host;?>">
	  <table class="listmenu sidtable">
		<th colspan="2"><?php print_string('checkall'); ?>
		  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
		</th>
		<th><?php print_string('student'); ?></th>
		<th><?php print_string('formgroup'); ?></th>
<?php
	if($_SESSION['role']!='support'){

		$d_catdef=mysql_query("SELECT name, subtype FROM categorydef WHERE 
				type='med' AND rating='1' ORDER BY name;");
		while($medcat=mysql_fetch_array($d_catdef,MYSQL_ASSOC)){
			$extra_studentfields['Medical'.$medcat['subtype']]=$medcat['name'];
			}

		while(list($index,$displayfield)=each($displayfields)){
?>
		<th style="<?php print $displayfields_width;?>">
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
		if($_SESSION['role']!='office' and $_SESSION['role']!='support'){
				$comment=comment_display($sid);
?>
		<a href="infobook.php?current=student_scores.php&sid=<?php print $sid;?>">T</a> 
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
		  <td>
			<a href="infobook.php?current=student_view.php&sid=<?php print $sid;?>">
<?php 
				print $Student[$displayname]['value']; 
?>
			</a>
		  </td>
		  <td>
<?php 
				print $Student['RegistrationGroup']['value']; 
?>
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
		else{
			$displayout=$Student[$displayfield]['value'];
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
<th colspan="<?php print $displayfields_no+3;?>">&nbsp;
<?php
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
	$listname='messageoption';$listlabel='';$liststyle='width:16em;float:left;';
	include('scripts/set_list_vars.php');
	$options=array(
				   array('id'=>'smscontacts','name'=>get_string('smscontacts',$book))
				   ,array('id'=>'emailcontacts','name'=>get_string('emailcontacts',$book))
				   //,array('id'=>'smsstudents','name'=>get_string('smsstudents',$book))
				   //,array('id'=>'emailstudents','name'=>get_string('emailstudents',$book))
				   );
	list_select_list($options,$listoptions,$book);
	$buttons=array();
	$buttons['message']=array('name'=>'current','title'=>'message','value'=>'email_contacts.php');
	all_extrabuttons($buttons,'infobook','processContent(this)');
		}
?>
</th>
<th>
<?php
	$buttons=array();
	$buttons['addcolumn']=array('title'=>'addcolumn','name'=>'extracol','value'=>'yes');
	all_extrabuttons($buttons,'infobook','processContent(this)')
?>
</th>
</tr>
	  </table>

	  <input type="hidden" name="colno" value="<?php print $displayfields_no;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>
