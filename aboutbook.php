<?php 
/**														aboutbook.php
 *	This is the hostpage for the aboutbook
 *	The page to be included is set by $current
 *	A preselected menu option is set by $choice
 */

$host='aboutbook.php';
$book='aboutbook';
if(isset($_GET['subtype']) and $_GET['subtype']!=""){$subtype=$_GET['subtype'];}else{$subtype="";}

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

<?php include('scripts/end_options.php');?>
