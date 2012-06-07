<?php 
/** 									column_copy.php
 */

$action='column_copy_action.php';

/* Make sure a column is checked */
if(!isset($_POST['checkmid'])){
	$action='class_view.php';
	$result[]='Please choose a mark column to copy!';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}

$checkmids=(array)$_POST['checkmid'];
/* Make sure only one column was checked */	
if(sizeof($checkmids)>2){
	$action='class_view.php';
	$result[]='Please choose only one or two mark columns to copy!';
	include('scripts/results.php');
	include('scripts/redirect.php');
	exit;
	}
elseif(sizeof($checkmids)>1){
	$action='column_copyinto_action.php';
	$marks=array();
	$copyto=-1;
	foreach($checkmids as $c => $mid){
		$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
		$marks[]=mysql_fetch_array($d_mark,MYSQL_ASSOC);
		$markdefname=$marks[$c]['def_name'];
		$marktype=$marks[$c]['marktype'];
		if($markdefname!=''){
			$d_markdef=mysql_query("SELECT DISTINCT scoretype, grading_name, outoftotal FROM markdef WHERE name='$markdefname'");
			$marks[$c]['scoretype']=mysql_result($d_markdef,0,0);
			$marks[$c]['grading_name']=mysql_result($d_markdef,0,1);
			if(!isset($copyto_markdefname)){
				$copyto=$c;
				$copyto_markdefname=$markdefname;
				}
			}
		}

	if($copyto>-1){
		if($copyto==0){$copyfrom=1;}
		else{$copyfrom=0;}

		three_buttonmenu();
?>
	  <div class="content">
		<form id="formtoprocess" name="formtoprocess" novalidate   
		  method="post" action="<?php print $host;?>"> 

		  <fieldset class="left divgroup">
			<legend><?php print_string('from','infobook');?></legend>
			<label for="Mark Type"><?php print_string('mark',$book);?></label>
			<?php print $marks[$copyfrom]['topic'].' ('. display_date($marks[$copyfrom]['entrydate']). ')';?>
		  </fieldset>

		  <fieldset class="right divgroup">
			<legend><?php print_string('to','infobook');?></legend>
			<label for="Mark Type"><?php print_string('mark',$book);?></label>
			<?php print $marks[$copyto]['topic'].' ('. display_date($marks[$copyto]['entrydate']). ')';?>
		  </fieldset>
<?php
		}
?>
	<input type="hidden" name="mid0" value="<?php print $marks[$copyfrom]['id']; ?>"/>
	<input type="hidden" name="mid1" value="<?php print $marks[$copyto]['id']; ?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	<input type="hidden" name="cancel" value="<?php print $choice;?>"/>
		</form>
	  </div>
<?php
	}
else{

	$mid=$checkmids[0];
	$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
	$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);
	$markdefname=$mark['def_name'];
	$marktype=$mark['marktype'];
	$scoretypes=array();
	/*This is a horrible hack. Solves the problem (as things work now)
	  that dependent columns have no real markdef of their own - so we'll guess!
	*/
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
	elseif($marktype=='average'){
		$scoretypes[]='percentage'; 
		$scoretypes[]='value';$scoretypes[]='grade';
		}
	/*End horible hack.*/

	for($c7=0;$c7<sizeof($cids);$c7++){
		$cid=$cids[$c7];
		$d_subject=mysql_query("SELECT DISTINCT subject_id FROM class WHERE id='$cid'");
		}

	$markdefs=array();
    while($subject=mysql_fetch_array($d_subject,MYSQL_ASSOC)){
		$newbid=$subject['subject_id'];
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
		<form id="formtoprocess" name="formtoprocess" novalidate   
		  method="post" action="<?php print $host;?>"> 

		  <fieldset class="center">
			<legend><?php print_string('detailsofthenewmark',$book);?></legend>
			<label for="Mark Type"><?php print_string('thetypeofmark',$book);?></label>
			<select class="required" id="Mark Type" name="def_name" tabindex="1" >
<?php
			for($c=0;$c<sizeof($markdefs);$c++){
				print '<option ';
				print ' value="'.$markdefs[$c]['name'].'"';
				if($markdefname==$markdefs[$c]['name']){print ' selected="selected" ';}
				print '>'.$markdefs[$c]['name'].'</option>';
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
			  value="<?php print $mark['topic'];?>" size="20" 
			  maxlength="38"  pattern="alphanumeric" />

			  <label for="Comment"><?php print_string('optionalcomment',$book);?></label>
			  <input type="text" id="Comment" name="comment" value="<?php print
				$mark['comment']; ?>" size="40" maxlength="98" pattern="alphanumeric" />
		  </fieldset>

<?php if($scoretypes[0]=='percentage' or $scoretypes[0]=='value'){
?>
		  <fieldset class="center">
			<legend><?php print_string('percentage',$book);?></legend>

			<div class="left">
			  <label for="Total"><?php print_string('outoftotal',$book);?></label>
			  <input type="text" id="Total" name="total" value="<?php print $mark['total']; ?>" 
					 size="3" maxlength="4" pattern="integer" />
			</div>
			<div class="right">
<?php 
			  $checkname='scale'; $checkcaption='Scale old values?'; 
			  include('scripts/check_yesno.php');
?>
			</div>
		  </fieldset>
<?php	}
?>
	<input type="hidden" name="mid" value="<?php print $mid; ?>"/>
	<input type="hidden" name="oldtotal" value="<?php print $mark['total']; ?>"/>
	<input type="hidden" name="marktype" value="<?php print $mark['marktype']; ?>"/>
	<input type="hidden" name="lena" value="<?php print $mark['levelling_name']; ?>"/>	
	<input type="hidden" name="midlist" value="<?php print $mark['midlist']; ?>"/>
	<input type="hidden" name="current" value="<?php print $action;?>"/>
	<input type="hidden" name="choice" value="<?php print $choice;?>"/>
	<input type="hidden" name="cancel" value="<?php print $choice;?>"/>
		</form>
	  </div>
<?php
	}
?>