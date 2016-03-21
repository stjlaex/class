<?php
/**						list_session.php
 *
 */

if(!isset($listname)){$listname='session';}
if(!isset($listlabel)){$listlabel='session';}

include('scripts/set_list_vars.php');

$sessions=list_sessions();

list_select_list($sessions,$listoptions,$book);

unset($listoptions);
unset($sessions);
?>
