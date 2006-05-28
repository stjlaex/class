<?php 
/** 									column_delete.php
 */

$action='column_delete_action.php';

/* Make sure a column is checked*/
if(!isset($_POST{'checkmid'})){
	$result[]='Please choose a mark to delete!';
	$action='class_view.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
   	exit;
	}

$checkmid=$_POST{'checkmid'};

/*	Make sure only one column was checked*/	
if(sizeof($checkmid)>1){
		$result[]='You can only delete one mark at a time!';
		$action='class_view.php';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		} 
	$mid=$checkmid[0];
	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
	$mark = mysql_fetch_array($d_mark,MYSQL_ASSOC);

/*	Make sure user has priviliges to edit*/	
	if($mark{'author'}!=$tid){
		$perm=getMarkPerm($mid, $respons);
		if($perm['w']!='1'){
			$action=$choice;
			$result[]='You need to be the marks author <br /> or have subject
					permissions to delete the mark!<br />';
			include('scripts/results.php');
			include('scripts/redirect.php');
			exit;		
			}
		}

three_buttonmenu();
?>
  <div class="content">
	<form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>">
	  <fieldset class="center">
		<table class="listmenu">
		  <caption>
			<?php print_string('marktodelete',$book); print $mark{'topic'}.' ('.$mark{'entrydate'}.')'; ?>
		  </caption>
		<tr><td></td><td></td></tr>
		<tr>
		  <td>
			  <input type="radio" name="delete" value="only"
				checked="checked" />
			</td>
			<td>
			  <?php print_string('deletemarkcurrentlychosen',$book);?>
			</td>
		  </tr>
		  <tr>
			<td>
			  <input type="radio" name="delete" value="all" />
			</td>
			<td>
			  <?php print_string('deletemarkforall',$book);?>
			</td>
		  </tr>
		  <tr><td></td><td></td></tr>
		</table>
	  </fieldset>
	  <input type="hidden" name="mid" value="<?php print $mid; ?>"/>
	  <input type="hidden" name="current" value="<?php print $action;?>"/>
	  <input type="hidden" name="choice" value="<?php print $choice;?>"/>
	  <input type="hidden" name="cancel" value="<?php print $choice;?>"/>
	</form>  				
  </div>
