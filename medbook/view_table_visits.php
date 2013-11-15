<?php
/**                                  view_table_visits.php
 */
$d_v=mysql_query("SELECT * FROM medical_log WHERE student_id='$sid' ORDER BY date DESC;");
?>
<div class="center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th>Date</th>
					<th>Time</th>
					<th>Category</th>
				</tr>
			</thead>
<?php

$imagebuttons=array();
$extrabuttons=array();
/*the rowaction buttons used within each assessments table row*/
$imagebuttons['clicktodelete']=array('name'=>'current',
									 'value'=>'delete_medical.php',
									 'title'=>'delete');
$extrabuttons['edit']=array('name'=>'process',
							'value'=>'edit',
							'title'=>'edit');

while($visits=mysql_fetch_array($d_v,MYSQL_ASSOC)){
	$entryno=$visits['id'];
	$entry['id_db']=$entryno;
	$entry['Date']=array(
					'value'=>$visits['date']
					,'label'=>'date'
					,'field_db'=>'date'
					,'type_db'=>'date'
					);
	$entry['Time']=array(
					'value'=>$visits['time']
					,'label'=>'time'
					,'field_db'=>'time'
					,'type_db'=>'time'
					);
	$entry['Category']=array(
					'value'=>$visits['category']
					,'value_db'=>$visits['category']
					,'label'=>'category'
					,'field_db'=>'category'
					,'type_db'=>'varchar(10)'
					);
	$entry['Details']=array(
					'value'=>$visits['details']
					,'value_db'=>$visits['details']
					,'label'=>'details'
					,'field_db'=>'details'
					,'type_db'=>'text'
					);
	$entries['Medical'][]=$entry;
	$rown=0;
	if($visits['details']!=''){
?>
			<tbody id="<?php echo $entryno;?>">
				<tr id="<?php echo $entryno.'-'.$rown++;?>" class="rowplus" onclick="clickToReveal(this)">
					<th> </th>
					<td></td>
					<td><?php echo display_date($visits['date']);?></td>
					<td><?php if($visits['time']!='00:00:00'){echo $visits['time'];}?></td>
					<td><?php echo $visits['category'];?></td>
				</tr>
				<tr id="<?php echo $entryno.'-'.$rown++;?>" class="hidden">
					<td colspan="6">
						<p><?php echo $visits['details'];?></p>
<?php
					rowaction_buttonmenu($imagebuttons,$extrabuttons,$book);
?>
					</td>
				</tr>
				<div id="<?php print 'xml-'.$entryno;?>" style="display:none;">
<?php
					xmlechoer("medical",$entry);
?>
				</div>
			</tbody>
<?php
		}
	}
?>
		</table>
		<input type="hidden" value="medical" name="tagname">
		<input type="hidden" name="current" value="med_add_visit.php">
	</form>
</div>
