<?php
/**                                  fees_concept_list.php    
 */

$action='fees_concept_list_action.php';

$feeyear=$_POST['feeyear'];

include('scripts/sub_action.php');

$extrabuttons['newconcept']=array('name'=>'current','value'=>'fees_new_concept.php');

two_buttonmenu($extrabuttons,$book);
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('concept',$book);?></caption>
<?php
		$entryno=0;
		$d_c=mysql_query("SELECT id FROM fees_concept ORDER BY name;");
		while($concept=mysql_fetch_array($d_c)){
			$conid=$concept['id'];
			$Concept=fetchConcept($conid);
			$entryno++;
			$actionbuttons=array();
			$rown=0;
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus <?php if($Concept['Inactive']['value']=='1'){print 'lowlite';} ?>" 
			  onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++; ?>">
			<th>&nbsp</th>
			<td>
			  <a href="admin.php?current=fees_new_concept.php&cancel=fees_concept_list.php&conid=<?php print $conid;?>&feeyear=<?php print $feeyear;?>">
			  <?php print $Concept['Name']['value'];?>
			  </a>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td colspan="2">
			  <div>
			  <ul>
<?php 
			foreach($Concept['Tarifs'] as $Tarif){
?>
				<li>
				  <a href="admin.php?current=fees_new_tarif.php&cancel=fees_concept_list.php&conid=<?php print $conid;?>&feeyear=<?php print $feeyear;?>&tarid=<?php print $Tarif['id_db'];?>">
				<?php print $Tarif['Name']['value'].' - '.$Tarif['Amount']['value'];?></a>
				</li>
<?php
				}
?>
			  </ul>
			</div>
			  <div class="rowaction">
				<a href="admin.php?current=fees_new_tarif.php&cancel=fees_concept_list.php&conid=<?php print $conid;?>&feeyear=<?php print $feeyear;?>&tarid=-1"><?php print get_string('add',$book).'&nbsp;'.get_string('tarif',$book);?></a>
			  </div>
			</td>

		  </tr>
		</tbody>
<?php
			}
?>
	  </table>
	</div>
	
	<input type="hidden" name="feeyear" value="<?php print $feeyear;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>

