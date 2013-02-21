<?php
/**									student_view_sen1.php
 */

$action='student_view_sen.php';

include('scripts/sub_action.php');

$SEN=fetchSEN($sid);
$senhid=$SEN['id_db'];

include('seneeds/sen_view_action.php');

?>
