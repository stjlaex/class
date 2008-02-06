<?php
/**								  		yeargroup_matrix.php
 */

$choice='yeargroup_matrix.php';
$action='yeargroup_matrix_action.php';

if($_SESSION['role']=='admin' or $_SESSION['role']=='office'){
	$extrabuttons['statistics']=array('name'=>'current','value'=>'yeargroup_statistics.php');
	}
else{
	$extrabuttons=array();
	}
three_buttonmenu($extrabuttons);
?>
  <div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post"
										action="<?php print $host; ?>" >

	<fieldset class="right">
		  <legend><?php print_string('assignyeartoteacher',$book);?></legend>

		<div class="center">
<?php $liststyle='width:95%;'; $required='yes'; include('scripts/list_teacher.php');?>
		</div>

		<div class="center">
<?php $liststyle='width:95%;'; $required='yes'; include('scripts/list_year.php');?>
		</div>

	</fieldset>

	    <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

	<div class="left"  id="viewcontent">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('yeargroup');?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
<?php
	if($_SESSION['role']=='admin' or $_SESSION['role']=='office'){
?>
		  <th><?php print_string('capacity',$book);?></th>
<?php
		}
?>
		  <th><?php print_string('yearresponsible',$book);?></th>
		</tr>
<?php
	$nosidstotal=0;
	$capacitytotal=0;
	$yeargroups=list_yeargroups();
	while(list($index,$year)=each($yeargroups)){
		$yid=$year['id'];
		$d_groups=mysql_query("SELECT gid FROM groups WHERE
				yeargroup_id='$yid' AND course_id=''");
		$gid=mysql_result($d_groups,0);
		$perms=getYearPerm($yid, $respons);
		$comid=update_community(array('type'=>'year','name'=>$yid));
		$com=get_community($comid);
		$nosids=countin_community($com);
		$nosidstotal=$nosidstotal+$nosids;
?>
		<tr>
		  <td>
<?php
		if($perms['r']==1){
			print '<a href="admin.php?current=yeargroup_edit.php&cancel='.$choice.'&choice='.$choice.'&newtid='.$tid.'&comtype=year'.'&comname='.$yid.'">'.$year['name'].'</a>';
			}
		else{
	   		print $year['name'];
	   		}
?>
		  </td>
		  <td><?php print $nosids;?></td>
<?php
	if($_SESSION['role']=='admin' or $_SESSION['role']=='office'){
		$capacity=$com['capacity'];
		$capacitytotal+=$capacity;
?>
		  <td><?php print $capacity;?></td>
<?php
		}
?>
		  <td>
<?php
		$yearperms=array('r'=>1,'w'=>1,'x'=>1);/*head of year only*/
		$users=(array)list_pastoral_users($yid,$yearperms);
		while(list($uid,$user)=each($users)){
			$Responsible=array('id_db'=>$yid.'-'.$uid);
			if($user['role']!='office' and $user['role']!='admin'){
				if($perms['x']==1){
?>
			<div  id="<?php print $yid.'-'.$uid;?>" class="rowaction" >
			  <button title="Remove this responsibility"
				name="current" 
				value="responsables_edit_yeargroup.php" 
				onClick="clickToAction(this)">
					 <?php print $user['username'];?>
			  </button>
			  <div id="<?php print 'xml-'.$yid.'-'.$uid;?>" style="display:none;">
							  <?php xmlechoer('Responsible',$Responsible);?>
			  </div>
			</div>
<?php
					}
				else{
					print $user['username'].' ';
					}
				}
			}
?>
		  </td></tr>
<?php
		}
?>
		  <tr>
			<th>
			  <?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
			</th>
			<td><?php print $nosidstotal;?></td>
<?php
	if($_SESSION['role']=='admin' or $_SESSION['role']=='office'){
?>
		  <td><?php print $capacitytotal;?></td>
<?php
		}
?>
			<td>&nbsp;</td>
		  </tr>
	  </table>
	</div>

<?php 

	if($_SESSION['role']=='xxx'  or $_SESSION['role']=='xxx'){

?>
	<div class="right">
	  <table class="listmenu">
		<tr>
		  <th><?php print_string('studentsnotonroll',$book);?></th>
		  <th><?php print_string('numberofstudents',$book);?></th>
		</tr>
<?php

		$comtypes=array();
		$comtypes[]='enquired';
		$comtypes[]='applied';
		$comtypes[]='accepted';
		//$comtypes[]='alumni';
		//$communities[]=array('type'=>'alumni','name'=>get_curriculumyear());
		while(list($index,$comtype)=each($comtypes)){
			$communities=(array)list_communities($comtype,get_curriculumyear());
			print '<tr><th colspan="2">'.get_string($comtype).'</th></tr>';

			while(list($index,$community)=each($communities)){
				list($enrolstatus,$enrolyear)=split(':',$community['name']);
				$description=get_string(displayEnum($enrolstatus,'enrolstatus'),$book).' '.$enrolyear;
				$nosids=countin_community($community);
				print '<tr><td>';
				print '<a href="admin.php?current=yeargroup_edit.php&cancel='.$choice.'&choice='.$choice.'&comtype='.$community['type'].'&comname='.$community['name'].'">'.$description.'</a>';
				print '</td>';
				print '<td>'.$nosids.'</td></tr>';
				}
			}
?>
	  </table>
	</div>
<?php
		}
?>

  </div>