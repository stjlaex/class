<?php 
/** 									column_average.php
 *
 */

$action='column_average_action.php';

/* Make sure a column is checked */
if(!isset($_POST['checkmid'])){
		$result[]='Choose more than one column to average.';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}
$checkmids=(array)$_POST['checkmid'];

/*	Make sure more than one column was checked */	
if(sizeof($checkmids)<2){
	$result[]='Choose more than one column to average.';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}



	$joiner='';
	$markdefs=array();
	$scoretypes=array();
	$midlist='';
	foreach($checkmids as $c => $mid){
		$d_m=mysql_query("SELECT name, scoretype, grading_name
				FROM markdef JOIN mark ON markdef.name=mark.def_name WHERE mark.id='$mid';");
		$markdef=mysql_fetch_array($d_m,MYSQL_ASSOC);

		/* Check all columns are compatible by taking the properties
		 * on the first pass through and comparing
		 * the porperties of each susequent pass.
		 */
		if($markdef['scoretype']=='value' or $markdef['scoretype']=='percentage' or $markdef['scoretype']=='grade'){
			if($c==0){
				$grading_name=$markdef['grading_name'];
				$scoretype=$markdef['scoretype'];
				$def_name=$markdef['name'];
				}
			if($grading_name==$markdef['grading_name'] and $markdef['scoretype']==$scoretype){
				$midlist.=$joiner. $mid;
				}
			else{
				/* Mixing two different types of columns so drop back
				   to to simple percentage using numerical values for
				   all grades. 
				*/
				$midlist.=$joiner. $mid;
				$scoretype='value';
				$def_name='Raw Score';
				$grading_name='';
				$result[]=get_string('warning',$book).': '.get_string('notofsametype',$book);
				}
			$joiner=' ';
			$markdefs[$def_name]=$def_name;
			}
		else{
			/* Can't handle dependent columns yet... */
			$result[]=get_string('warning',$book).': '.'Marks must be primary values...';
			}
		}

if(isset($result)){
	$action='class_view.php';
	include('scripts/results.php');
	include('scripts/redirect.php');
	}

three_buttonmenu();
?>
	  <div class="content">
		<form id="formtoprocess" name="formtoprocess" novalidate   
		  method="post" action="<?php print $host;?>"> 

		  <fieldset class="center">
			<legend><?php print_string('detailsofthenewmark',$book);?></legend>
			<label for="Mark Type"><?php print_string('thetypeofmark',$book);?></label>
			<select class="required" id="Mark Type" name="def_name" tabindex="1" >
<?php
			foreach($markdefs as $markdef){
				print '<option value="'.$markdef.'"';
				if($def_name==$markdef){print ' selected="selected" ';}
				print '>'.$markdef.'</option>';
				}
?>
			</select>
		  </fieldset>

		  <fieldset class="center">
			<legend><?php print_string('dateofmark',$book);?></legend>
			<?php $todate=$mark['entrydate']; include('scripts/jsdate-form.php');?>
		  </fieldset>

		  <fieldset class="center">
			<legend><?php print_string('detailsofmark',$book);?></legend>
			<label for="Topic"><?php print_string('markstitleidentifyingname',$book);?></label>
			<input class="required" type="text" id="Topic" name="topic"
			  value="<?php print $mark['topic'];?>" size="20" maxlength="38"  pattern="alphanumeric" />

			  <label for="Comment"><?php print_string('optionalcomment',$book);?></label>
			  <input type="text" id="Comment" name="comment" value="<?php print
				$mark['comment']; ?>" size="40" maxlength="98" pattern="alphanumeric" />
		  </fieldset>

<?php
	if($scoretype=='percentage' or $scoretype=='value'){
?>
		  <fieldset class="center">
			<legend><?php print_string('weightings',$book);?></legend>
			<div class="left">
<?php
		foreach($checkmids as $c => $mid){
			$d_m=mysql_query("SELECT topic, entrydate, total FROM mark WHERE mark.id='$mid';");
			$mark=mysql_fetch_array($d_m, MYSQL_ASSOC);
?>
			<div class="left">
			  <label for="weight<?php print $mid;?>"><?php print $mark['topic'].'<br />'.display_date($mark['entrydate']);?></label>
			</div>
			<div class="right">
			  <input type="text" id="weight<?php print $mid;?>" name="weight<?php print $mid;?>" value="" size="3" maxlength="4" pattern="integer" />
			</div>
<?php
				}
?>
			</div>

		  </fieldset>
<?php	}
?>
	<input type="hidden" name="midlist" value="<?php print $midlist; ?>"/>
	<input type="hidden" name="scoretype" value="<?php print $scoretype; ?>"/>
	<input type="hidden" name="grading_name" value="<?php print $grading_name; ?>"/>
	<input type="hidden" name="def_name" value="<?php print $def_name; ?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	<input type="hidden" name="cancel" value="<?php print $choice;?>"/>
		</form>
	  </div>
