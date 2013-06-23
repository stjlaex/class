<?php
/**							student_view_action.php
 *
 */

$action='student_list.php';
$action_post_vars=array('sid','sids');


include('scripts/sub_action.php');
include('scripts/answer_action.php');

mysql_query("DELETE FROM student WHERE id='$sid' LIMIT 1;");
mysql_query("DELETE FROM info WHERE student_id='$sid' LIMIT 1;");
mysql_query("DELETE FROM gidisd WHERE student_id='$sid';");
mysql_query("DELETE FROM comidsid WHERE student_id='$sid';");
mysql_query("DELETE FROM cidsid WHERE student_id='$sid';");
mysql_query("DELETE FROM cidsid WHERE student_id='$sid';");
mysql_query("DELETE FROM score WHERE student_id='$sid';");
mysql_query("DELETE FROM accomodation WHERE student_id='$sid';");
mysql_query("DELETE sencurriculum FROM sencurriculum JOIN senhistory ON senhistory_id=senhistory.id WHERE senhistory.student_id='$sid';");
mysql_query("DELETE FROM senhistory WHERE student_id='$sid';");
mysql_query("DELETE FROM sentype WHERE student_id='$sid';");
mysql_query("DELETE FROM incidents WHERE student_id='$sid';");
mysql_query("DELETE FROM comments WHERE student_id='$sid';");
mysql_query("DELETE FROM background WHERE student_id='$sid';");
mysql_query("DELETE FROM exclusions WHERE student_id='$sid';");
mysql_query("DELETE FROM merits WHERE student_id='$sid';");
mysql_query("DELETE FROM update_event WHERE student_id='$sid';");
mysql_query("DELETE FROM attendance WHERE student_id='$sid';");
mysql_query("DELETE FROM attendance_booking WHERE student_id='$sid';");
mysql_query("DELETE FROM reportentry WHERE student_id='$sid';");
mysql_query("DELETE FROM eidsid WHERE student_id='$sid';");
mysql_query("DELETE FROM report_event WHERE student_id='$sid';");
mysql_query("DELETE FROM report_skill_log WHERE student_id='$sid';");
mysql_query("DELETE FROM transport_booking WHERE student_id='$sid';");
mysql_query("DELETE FROM fees_charge WHERE student_id='$sid';");
mysql_query("DELETE FROM fees_applied WHERE student_id='$sid';");
mysql_query("DELETE FROM file WHERE owner_id='$sid' AND owner='s';");
mysql_query("DELETE FROM file_folder WHERE owner_id='$sid' AND owner='s';");

$key=array_search($sid,$sids);	
unset($sids[$key]);
$sid=-1;

include('scripts/redirect.php');	
?>
