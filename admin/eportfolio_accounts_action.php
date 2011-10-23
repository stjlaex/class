<?php
/**				   				eportfolio_accounts_action.php
 *
 */

$choice='eportfolio_accounts.php';
$cancel='eportfolio_accounts.php';
require_once('lib/eportfolio_functions.php');

include('scripts/sub_action.php');

$refresh=$_POST['refresh0'];
$staffcheck=$_POST['staffcheck0'];
$studentblank=$_POST['studentblank0'];
$photocheck=$_POST['photocheck0'];
$contactcheck=$_POST['contactcheck0'];
$contactblank=$_POST['contactblank0'];

if($refresh=='yes'){
	/* This empties content and relationships but leaves accounts as
	 *  they are.
	 */
	elgg_refresh();
	}


if($studentblank=='yes'){
	/* Clear out all students ready to regenerate them. 
	 * Everything to do with each account is lost.
	 */
	elgg_blank('Default_Student');
	$result[]='Students\' accounts blanked.';
	}

if($contactblank=='yes'){
	/* Clear out all contacts ready to regenerate them. 
	 * Everything to do with each account is lost.
	 */
	elgg_blank('Default_Guardian');
	$result[]='Contacts\' accounts blanked.';
	}



include('scripts/results.php');
include('scripts/redirect.php');
?>
