<?php
/**											new_report.php
 *
 *
 * Allows the editing or creation of both report wrapper definitions
 * and report definitions (depending on wether a responsibility course
 * is selected).
 *
 *
 */

$action='new_report_action.php';
$choice='new_report.php';
$toyear=get_curriculumyear();
$extrabuttons=array();

if($r>-1){$rcrid=$respons[$r]['course_id'];}
else{$rcrid='';}

$reptypes=array('profile','subject','wrapper');
if($rcrid!=''){
	$extrabuttons['newsubjectreport']=array('name'=>'current','value'=>'new_report_action.php');
	}
else{
	$extrabuttons['newreportbinder']=array('name'=>'current','value'=>'new_report_action.php');
	}

two_buttonmenu($extrabuttons);
?>
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

	  <input type="text" style="display:none;" id="Id_db" name="id" value="" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $current;?>" />
  </form>

  <div class="content">
	<div class="center">
<?php
	if($rcrid!=''){
		$tablecaption=get_string('subjectreports');
		}
	else{
		$tablecaption=get_string('subjectreportbinders',$book);
		}
?>
	  <h3><?php print $tablecaption;?></h3>
<?php
	foreach($reptypes as $reptype){
		if($rcrid!=''){
		    $d_report=mysql_query("SELECT id FROM report WHERE type='$reptype' AND
								course_id='$rcrid' AND year='$toyear' ORDER BY date DESC, title");
			}
		else{
		    $d_report=mysql_query("SELECT id FROM report WHERE type='$reptype' AND
								course_id='wrapper' AND year='$toyear' ORDER BY date DESC, title");
			}
		if(mysql_num_rows($d_report)>0){
?>
	  <table class="listmenu" name="listmenu">
		<caption><?php print_string($reptype);?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print get_string('date');?></th>
			<?php if($rcrid!=''){ print '<th>'.get_string('stage').'</th>';}?>
			<th><?php print_string('title');?></th>
		  </tr>
		</thead>
<?php
	while($report=mysql_fetch_array($d_report,MYSQL_ASSOC)){
		unset($ReportDef);
		$rid=$report['id'];
		$ReportDef=fetch_reportdefinition($rid);
		$rown=0;

		$imagebuttons=array();
		$imagebuttons['clicktodelete']=array('name'=>'current',
										'id'=>'deletereport'.$rid,
											 'value'=>'delete_report.php',
											 'title'=>'delete');
		$extrabuttons=array();
		$extrabuttons['edit']=array('name'=>'process',
								'id'=>'deletereport'.$rid,
								'value'=>'edit');
?>
		<tbody id="<?php print $rid;?>">
		  <tr class="rowplus" onClick="clickToReveal(this);" 
								id="<?php print $rid.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print $ReportDef['report']['date']; ?></td>
			<?php if($rcrid!=''){print '<td>'.$ReportDef['report']['stage'].'</td>';} ?>
			<td><?php print $ReportDef['report']['title']; ?></td>
		  </tr>
		  <tr class="hidden" id="<?php print $rid.'-'.$rown++;?>">
			<td colspan="6">
			  <p>
<?php
		if($rcrid!=''){
?>
				<value id="<?php print $rid;?>-Markcount">
				  <?php print $ReportDef['MarkCount']['value'];?>
				</value>
				<?php print_string('markbookcolumns',$book);?>
<?php
			}
		else{
			foreach($ReportDef['reptable']['rep'] as $Rep){
?>
				<value id="<?php print $Rep['id_db'];?>">
				  <?php print $Rep['course_id'].' '.$Rep['stage'].' '.$Rep['name'];?>
				</value>
				<br />
<?php
				}
			}
?>
			  </p>

<?php
		if($rcrid!=''){
			$extrabuttons['generatecolumns']=array('name'=>'current',
												   'value'=>'generate_report_columns.php');
			$extrabuttons['deletecolumns']=array('name'=>'current',
												 'value'=>'delete_report_columns.php');
			}
?>
		<div class="right">
<?php
		rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
?>
		</div>
			</td>
		  </tr>
		  <div id="<?php print 'xml-'.$rid;?>" style="display:none;">
<?php
					  xmlechoer('ReportDefinition',$ReportDef);
?>
		  </div>
		</tbody>
<?php
	   }
?>
	  </table>
<?php
	  }
	}
?>
	</div>
  </div>
