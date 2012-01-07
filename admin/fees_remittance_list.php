<?php
/**                                  fees_remittance_list.php    
 */

$action='fees_remittance_list_action.php';

$feeyear=$_POST['feeyear'];

include('scripts/sub_action.php');

$extrabuttons['newremittance']=array('name'=>'current','value'=>'fees_new_remittance.php');

two_buttonmenu($extrabuttons,$book);
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu">
		<caption><?php print_string('remittance',$book);?></caption>
<?php
		$entryno=0;
		$d_c=mysql_query("SELECT id FROM fees_remittance ORDER BY name;");
		while($remittance=mysql_fetch_array($d_c)){
			$remid=$remittance['id'];
			$Remittance=fetchRemittance($remid);
			$entryno++;
			$actionbuttons=array();
			$rown=0;
?>
		<tbody id="<?php print $entryno;?>">
		  <tr class="rowplus" onClick="clickToReveal(this)" id="<?php print $entryno.'-'.$rown++; ?>">
			<th>&nbsp</th>
			<td>
			  <a href="admin.php?current=fees_new_remittance.php&cancel=fees_remittance_list.php&remid=<?php print $remid;?>&feeyear=<?php print $feeyear;?>">
			  <?php print $Remittance['Name']['value'];?>
			  </a>
			</td>
			<td>
			  <?php print display_date($Remittance['EntryDate']['value']);?>
			</td>
		  </tr>
		  <tr class="hidden" id="<?php print $entryno.'-'.$rown++;?>">
			<td>
			</td>
			<td colspan="2">
			  <div>
			  <ul>
<?php 
			foreach($Remittance['Concepts'] as $Concept){
				print '<li><a  href="admin.php?current=fees_remittance_view.php&cancel='.$choice.'&choice='.$choice.'&remid='.$Remittance['id_db'].'&conid='.$Concept['id_db'].'">'.$Concept['Name']['value'].' - '.$Concept['TotalAmount']['value'];'</a></li>';
				}
?>
			  </ul>
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

