<?php 
/* 									define_levels_action1.php
*/

$host='markbook.php';
$current='define_levels_action1.php';
$action='define_levels_action2.php';
$choice='class_view.php';

if(isset($_POST{'sub'})){$sub=$_POST{'sub'};}else{$sub='';}
if($sub=='Cancel'){
		$current='class_view.php';
		include('scripts/redirect.php');
		exit;
		}

$gena=$_POST['gena'];
$lena=$_POST['lena'];
$comment=$_POST['comment'];

/* Need to validate the uniqness of $lena value!!!*/

	$d_grading=mysql_query("SELECT * FROM grading WHERE name='$gena'");
	$grading=mysql_fetch_array($d_grading,MYSQL_ASSOC);
	$bid=$grading{'subject_id'};
	$crid=$grading{'course_id'};
	$pairs=explode (';', $grading{'grades'});
?>
<div class="content">
<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>"> 
<table class="listmenu">
<caption>Define Level Boundaries for <?php print $lena;?></caption>
	<tr>
		  <th>Using grade scheme:<?php print $gena;?></th>
		  <th>Enter levels in <em>ascending</em> order!</th>
	</tr> 
<?php	 
	for($c3=0; $c3<sizeof($pairs); $c3++){
		list($level_grade, $level)=split(':',$pairs[$c3]);
?>				
	<tr>
		<td><?php print $level_grade; ?></td>
<?php 
		if($c3==0){$value=0;}
		else{$value='';}
?>
		<td>
			<input tabindex="<?php print $c3;?>" class="required" type="text" name="levels[]"
			  maxlength="5" size="3" value="<?php print $value;?>"
			  pattern="numeric" />
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
		<input type="hidden" name="mid" value="<?php print $_POST{'mid'}; ?>" />
		<input type="hidden" name="crid" value="<?php print $crid; ?>" />
		<input type="hidden" name="grades" value="<?php print $grading{'grades'}; ?>" />
		<input type="hidden" name="current" value="<?php print $action;?>" />
		<input type="hidden" name="choice" value="<?php print $choice;?>" />
</form>
</div>
<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" value="Submit">Submit</button>
	<button onClick="processContent(this);" name="sub" value="Cancel">Cancel</button>
	<button onClick="processContent(this);" name="sub" value="Reset">Reset</button>
	<button style="visibility:hidden;" name="" value=""></button>
</div>















