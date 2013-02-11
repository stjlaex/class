<?php 
/**												seneeds.php
 *	This is the hostpage for the seneeds.
 */

$host='seneeds.php';
$book='seneeds';

include('scripts/head_options.php');
include('scripts/set_book_vars.php');
$session_vars=array('sid','senhid','sentype','newyid','sensupport');
include('scripts/set_book_session_vars.php');

$list='';
if(isset($_POST['list']) and $_POST['list']=='all'){
	$sentype='';
	$newyid='';
	$sensupport='';
	$list='all';
	$_SESSION['seneedssid']='';
	$_SESSION['seneedssenhid']='-1';
	$sid='';
	$senhid=-1;
	}
if($sid=='' or $current==''){
	$current='sen_student_list.php';
	$_SESSION['seneedssid']='';
	$_SESSION['seneedssenhid']='-1';
	$sid='';
	$senhid=-1;
	}
elseif($sid!=''){
	/*working with a single student*/
	$Student=fetchStudent($sid);
	if($senhid==-1000){
		$senhid=set_student_senstatus($sid);
		}
	$SEN=(array)fetchSEN($sid,$senhid);
	$senhid=$SEN['id_db'];
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
<?php 
	 if(!isset($sid) or $sid==''){
		  $enum=array_merge(getEnumArray('sentypeinternal'),getEnumArray('sentype'));
?>
	<form id="seneedschoice" name="seneedschoice" method="post" action="seneeds.php" target="viewseneeds">
	  <fieldset class="seneeds">
		<legend><?php print_string('filterlist',$book);?></legend>
		<label for="Type"><?php print_string('sentype',$book);?></label>
		<select id="Type" name="sentype" onChange="document.<?php print $book;?>choice.submit();">
		  <option value=""></option>
<?php
		 foreach($enum as $inval => $description){	
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
?>
	  </fieldset>
	</form>
<?php
		  }
	  else{

		  $senhistories=(array)list_student_senhistories($sid);
?>
	<form id="seneedschoice" name="seneedschoice" method="post" action="seneeds.php" target="viewseneeds">
	  <fieldset class="seneeds selery">
		<legend><?php print_string('records','admin');?></legend>
<?php

	     foreach($senhistories as $no => $senhistory){
			 if($senhid==$senhistory['id']){$displayclass=' class="solery hilite" ';}
			 else{$displayclass=' class="solery lolite" ';}
?>
			   <a href="seneeds.php?current=sen_view.php&sid=<?php print $sid;?>&senhid=<?php print $senhistory['id'];?>" target="viewseneeds" onclick="parent.viewBook('seneeds');">
				 <div <?php print $displayclass;?>>
				   <?php print '&nbsp;'.display_date($senhistory['startdate']);?>
				 </div>
			   </a>
<?php
			 }
		 if(strtotime($senhistory['reviewdate']) <= mktime() and $senhistory['reviewdate']!=''){
			 /* If the last IEP's reviewdate has past then allow option to create to a new one. */
?>
			   <a  class="lolite" href="seneeds.php?current=sen_view.php&sid=<?php print $sid;?>&senhid=-1000" target="viewseneeds" onclick="parent.viewBook('seneeds');">
				 <div class="solery lolite"><?php print ' '.get_string('new');?></div>
			   </a>
<?php
			 }
?>
	  </fieldset>
	</form>

<?php
		  }
?>
	<br />
	<form id="configseneedschoice" name="configseneedschoice" method="post" action="seneeds.php" target="viewseneeds">
	  <fieldset class="seneeds selery">
		<legend><?php print get_string('list','admin');?></legend>
<?php 
		$choices=array('sen_student_list.php'=>'allstudents');
		selery_stick($choices,'',$book);
?>
		<input type="hidden" name="list" value="all"/>
	  </fieldset>
	</form>



  </div>
<?php
include('scripts/end_options.php');
?>