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

/* Default to displaying enrolment for next curriculum year. */
$currentyear=get_curriculumyear();
if(isset($_POST['enrolyear']) and $_POST['enrolyear']!=''){$enrolyear=$_POST['enrolyear'];}
else{$enrolyear=$currentyear+1;}

$extrabuttons=array();
$extrabuttons['statistics']=array('name'=>'current',
								  'value'=>'yeargroup_statistics.php'
								  );
$extrabuttons['report']=array('name'=>'current',
							  'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							  'value'=>'admissions_print.php',
							  'onclick'=>'checksidsAction(this)');
twoplus_buttonmenu($enrolyear,$currentyear+3,$extrabuttons,$book,$currentyear);


$todate=date('Y-m-d');

$yearstart=$currentyear-1;
$yearstartdate=$yearstart.'-08-20';
$yearenddate=$yearstart.'-07-01';
$d_a=mysql_query("SELECT MAX(date) FROM admission_stats WHERE year='$enrolyear';");
if(mysql_result($d_a,0)>0){
	$update=mysql_result($d_a,0);
	$todates=explode('-',$todate);
	$updates=explode('-',$update);
	$diff=mktime(0,0,0,$todates[1],$todates[2],$todates[0]) - mktime(0,0,0,$updates[1],$updates[2],$updates[0]);
	$dayno=round($diff/(60*60*24));/* How days since last update */
	if($dayno>6){$save_stats=true;}
	else{$save_stats=false;}
	}
else{
	$save_stats=true;
	}

$yeargroups=list_yeargroups();
$yeargroup_names=array();/* The row index for both tables. */
foreach($yeargroups as $year){
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
	foreach($CFG->feeders as $feeder){
		if($feeder!=''){
			$Transfers=(array)feeder_fetch('transfer_nos',$feeder,$postdata);
			foreach($Transfers['transfer'] as $Transfer){
				if(!isset($feeder_nos[$Transfer['yeargroup']])){
					$feeder_nos[$Transfer['yeargroup']]=0;
					}
				trigger_error($Transfer['yeargroup'].' '.$Transfer['value'],E_USER_WARNING);
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
	foreach($tables as $table){
?>
	  <table class="listmenu center smalltable">
		<caption><?php print get_string($table['caption'],$book). 
		' - '.$CFG->schoolname.'  ('.display_date($todate).')';?></caption>
		<tr>
		  <th><?php print display_curriculumyear($enrolyear);?></th>
<?php
		$coltotals=array();
		$totals=array();
		$totals['class']='other';
		$totals['values']=array();
		$totals['label']='numberofstudents';
		/* Does the school have boarders? Add a row of sub-totals. */
		if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
			$boarder_totals=array();
			$boarder_totals['values']=array();
			$boarder_totals['class']='live';
			$boarder_totals['label']='boarders';
			}
		$cellclasses=array();
		foreach($table['cols'] as $colindex => $col){
			if(isset($col['display'])){
				$cellclasses[]=$col['class'];
?>
		  <th class="<?php print $col['class'];?>">
			<?php print $col['display'];?>
		  </th>
<?php
				$total=0;
				$total_boarder=0;
				/* Calculate the totals for each column. */
				foreach($yeargroups as $yeargroup){
					$cellvalue=$table['rows'][$yeargroup['id']][$col['value']]['value'];
					$total+=$cellvalue;
					if(isset($table['rows'][$yeargroup['id']][$col['value']]['value_boarder'])){
						$total_boarder+=$table['rows'][$yeargroup['id']][$col['value']]['value_boarder'];
						}
					//$total+=$table['rows'][$yeargroup['id']][$col['value']]['extravalue'];
					if($save_stats and isset($table['rows'][$yeargroup['id']][$col['value']]['name'])){
						$cellname=$table['rows'][$yeargroup['id']][$col['value']]['name'];
						mysql_query("INSERT admission_stats SET date='$todate', name='$cellname', year='$enrolyear', count='$cellvalue';");
						}
					}
				$totals['values'][$colindex]=$total;
				$boarder_totals['values'][$colindex]=$total_boarder;
				if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes' and $save_stats){
					$cellname=$table['rows'][1][$col['value']]['name_boarder'];
					mysql_query("INSERT admission_stats SET date='$todate', name='$cellname', year='$enrolyear', count='$total_boarder';");
					}
				}
			}

		$coltotals[]=$totals;
		if(isset($CFG->enrol_boarders) and $CFG->enrol_boarders=='yes'){
			$coltotals[]=$boarder_totals;
			}
?>
		</tr>
<?php
		foreach($table['rows'] as $rowindex => $tablecells){
			$colindex=0;
?>
		<tr>
		<th><?php print $yeargroup_names[$rowindex];?></th>
<?php
			foreach($tablecells as $cell){
				if(isset($cell['display'])){
?>
		  <td class="<?php print $cellclasses[$colindex++];?>">
			<?php print $cell['display'];?>
		  </td>
<?php
					}
				}
?>
		</tr>
<?php
			}
		foreach($coltotals as $totals){
?>
		<tr>
		  <th class="<?php print $totals['class'];?>">
			<?php print get_string('total',$book).' '.get_string($totals['label'],$book);?>
		  </th>
<?php
			foreach($totals['values'] as $total){
?>
				<td class="<?php print $totals['class'];?>"><?php print $total;?></td>
<?php
				}
?>
		</tr>

<?php
			}
?>
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
