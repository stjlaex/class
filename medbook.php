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

if(isset($_POST['list']) and $_POST['list']=='all'){
	$medtype='';$newyid='';$list='all';$sid='';
	}
if($sid=='' or $current==''){
	$current='med_student_list.php';
	$_SESSION['medbooksid']='';
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
		selery_stick($choices,'',$book);
?>
		<input type="hidden" name="list" value="all"/>
	  </fieldset>
	</form>


  </div>
<?php
include('scripts/end_options.php');
?>