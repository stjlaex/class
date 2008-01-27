<?php 
/**				   				portfolio_accounts.php
 */

$choice='portfolio_accounts.php';
$action='portfolio_accounts_action.php';

include('scripts/sub_action.php');


twoplusprint_buttonmenu();
?>
  <div id="heading">
  <?php print get_string('eportfolios',$book).' ';?>
  </div>
  <div id="viewcontent" class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
		<table class="listmenu">
		  <tr>
			<th>
			  <label id="checkall">
				<?php print_string('checkall');?>
				<input type="checkbox" name="checkall" value="yes" onChange="checkAll(this);" />
			  </label>
			</th>
			<th><?php print_string('student');?></th>
		  </tr>
<?php
	$sids=array();
	while(list($index,$sid)=each($sids)){
		$student=$students[$sid];
?>
		  <tr>
			<td>
			  <input type="checkbox" name="sids[]" value="<?php print $sid; ?>" />
			</td>
			<td>
				   <?php print $student['surname']; ?>, <?php print $student['forename']; ?>
					  (<?php print $student['form_id']; ?>)
			</td>
		 </tr>
<?php	
		}
	reset($sids);
?>
		</table>

<?php if(isset($yid)){?>
 	<input type="hidden" name="yid" value="<?php print $yid;?>" />
<?php	} ?>
<?php if(isset($fid)){?>
 	<input type="hidden" name="fid" value="<?php print $fid;?>" />
<?php	} ?>
 	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
 	<input type="hidden" name="choice" value="<?php print $choice;?>" />
 	<input type="hidden" name="current" value="<?php print $action;?>" />
</form>
</div>
