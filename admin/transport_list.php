<?php
/**                                  transport_list.php   
 *
 *
 */

$action='transport_list_action.php';

include('scripts/sub_action.php');

$extrabuttons=array();
if((isset($_POST['busname']) and $_POST['busname']!='')){$busname=$_POST['busname'];}else{$busname='';}
if((isset($_GET['busname']) and $_GET['busname']!='')){$busname=$_GET['busname'];}
if((isset($_POST['fid']) and $_POST['fid']!='')){$fid=$_POST['fid'];}else{$fid='';}
if((isset($_GET['fid']) and $_GET['fid']!='')){$fid=$_GET['fid'];}

if($busname!=''){
	$com=array('id'=>'','type'=>'transport','name'=>$busname);
	$students=(array)listin_community($com);
	}
elseif($fid!=''){
	$com=array('id'=>'','type'=>'form','name'=>$fid);
	$students=(array)listin_community($com);
	}
else{
	$students=array();
	}
//$Bus=fetchBus();

two_buttonmenu($extrabuttons,$book);

	if($buaname!=-1){
?>
  <div id="heading">
	<label><?php print_string('transport',$book);?></label>
<?php	print $Bus['Name']['value'].' ';?>
  </div>
<?php
		}
?>
  <div id="viewcontent" class="content">


  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu sidtable">
		<caption><?php print get_string($com['type'],$book).': '.$com['name'];?></caption>
		<thead>
		  <tr>
			<th colspan="4">&nbsp;</th>
<?php
	$days=getEnumArray('dayofweek');
	foreach($days as $day => $dayname){
		print '<th>'.get_string($dayname,$book).'</th>';
		}
?>
		  </tr>
		</thead>
<?php
	$rown=1;
	while(list($index,$student)=each($students)){
		$sid=$student['id'];
		print '<tr id="sid-'.$sid.'">';
		print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td>';
		print '<td colspan="2" class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_view.php&sid='.$sid.'">'.$student['surname'].', '. $student['forename'].'</a></td>';
		print '<td>'.$student['form_id'].'</td>';
		print '</tr>';
		}
?>
	  </table>
	</div>


	<input type="hidden" name="busname" value="<?php print $busname;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

