<?php 
/** 									define_mark_action1.php
 */

$host="markbook.php";
$current="define_mark.php";
$action="define_mark_action2.php";
$choice="class_view.php";
	
$crid=$_POST['crid'];
$bid=$_POST['bid'];
$type=$_POST['type'];
$comment=$_POST['comment'];
$name=$_POST['name'];
$sub=$_POST{'sub'};

if($sub=='Cancel'){
		$current="class_view.php";
		$result[]="Action cancelled.";
		include("scripts/results.php");
		include("scripts/redirect.php");
		exit;
		}

three_buttonmenu();
?>
  <div id="heading">New mark definition '<?php print $name; ?>'</div>

  <div class="content">

	<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>"> 
	
					<fieldset class="center">
					  <legend>Details for New Mark-Type</legend>
<?php
	if($type=="value"){}
	elseif($type=="grade"){
		include('scripts/list_gradescheme.php');
		}
	elseif($type=="percentage"){ 
?>

<label for="Total" >What is the default 'out-of-total':</label>
	<input class="required" type="text" name="total"  
		id="Total" value="" maxlength="4" pattern="numeric" />
<?php 
		}
	elseif($type=="comment"){}
?>
	  </fieldset>

	  <input type="hidden" name="comment" value="<?php print $comment; ?>" />
	  <input type="hidden" name="name" value="<?php print $name; ?>" />
	  <input type="hidden" name="crid" value="<?php print $crid; ?>" />
	  <input type="hidden" name="bid" value="<?php print $bid; ?>" />
	  <input type="hidden" name="type" value="<?php print $type;?>" />
	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />

	</form>
  </div>
