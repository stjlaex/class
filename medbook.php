<?php 
/**												medbook.php
 *	This is the hostpage for the medbook.
 */

$host='medbook.php';
$book='medbook';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('sid','newyid','medtype');
include('scripts/set_book_session_vars.php');

if(isset($_POST['list']) and ($_POST['list']=='all' or $_POST['list']=='new' or $_POST['list']=='visit' or $_POST['list']=='search')){
	$medtype='';$newyid='';$list=$_POST['list'];$sid='';
	}
if($sid=='' or $current==''){
	if($current==''){$current='';}
	$_SESSION['medbooksid']='';
	$_SESSION['searchstring']='';
	$_SESSION['time']='';
	}
elseif($sid!=''){
	/*working with a single student*/
	$Student=fetchStudent($sid);
	$Medical=fetchMedical($sid);
	}
?>
  <div id="bookbox" class="medbookcolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">
	<form id="medbookchoice" name="medbookchoice" method="post" 
		action="medbook.php" target="viewmedbook">

<?php 
	  if($sid==''){
?>
	  <fieldset class="medbook">
		<legend><?php print_string('filterlist',$book);?></legend>
<?php
		$onsidechange='yes';
		include('scripts/list_year.php');

		$listname='medtype';
		$listlabel='type';
		$cattype='med';
		$onsidechange='yes';
		include('scripts/set_list_vars.php');
		$d_catdef=mysql_query("SELECT subtype AS id, name FROM categorydef WHERE
								  type='med' ORDER BY rating DESC, name");
		list_select_db($d_catdef,$listoptions,$book);
?>
	  </fieldset>
	</form>
<?php
		  }
?>
	<form id="configmedbookchoice" name="configmedbookchoice" method="post" action="medbook.php" target="viewmedbook">
	  <fieldset class="medbook selery">
		<legend><?php print get_string('list','admin');?></legend>
<?php 
		$choices=array('med_student_list.php'=>'allstudents');
		selery_stick($choices,$choice1,$book);
?>
		<input type="hidden" name="list" value="all"/>
	  </fieldset>
	</form>

	<form id="configmedbookchoice" name="configmedbookchoice" method="post" action="medbook.php" target="viewmedbook">
	  <fieldset class="medbook selery">
	      <legend>&nbsp;</legend>
<?php 
		$choices=array('med_student_list.php'=>'newstudents');
		selery_stick($choices,$choice2,$book);
?>
		<input type="hidden" name="list" value="new"/>
	  </fieldset>
	</form>


	<form id="configmedbookchoice" name="configmedbookchoice" method="post" action="medbook.php" target="viewmedbook">
	  <fieldset class="medbook selery">
		<legend><?php print get_string('visits',$book);?></legend>
<?php 
		$choices=array('med_student_list.php'=>'visitstudents');
		selery_stick($choices,$choice3,$book);
?>
		<input type="hidden" name="list" value="visit"/>
	  </fieldset>
	</form>

	<form id="configmedbookchoice" name="configmedbookchoice" method="post" action="medbook.php?current=med_search_student.php" target="viewmedbook">
	  <fieldset class="medbook selery">
	      <legend>&nbsp;</legend>
<?php 
		$choices=array('med_search_student.php'=>'newvisit');
		selery_stick($choices,$choice,$book);
?>
		<input type="hidden" name="list" value="search"/>
	  </fieldset>
	</form>


  </div>
<?php
include('scripts/end_options.php');
?>
