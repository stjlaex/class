<?php
/**									student_view_sen1.php
 */

$action='student_view_sen.php';

include('scripts/sub_action.php');

$SEN=fetchSEN($sid);

include('seneeds/sen_view_action.php');

?>
