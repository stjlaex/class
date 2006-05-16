<?php 
/* 									define_mark_action1.php
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

?>
<div class="content">
<h4>Defining "<?php print $name; ?>" "<?php print $type;?>"</h4>

<form name="formtoprocess" id="formtoprocess" method="post" action="<?php print $host;?>"> 
	
 	<input type="hidden" name="comment" value="<?php print $comment; ?>" />
 	<input type="hidden" name="name" value="<?php print $name; ?>" />
 	<input type="hidden" name="crid" value="<?php print $crid; ?>" />
 	<input type="hidden" name="bid" value="<?php print $bid; ?>" />
 	<input type="hidden" name="type" value="<?php print $type;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	
<fieldset class="leftcentertop">
<legend>Details for New Mark-Type</legend>
<?php
	if($type=="value"){}
	elseif ($type=="grade"){
		$d_grading=mysql_query("SELECT * FROM grading WHERE
				(subject_id LIKE '$bid' OR subject_id='%') AND (course_id
				LIKE '$crid' OR course_id='%') ORDER BY name");
?>

<label for="Grade scheme" >Select the grading scheme:</label>
	<select class="required" name="gena" id="Grade scheme">
	<option value="" selected="selected"></option>
<?php
		while($grading=mysql_fetch_array($d_grading,MYSQL_ASSOC)){
				print "<option value='".$grading{'name'}."'>";
				print $grading{'name'}."</option>";
				}
		print "</select>";
		}
	elseif ($type=="percentage"){ 
?>
<label for="Total" >What is the default 'out-of-total':</label>
	<input class="required" type="text" name="total"  
		id="Total" value="" maxlength="4" pattern="numeric" />
<?php 
		}

	elseif
	($type=="comment"){}
	
	elseif
	($type=="tier"){}
?>
</fieldset>

<fieldset class="leftcentermiddlebottom">
<legend>Qualify for use with assessments.</legend>
<label for="resultq" >Select the result qualfier</label>
<?php
				$table='resultqualifier';
				$enum=getEnumArray($table);
				print "<select name='resultq' size='1'>";
				print "<option value=''>select</option>";		
				while(list($inval,$description)=each($enum)){	
					print "<option ";
					print " value='".$inval."'>".$description."</option>";
					}
				print "</select>";					
?>

<label for="method" >Select the assessment method</label>
<select name="method" size="1">
<option value=""></option>
<?php
				$table='method';
				$enum=getEnumArray($table);
				while(list($inval,$description)=each($enum)){	
					print "<option ";
					print " value='".$inval."'>".$description."</option>";
					}
?>
<option value="%">applies to all</option>
</select>
</fieldset>

</form>
</div>
<div class="buttonmenu">
	<button onClick="processContent(this);" name="sub" value="Submit">Submit</button>
	<button onClick="processContent(this);" name="sub" value="Cancel">Cancel</button>
	<button onClick="processContent(this);" name="sub" value="Reset">Reset</button>
</div>





















































