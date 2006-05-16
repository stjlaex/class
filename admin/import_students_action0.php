<?php 
/**									   			import_students_action0.php
 *	Reads the import file into an array.
 */

$action='import_students_action1.php';

include('scripts/sub_action.php');

if($sub=='Submit'){

	include('scripts/file_import_csv.php');

	/*to be used by the other action pages*/
	if(sizeof($inrows>0)){
		$_SESSION{'instudents'}=$inrows;
		$idef=array();
		$_SESSION{'idef'}=$idef;
		$_SESSION{'nofields'}=$nofields;
		}
	}

include('scripts/results.php');
include('scripts/redirect.php');
?>
