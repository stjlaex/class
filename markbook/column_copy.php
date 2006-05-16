<?php 
/** 									column_copy.php
 */

$action='column_copy_action.php';

/* Make sure a column is checked*/
if(!isset($_POST{'checkmid'})){
	$action='class_view.php';
	$result[]='Please choose a mark column to copy!';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

$checkmid=$_POST{'checkmid'};
/*	Make sure only one column was checked*/	
	if(sizeof($checkmid)>1){
		$action='class_view.php';
		$result[]='Please choose only one mark column to copy!';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}
	else{$mid=$checkmid[0];}
	
	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
	$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
	$markdefname=$mark['def_name'];
	$marktype=$mark['marktype'];
	$scoretypes=array();
/*This is a horrible hack. Solves the problem (as things work now)
	that dependent columns have no real markdef of their own - so we'll guess!*/
    if($marktype=='level'){
	  /*make a guess at the best markdef to choose*/
   	    $lena=$mark['levelling_name'];
		$d_markdef=mysql_query("SELECT DISTINCT markdef.name FROM 
				 markdef JOIN levelling ON levelling.grading_name=markdef.grading_name 
					WHERE levelling.name='$lena'");
		if(mysql_num_rows($d_markdef)>0){$markdefname=mysql_result($d_markdef,0);}
		else{$markdefname='';/*no markdefs use that grading scheme!*/}
		}
	if($markdefname!=''){
		$d_markdef=mysql_query("SELECT DISTINCT scoretype FROM 
								markdef WHERE name='$markdefname'");
		$scoretypes[]=mysql_result($d_markdef,0);
		if($scoretypes[0]=='value'){$scoretypes[]='percentage';}
		if($scoretypes[0]=='percentage'){$scoretypes[]='value';}
		}
	elseif($marktype=='level'){$scoretypes[]='grade';}
	elseif($marktype=='sum'){$scoretypes[]='percentage';$scoretypes[]='value';}
	elseif($marktype=='average'){$scoretypes[]='percentage';$scoretypes[]='value';$scoretypes[]='grade';}
/*End horible hack.*/

	for($c7=0;$c7<sizeof($cids);$c7++){
		$cid=$cids[$c7];
		$d_subject=mysql_query("SELECT DISTINCT subject_id FROM class
				WHERE id='$cid'");
		}

	$markdefs=array();
    while ($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
		$newbid=$subject{'subject_id'};
		for($c=0;$c<sizeof($scoretypes);$c++){
		  $d_markdef=mysql_query("SELECT * FROM markdef WHERE scoretype='$scoretypes[$c]'
					AND	(subject_id LIKE '$newbid' OR subject_id='%')");
		  while ($markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC)){
		    if(!in_array($markdef,$markdefs)){
				$markdefs[]=$markdef;
				}
		    }
		  }
		}

three_buttonmenu();
?>
	  <div class="content">
		<form id="formtoprocess" name="formtoprocess"  
		  method="post" action="<?php print $host;?>"> 

		  <fieldset class="center">
			<legend>Type to copy into</legend>
			<label for="Mark Type">Mark-Type</label>
			<select class="required" id="Mark Type" name="def_name" tabindex="1" >
<?php	   	for($c=0; $c<sizeof($markdefs); $c++){
				print '<option ';
				print ' value="'.$markdefs[$c]{'name'}.'"';
				if($markdefname==$markdefs[$c]['name']){print ' selected="selected" ';}
				print '>'.$markdefs[$c]{'name'}.'</option>';
				}
?>
			</select>
		  </fieldset>

		  <fieldset class="left">
			<legend>Date of Mark</legend>
<?php
		$todate=$mark{'entrydate'};
		include('scripts/jsdate-form.php');
?>
		  </fieldset>

		  <fieldset class="right">
			<legend>New Mark's Identifying Name</legend>
			<label for="Topic">Mark's Title:</label>
			<input class="required" type="text" id="Topic" name="topic"
			  value="<?php print $mark{'topic'};?>" size="20" maxlength="38"  pattern="alphanumeric" />
			  <label for="Comment">Comment (optional):</label>
			  <input type="text" id="Comment" name="comment" value="<?php print
				$mark{'comment'}; ?>" size="40" maxlength="98" pattern="alphanumeric" />
		  </fieldset>

<?php if($scoretypes[0]=='percentage' or $scoretypes[0]=='value'){
?>
		  <fieldset class="left">
			<legend>If Copying to a Percentage</legend>
			<label for="Total">Default Out-of-Total:</label>
			<input type="text" id="Total" name="total" value="<?php print
			  $mark{'total'}; ?>" sixe="3" maxlength="4"  pattern="integer" />
			  <label for="Scale">Scale Old Values:</label>
			  <input type="checkbox" id="Scale" name="scale" value="yes"/>
		  </fieldset>
<?php	}
?>
	<input type="hidden" name="mid" value="<?php print $mid; ?>"/>
	<input type="hidden" name="oldtotal" value="<?php print $mark{'total'}; ?>"/>
	<input type="hidden" name="marktype" value="<?php print $mark{'marktype'}; ?>"/>
	<input type="hidden" name="lena" value="<?php print $mark{'levelling_name'}; ?>"/>	
	<input type="hidden" name="midlist" value="<?php print $mark{'midlist'}; ?>"/>
	<input type="hidden" name="bid" value="<?php print $mark{'subject_id'}; ?>"/>
	<input type="hidden" name="crid" value="<?php print $mark{'course_id'}; ?>"/>	
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	<input type="hidden" name="cancel" value="<?php print $choice;?>"/>
		</form>
	  </div>
