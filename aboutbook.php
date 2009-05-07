<?php 
/**														aboutbook.php
 *	This is the hostpage for the aboutbook
 *	The page to be included is set by $current
 *	A preselected menu option is set by $choice
 */

$host='aboutbook.php';
$book='aboutbook';

include('scripts/head_options.php');

include('scripts/set_book_vars.php');

?>
  <div id="bookbox" class="aboutcolor">
<?php
	if($current!=''){
		include($book.'/'.$current);
		}
	else{
		include($book.'/about.php');
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions">	
	<form id="aboutchoice" name="aboutchoice" method="post" 
	  action="aboutbook.php" target="viewaboutbook">
	  <fieldset class="aboutbook selery">
		<legend><?php print_string('helpandsupport');?></legend>
<?php
	$choices=array('report_bug.php' => 'reportbug'
				   //,'request_feature.php' => 'requestfeature'
				   ,'support.php' => 'contactsupport'
				   );

	selery_stick($choices,$choice,$book);
?>
	  </fieldset>
<br />
<br />
<br />

	  <fieldset class="aboutbook selery">
		<legend><?php print_string('documentation',$book);?></legend>
<a href="http://laex.org/dokuwiki" target="_blank">
<?php
	$choices=array('usermanual.php' => 'usermanual'
				   );

	selery_stick($choices,$choice,$book);
?>
</a>
	  </fieldset>

	</form>
  </div>

<?php include('scripts/end_options.php');?>
