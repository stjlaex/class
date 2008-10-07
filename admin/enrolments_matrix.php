<?php
/**								  		enrolments_matrix.php
 *
 * This produces two tables, first applications during current year
 * and then enrolments (and re-enrolments) for the actual roll.
 *
 */

$choice='enrolments_matrix.php';
$action='enrolments_matrix_action.php';

require_once('lib/curl_calls.php');

$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear;}

$extrabuttons=array();
twoplus_buttonmenu($enrolyear,$currentyear+3,$extrabuttons,$book);

if($CFG->enrol_applications=='yes'){
	$applications_live=true;
	}
else{
	$applications_live=false;
	}

$todate=date('Y-m-d');
$yearstartdate=$currentyear-1;
$yeargroups=list_yeargroups();
$yeargroup_names=array();/* The row index for both tables. */
while(list($index,$year)=each($yeargroups)){
	$yid=$year['id'];
	$yearcom=array('id'=>'','type'=>'year','name'=>$yid);
	$yeargroup_names[$yid]=$year['name'];
	$yeargroup_comids[$yid]=update_community($yearcom);
	}

/**
 * This is for checking transfers from feeder schools and needs
 * options setting in school.php. Probably not of use to most ClaSS
 * schools unless you are part of a group of schools.
 */
	$feeder_nos=array();
	$postdata['enrolyear']=$enrolyear;
	$postdata['currentyear']=$currentyear;
	while(list($findex,$feeder)=each($CFG->feeders)){
		if($feeder!=''){
			$Transfers=feeder_fetch('transfer_nos',$feeder,$postdata);
			while(list($findex,$Transfer)=each($Transfers['transfer'])){
				if(!isset($feeder_nos[$Transfer['yeargroup']])){
					$feeder_nos[$Transfer['yeargroup']]=0;
					}
				$feeder_nos[$Transfer['yeargroup']]+=$Transfer['value'];
				}
			}
		}

?>

  <div id="heading">
	<label><?php print_string('academicyear'); ?></label>
	<?php  print display_curriculumyear($enrolyear);?>
  </div>

  <div id="viewcontent" class="content">
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

<?php

  include('enrolments_matrix_apptable.php');

  include('enrolments_matrix_enroltable.php');

	$tables=array();
	$tables[]=array('caption'=>'applications','rows'=>$app_tablerows,'cols'=>$appcols);
	$tables[]=array('caption'=>'enrolments','rows'=>$enrol_tablerows,'cols'=>$enrolcols);
	while(list($tindex,$table)=each($tables)){
?>
	  <table class="listmenu center smalltable">
		<caption><?php print_string($table['caption'],$book);?></caption>
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		$coltotals=array();
		while(list($colindex,$col)=each($table['cols'])){
			if(isset($col['display'])){
?>
		  <th>
			<?php print $col['display'];?>
		  </th>
<?php
			$total=0;
			reset($yeargroups);
			while(list($yindex,$yeargroup)=each($yeargroups)){
				$total+=$table['rows'][$yeargroup['id']][$col['value']]['value'];
				}
			$coltotals[$colindex]=$total;
			}
		  }
?>
		</tr>

<?php
		while(list($rowindex,$tablecells)=each($table['rows'])){
?>
		<tr>
		<th><?php print $yeargroup_names[$rowindex];?></th>
<?php
			while(list($cellindex,$cell)=each($tablecells)){
				if(isset($cell['display'])){

?>
		  <td><?php print $cell['display'];?></td>
<?php
					}
				}
?>
		</tr>
<?php
			}
?>
		<tr>
		  <th>
			<?php print get_string('total',$book).' '.get_string('numberofstudents',$book);?>
		  </th>
<?php
		while(list($totindex,$total)=each($coltotals)){
?>
		  <td><?php print $total;?></td>
<?php
			}
?>
		</tr>
	  </table>
<?php
		}
?>

	  <input type="hidden" name="enrolyear" value="<?php print $enrolyear;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
