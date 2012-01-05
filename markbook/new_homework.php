<?php
/**									new_homework.php
 *
 */

$action='new_homework_action.php';

if($umnfilter!='%'){$umnfilter='hw';$_SESSION['umnfilter']=$umnfilter;}

include('scripts/sub_action.php');

if(isset($_GET['hwid'])){$hwid=$_GET['hwid'];}else{$hwid=-1;}
if(isset($_POST['hwid']) and $_POST['hwid']!=''){$hwid=$_POST['hwid'];}

if($hwid>-1){
	/*editing a pre-existing*/
	$HomeworkDef=fetchHomeworkDefinition($hwid);
	}
else{
	$HomeworkDef=fetchHomeworkDefinition(-1);
	}
	$extrabuttons=array();

	/* Can use cid 0 because the homework button only accessible when one
	 * class is in the marktable. 
	 */
	$cid=$cids[0];
	$d_cridbid=mysql_query("SELECT subject_id, course_id, stage 
						   		FROM class WHERE id='$cid'");
	$bid=mysql_result($d_cridbid,0,0);
	$crid=mysql_result($d_cridbid,0,1);
	$stage=mysql_result($d_cridbid,0,2);


   	$d_markdef=mysql_query("SELECT name, comment FROM markdef WHERE 
					 ORDER BY subject_id");
	$d_hw=mysql_query("SELECT id, title AS name FROM homework WHERE
					(component_id LIKE '$pid' OR component_id='%') AND
					(subject_id LIKE '$bid' OR subject_id='%') AND
					(stage LIKE '$stage' OR stage='%') AND
					(course_id LIKE '$crid' OR course_id='%') ORDER BY title;");
?>
  <div id="heading">
	<form id="headertoprocess" name="headertoprocess" 
					method="post" action="<?php print $host;?>">
<?php
	$listname='hwid';$listlabel='';
	include('scripts/set_list_vars.php');
	list_select_db($d_hw,$listoptions,$book);
	$button['existinghomework']=array('name'=>'sub','value'=>'Link');
	all_extrabuttons($button,'markbook','processHeader(this)');
?>
 	<input type="hidden" name="current" value="<?php print $current;?>">
 	<input type="hidden" name="cancel" value="<?php print $cancel;?>">
 	<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>
<?php
	three_buttonmenu($extrabuttons,$book);
?>

  <div class="topform divgroup">
	<form id="formtoprocess" name="formtoprocess" 
					method="post" action="<?php print $host;?>">
	  <div class="left">
		<?php $tab=xmlarray_divform($HomeworkDef,'','',$tab,$book); ?>
	  </div>

	  <div class="right">
		<label><?php print_string('dateset',$book);?></label>
		<?php $xmldate='Dateset'; $required='yes'; 
			 include('scripts/jsdate-form.php');?>

		<label><?php print_string('datedue',$book);?></label>
		<?php $xmldate='Datedue'; $required='yes'; 
		/* default to one week hence for collecting homework */
		$time=mktime(0,0,0,date('n'),date('j')+7,date('Y'));
		$todate=date('Y-m-j',$time);
		include('scripts/jsdate-form.php');?>
	  </div>

	  <div class="right">
<?php 
  		$d_markdef=mysql_query("SELECT name AS id,
					CONCAT(name,' (',comment,')') AS name FROM markdef WHERE 
					(subject_id LIKE '$bid' OR subject_id='%') AND
					(course_id LIKE '$crid' OR course_id='%') ORDER BY subject_id;");
		$required='yes';$liststyle='width:90%;';
		$listswitch='yes';
		$listname='defname';$listlabel='thetypeofmark';
		$seldefname=$HomeworkDef['Markdef']['value'];
		include('scripts/set_list_vars.php');
		list_select_db($d_markdef,$listoptions,$book);
?>
	  </div>

		<div id="switchDefname" class="right">
		</div>


	    <input type="hidden" name="hwid" value="<?php print $hwid;?>">
	    <input type="hidden" name="crid" value="<?php print $crid;?>">
	    <input type="hidden" name="bid" value="<?php print $bid;?>">
	    <input type="hidden" name="newpid" value="<?php print $pid;?>">
	    <input type="hidden" name="stage" value="<?php print $stage;?>">
		<input type="hidden" name="current" value="<?php print $action;?>">
		<input type="hidden" name="cancel" value="<?php print $choice;?>">
		<input type="hidden" name="choice" value="<?php print $choice;?>">
	</form>
  </div>

  <div class="content">
	<div class="center">
	  <table class="listmenu" name="listmenu">
		<caption><?php print_string('assessments');?></caption>
		<thead>
		  <tr>
			<th></th>
			<th><?php print_string('dateset',$book);?></th>
			<th><?php print_string('datedue',$book);?></th>
			<th><?php print_string('title',$book);?></th>
		  </tr>
		</thead>
<?php

   	$d_hw=mysql_query("SELECT midlist AS id, entrydate AS datedue,
				comment AS dateset FROM mark 
				JOIN midcid ON mark.id=midcid.mark_id
				WHERE midcid.class_id='$cid' AND marktype='hw' 
				ORDER BY mark.entrydate DESC");
	while($hw=mysql_fetch_array($d_hw,MYSQL_ASSOC)){
	    unset($HomeworkDef);
		$oldhwid=$hw['id'];
		$HomeworkDef=fetchHomeworkDefinition($oldhwid);
		$rown=0;
?>
		<tbody id="<?php print $oldhwid;?>">
		  <tr class="rowplus"  
			onClick="clickToReveal(this)" id="<?php print $oldhwid.'-'.$rown++;?>">
			<th>&nbsp</th>
			<td><?php print $hw['dateset']; ?></td>
			<td><?php print $hw['datedue']; ?></td>
			<td><?php print $HomeworkDef['Title']['value']; ?></td>
		  </tr>
		  <tr class="hidden" id="<?php print $oldhwid.'-'.$rown++;?>">
			<td colspan="4">
			  <p>
				<?php print $HomeworkDef['Description']['value']; ?>
				<?php print $HomeworkDef['References']['value']; ?>
			  </p>
			</td>
		  </tr>
		</tbody>
<?php
		}
?>
	  </table>
	</div>

  </div>

  <div id="switchDefnametest percent" class="hidden">
	<label for="Total"><?php print_string('outoftotal',$book);?></label>
	<input class="required" type="text" id="Total" name="total" 
		   maxlength="4" pattern="integer" />
  </div>
