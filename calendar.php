<?php 
/**												calendar.php
 *
 *	This is the hostpage for the calendar
 *
 */

$host='calendar.php';
$book='calendar';

include ('scripts/head_options.php');

$entrypage='embed';
$user=get_user($tid);
$externalparams=array(
					  'src' => $CFG->calendarsrc,
					  'ctz' => $CFG->timezone
					  );

/*construct the redirect string*/
$externalred=$CFG->calendarsite . '/'.$entrypage;


while(list($param,$value)=each($externalparams)){
	if(!isset($joiner)){$joiner='?';}
	else{$joiner='&';}
	$externalred=$externalred . $joiner . $param . '=' . $value;
	}
?>

  <div style="visibility:hidden;" id="hiddenbookoptions">
	<fieldset class="calendar">
	  <legend><?php print_string('options');?></legend>
	</fieldset>
  </div>

  <div id="bookbox" class="calendarcolor">
	<iframe id="externalbook" name="externalbook" class="externalbookframe"></iframe>
  </div>

<?php
include('scripts/end_options.php');
?>
<script>frames["externalbook"].location.href="<?php print $externalred;?>";</script>
