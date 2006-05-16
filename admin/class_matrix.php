<?php
/**											class_matrix.php
 *
 *	Select which subjects are taught for which courses
 */

$action='class_matrix_action1.php';
$choice='class_matrix.php';

	if($r>-1){
		$bid=$respons[$r]{'subject_id'};
		$crid=$respons[$r]{'course_id'};
		if($bid==''){$bid='%';}
		if($bid!='%'){
		   	$error[]='Select a Course not a Subject responsibility.';
		   	include('scripts/results.php');
		   	exit;
		   	}
		}
	else {
		$error[]='You need to select a Course repsonsibility in the LogBook.';
		include('scripts/results.php');
		exit;
		}

	$yids=array();
	$d_yeargroup = mysql_query("SELECT DISTINCT id, name FROM yeargroup JOIN
	classes ON classes.yeargroup_id=yeargroup.id WHERE
	classes.course_id='$crid' ORDER BY yeargroup.ncyear, yeargroup.id");
	$d_subject = mysql_query("SELECT DISTINCT subject_id FROM cridbid 
				WHERE course_id='$crid' ORDER BY subject_id");
	$bids=array();
	$c=0;
   	while($subject = mysql_fetch_array($d_subject,MYSQL_ASSOC)){
   		$bids[$c]=$subject{'subject_id'};
		$c++;
		}

$extrabuttons['generateclasses']=array('name'=>'sub','value'=>'Generate');
$extrabuttons['savechanges']=array('name'=>'sub','value'=>'Update');
two_buttonmenu($extrabuttons);
?>
  <div class="content">
	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host; ?>">
	  <table class="listmenu">
		<tr>
		  <th></th>
<?php
  	$c=0;
	while($yeargroup=mysql_fetch_array($d_yeargroup,MYSQL_ASSOC)){
   		$yids[$c]=$yeargroup{'id'};
   		$yidname[$c]=$yeargroup{'name'};
  		print '<th>'.$yidname[$c].'</th>';
		$c++;
		}
?>
		</tr>
<?php
	for($c2=0; $c2<sizeof($bids); $c2++){
   		print '<tr id="'.$c2.'" >';
   		print '<th>'.$crid.':'.$bids[$c2].'</th>';
	
   		for($c=0; $c<sizeof($yids); $c++){	
   			$d_classes = mysql_query("SELECT * FROM classes WHERE
				subject_id='$bids[$c2]' AND yeargroup_id='$yids[$c]' AND course_id='$crid'");
   			$classes = mysql_fetch_array($d_classes,MYSQL_ASSOC);
   			$many=$classes{'many'};
   			$generate=$classes{'generate'};

//	   		print "<td><a href=\"admin.php?current=subject_edit.php&bid=$bids[$c2]&yid=$yids[$c]&crid=$crid&many=$many&generate=$generate\">".$many."/".$generate."</a></td>";
?>
		  <td>
			<input style="width:25%;" type="text" 
			  name="<?php print $bids[$c2].$yids[$c].'m';?>" value="<?php print $many;?>"/>

			  <select name="<?php print $bids[$c2].$yids[$c].'g';?>">
				<option value="none" <?php if($generate=="none"){print "selected='selected'";}?>></option>	
				<option value="sets" <?php if($generate=="sets"){print "selected='selected'";}?>>sets</option>	
				<option value="forms" <?php if($generate=="forms"){print "selected='selected'";}?>>forms</option>	
			  </select>
		  </td>
<?php
	  		}
?>
		</tr>
<?php
	   	}
?>
	  </table>

	  <input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
		<input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>
  </div>
