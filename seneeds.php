<?php 
/**												seneeds.php
 *	This is the hostpage for the seneeds.
 */

$host='seneeds.php';
$book='seneeds';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('sid','sentype','newyid','sensupport');
include('scripts/set_book_session_vars.php');

if($sid=='' or $current==''){
	$current='sen_student_list.php';
	$_SESSION['seneedssid']='';
	}
elseif($sid!=''){
	/*working with a single student*/
	$Student=fetchStudent($sid);
	$SEN=$Student['SEN'];
	}
?>
  <div id="bookbox" class="seneedscolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
		}
?>
  </div>


  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">
	<form id="seneedschoice" name="seneedschoice" method="post" 
		action="seneeds.php" target="viewseneeds">

<?php 
	  if($sid==''){
		  $enum=getEnumArray('sentype');
?>
	  <fieldset class="seneeds">
		<legend><?php print_string('filterlistbyneeds',$book);?></legend>
		<label for="Type"><?php print_string('sentype',$book);?></label>
		<select id="Type" name="sentype" 
		  onChange="document.<?php print $book;?>choice.submit();">
		  <option value=""></option>
<?php
		  while(list($inval,$description)=each($enum)){	
			  print '<option ';
			  if($sentype==$inval){print 'selected="selected" ';}
			  print ' value="'.$inval.'">'.get_string($description,$book).'</option>';
			  }
?>
		</select>
<?php
		  $onsidechange='yes'; 
		  include('scripts/list_year.php');

		  $listname='sensupport';
		  $listlabel='extrasupport';
		  $cattype='sen';
		  $onsidechange='yes';
		  include('scripts/list_category.php');
		  }
?>

	  </fieldset>
	</form>
  </div>
<?php
include('scripts/end_options.php');
?>