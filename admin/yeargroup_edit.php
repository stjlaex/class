<?php 
/**			   						   yeargroup_edit.php
 */

$action='yeargroup_edit_action.php';
$cancel='yeargroup_matrix.php';

if(isset($_GET['comtype'])){$comtype=$_GET['comtype'];}else{$comtype='year';}
if(isset($_GET['comname'])){$comname=$_GET['comname'];}else{$comname='';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['newcomid'])){$newcomid=$_POST['newcomid'];}else{$newcomid='';}


	if($newcomid!=''){
		$newcommunity=get_community($newcomid);
		}
	if($comid!=''){
		$currentcommunity=get_community($comid);
		$comtype=$currentcommunity['type'];
		$comname=$currentcommunity['name'];
		}
	else{
		$currentcommunity=array('type'=>$comtype,'name'=>$comname);
		$comid=update_community($currentcommunity);
		}

	if($comtype=='year'){
		/*Check user has permission to edit*/
		$perm=getYearPerm($comname,$respons);
		$neededperm='w';
		include('scripts/perm_action.php');
	
		$d_year=mysql_query("SELECT name FROM yeargroup WHERE id='$comname'");
		$displayname=mysql_result($d_year,0);
		if($newcomid==''){$newcommunity=array('type'=>'year','name'=>'none');}
		}
	elseif($comtype=='alumni'){
		$displayname=get_string($comtype,'infobook');
		//$yid=get_curriculumyear();
		if($newcomid==''){$newcommunity=array('type'=>'year','name'=>'none');}
		}
	else{
		/*or enquired, applied, accepted*/
		$displayname=get_string($comtype,'infobook').' '.$comname;
		/*should not really have comid blank but... */
		if($newcomid=='' and $comtype=='enquired'){
			$newcommunity=array('type'=>'applied','name'=>'AP:');
			}
		elseif($newcomid=='' and $comtype=='applied'){
			$newcommunity=array('type'=>'accepted','name'=>'AC:');
			}
		elseif($newcomid=='' and $comtype=='accepted'){
			$newcommunity=array('type'=>'year','name'=>'none');
			}
		}

	$oldstudents=listin_community($currentcommunity);
	$newstudents=listin_union_communities($currentcommunity,$newcommunity);


	three_buttonmenu();
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post"
	  action="<?php print $host; ?>">

	  <div style="width:48%;float:left;"  id="viewcontent">
		<table class="listmenu">
		  <caption>
			<?php print_string('current');?>
			<?php print_string('yeargroup');?>
		  </caption>
		  <tr>
			<th>
			  <?php print $displayname;?>
			</th>
			<td>
			  <?php print_string('remove');?><br />
			  <input type="checkbox" name="checkall" 
				value="yes" onChange="checkAll(this);" />
				<?php print_string('checkall'); ?>
			</td>
		  </tr>
<?php
	while(list($sid,$student)=each($oldstudents)){
		print '<tr><td>'.$student['surname']. 
				', '.$student['forename']. ' ('.$student['form_id'].')</td>';
		print '<td><input type="checkbox" name="oldsids[]" value="'.$student['id'].'" /></td>';
		print '</tr>';
		}
?>
		</table>
	  </div>

	  <div style="width:50%;float:right;">
		<fieldset class="center">
		<legend><?php print_string('changegroup',$book);?></legend>
		  <div class="center">
<?php
			$onchange='yes';
			if($newcomid==''){
				/*user has not selected ac ommunity but one must be chosen*/
				$newcomid=update_community($newcommunity);
				}
			$selcomids=array($newcomid);
			include('scripts/list_community.php');
?>
		  </div>
		</fieldset>

		<fieldset class="center">
		<legend><?php print_string('choosestudentstoadd',$book);?></legend>
		<div class="center">
		  <label><?php print_string('studentsnotin',$book);?></label>
		  <select name="newsids[]" size="24" multiple="multiple" style="width:98%;">
<?php
	while(list($index,$student)=each($newstudents['scab'])){
		print '<option ';
		print	'value="'.$student['student_id'].'">'. 
		$student['surname'].', '.$student['forename'].' '. 
		$student['middlenames'].' ('.$student['form_id'].')</option>';
		}
?>
		  </select>
		</div>

		</fieldset>
	  </div>

	<input type="hidden" name="comid" value="<?php print $comid;?>" /> 
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="cancel" value="<?php print $cancel;?>" />
	</form>
  </div>
