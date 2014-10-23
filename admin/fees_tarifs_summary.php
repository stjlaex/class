<?php
/**								  		fees_tarifs_summary.php
 */

$choice='fees.php';
$action='fees_concept_list.php';

if(isset($_POST['conids'])){$conids=(array)$_POST['conids'];}else{$conids=array();}

$curryear=get_curriculumyear();

$yeargroups=list_yeargroups();
$yeargroup_names=array();
foreach($yeargroups as $year){
	$yid=$year['id'];
	$yearcom=array('id'=>'','type'=>'year','name'=>$yid);
	$yeargroup_names[$yid]=$year['name'];
	$yeargroup_comids[$yid]=update_community($yearcom);
	}

if(count($conids)==0){
	$action='fees.php';
	$concepts=list_concepts();
	foreach($concepts as $concept){
		$conids[]=$concept['id'];
		}
	}

$headrow='';$secondrow='';$rows=array();
foreach($conids as $conid){
	$Concept=fetchConcept($conid);
	$tarifsno=count($Concept['Tarifs']);
	$headrow.="<th colspan='$tarifsno'>".$Concept['Name']['value']."</th>";
	foreach($Concept['Tarifs'] as $Tarif){
		$secondrow.="<th>".$Tarif['Name']['value']." ".display_money($Tarif['Amount']['value'])."</th>";
		foreach($yeargroup_names as $year_id=>$year_name){
			$d_tno=mysql_query("SELECT COUNT(*) FROM fees_charge JOIN student ON student.id=fees_charge.student_id JOIN fees_remittance ON fees_remittance.id=fees_charge.remittance_id WHERE fees_charge.tarif_id='".$Tarif['id_db']."' AND student.yeargroup_id='$year_id' AND fees_remittance.year='$curryear';");
			$tno=mysql_result($d_tno,0);
			$rows[$year_id].="<td class='live'>".$tno."</td>";
			}
		}
	}

two_buttonmenu();
?>
    <div id="heading">
        <h4><?php print_string('tarifssummary',$book); ?></h4>
    </div>

  <div id="viewcontent" class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >
	  	<div class="table-scrollable">
		  <table class="listmenu center smalltable">
			<tr>
			  <th></th>
			  <?php print $headrow; ?>
			</tr>
			<tr>
			  <th></th>
			  <?php print $secondrow; ?>
			</tr>
<?php
	foreach($yeargroup_names as $year_id=>$year_name){
?>
			<tr>
			  <th><?php echo $year_name;?></th>
			  <?php print $rows[$year_id]; ?>
			</tr>
<?php
		}
?>
		</table>
	  </div>
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print $action;?>" />
	</form>

  </div>
