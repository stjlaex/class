<?php
/**									student_list.php
 *   	Lists students identified in array sids.
 */

$action='student_list.php';
$choice='student_list.php';

include('scripts/sub_action.php');

$displayfields=array();
$displayfields[]='RegistrationGroup';
$displayfields[]='Gender';
$displayfields[]='DOB';
//$displayfields[]='';
if(isset($_POST['displayfield'])){$displayfields[0]=$_POST['displayfield'];}
if(isset($_POST['displayfield1'])){$displayfields[1]=$_POST['displayfield1'];}
if(isset($_POST['displayfield2'])){$displayfields[2]=$_POST['displayfield2'];}
if(isset($_POST['displayfield3'])){$displayfields[3]=$_POST['displayfield3'];}


$extrabuttons='';
if($_SESSION['role']=='office' or $_SESSION['role']=='admin'){
	$displayname='DisplayFullSurname';
	if(isset($CFG->books['external'][$_SESSION['role']]['webmail'])){
		$extrabuttons['emailstudents']=array('name'=>'current',
											 'title'=>'emailstudents',
											 'value'=>'email_students.php');
		$extrabuttons['emailcontacts']=array('name'=>'current',
											 'title'=>'emailstudents',
											 'value'=>'email_contacts.php');
		}
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
<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
<table class="listmenu sidtable">
	<th colspan="2"><?php print_string('checkall'); ?><input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" /></th>
	<th ><?php print_string('student'); ?></th>
<?php
	if($_SESSION['role']!='support'){
		while(list($index,$displayfield)=each($displayfields)){
?>
		<th><?php include('scripts/list_studentfield.php');?></th>
<?php
			}
		}
	else{
?>
	<th colspan="<?php print sizeof($displayfields);?>">&nbsp</th>
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
				$comment=commentDisplay($sid);
?>
			<a href="infobook.php?current=student_scores.php&sid=<?php print $sid;?>">T</a> 
			<span title="<?php print $comment['body'];?>">
			  <a href="infobook.php?current=comments_list.php&sid=<?php print $sid;?>"
				class="<?php print $comment['class'];?>">C</a> 
			</span>
			<a href="infobook.php?current=incidents_list.php&sid=<?php print $sid;?>">I</a>
<?php
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
	  </table>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	</form>
  </div>