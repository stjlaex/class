<?php 
/** 									define_levels_action1.php
 */

$action='define_levels_action2.php';

include('scripts/sub_action.php');

if(isset($_POST['sub'])){$sub=$_POST['sub'];}else{$sub='';}
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['crid'])){$crid=$_POST['crid'];}else{$crid='%';}

$gena=$_POST['gena'];
$lena=$_POST['lena'];
$comment=$_POST['comment'];

/* Need to validate the uniqness of $lena value!!!*/

	$d_grading=mysql_query("SELECT * FROM grading WHERE name='$gena'");
	$grading=mysql_fetch_array($d_grading,MYSQL_ASSOC);
	$pairs=explode(';', $grading['grades']);

three_buttonmenu();
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess" novalidate method="post" action="<?php print $host;?>"> 
<h4><?php print_string('definelevelboundaries',$book);?> <?php print $lena;?></h4>
<table class="listmenu">
	<tr>
		  <th><?php print_string('gradescheme',$book);?>: <?php print $gena;?></th>
		  <th><?php print_string('enterlevels',$book);?></th>
	</tr> 
<?php	 
	for($c3=0; $c3<sizeof($pairs); $c3++){
		list($level_grade, $level)=explode(':',$pairs[$c3]);
?>				
	<tr>
		<td><?php print $level_grade; ?></td>
<?php 
		if($c3==0){$value=0;$fix='readonly="readonly"';}
		else{$value='';$fix='';}
?>
		<td>
			<input tabindex="<?php print $c3;?>" class="required" type="text" name="levels[]"
			  maxlength="5" size="3" value="<?php print $value;?>" <?php print $fix;?> pattern="numeric" />
		</td>
	</tr>
<?php
		}
?> 
</table>

<input type="hidden" name="gena" value="<?php print $gena; ?>" />
<input type="hidden" name="lena" value="<?php print $lena; ?>" />
<input type="hidden" name="comment" value="<?php print $comment; ?>" />
<input type="hidden" name="bid" value="<?php print $bid; ?>" />
<input type="hidden" name="checkmid" value="<?php print $_POST['checkmid']; ?>" />
<input type="hidden" name="crid" value="<?php print $crid; ?>" />
<input type="hidden" name="grades" value="<?php print $grading['grades']; ?>" />
<input type="hidden" name="current" value="<?php print $action;?>" />
<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form>
</div>
