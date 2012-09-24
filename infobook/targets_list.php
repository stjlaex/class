<?php
/**                                  targets_list.php
 */

$action='targets_list_action.php';
$cancel='student_view.php';

$Targets=(array)fetchTargets($sid);

three_buttonmenu();
?>
  <div id="heading">
	<label><?php print_string('targets',$book);?></label>
	<?php print $Student['DisplayFullName']['value'];?>
  </div>


  <div class="content">
	<div class="center">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">

<?php
	$yid=$Student['YearGroup']['value'];
	$perm=getYearPerm($yid, $respons);

	foreach($Targets['Target'] as $index => $Target){
		if(is_array($Target)){
			$cattype=$Target['Category']['value_db'];
?>
			  <table>
				<tr>
				  <td>&nbsp;</td>
			   	</tr>
				<tr>
				  <td>
				  <label for="Detail<?php print $index;?>">
					<?php print $Target['Category']['value']; ?>
				  </label>
				  <div style="float:right;">
					<label>
					<?php xmlelement_input($Target['EntryDate'],$index,$tab,$book);?>
					</label>
				  </div>
				  </td>
				</tr>
				<tr>
				  <td>
				  <textarea id="Detail<?php print $index;?>" wrap="on" rows="5" tabindex="<?php print $tab++;?>"
						name="<?php print $Target['Detail']['field_db'].$index;?>" 
						><?php print $Target['Detail']['value_db'];?></textarea>
				  </td>
				</tr>
			  </table>
<?php

			}
		}
?>
		</div>


	<input type="hidden" name="current" value="<?php print $action;?>"/>
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>"/>
 	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	</form>

	</div>
  </div>
