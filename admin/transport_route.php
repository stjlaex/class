<?php
/**                                  transport_route.php
 *
 *
 */

$action='transport_route_action.php';

include('scripts/sub_action.php');

if((isset($_POST['busname']) and $_POST['busname']!='')){$busname=$_POST['busname'];}else{$busname='';}
if((isset($_GET['busname']) and $_GET['busname']!='')){$busname=$_GET['busname'];}


$extrabuttons=array();
three_buttonmenu($extrabuttons,$book);


	if($busname!=-1){
?>
  <div id="heading">
	<label><?php print_string('transport',$book);?></label>
<?php	print $busname;?>
  </div>
<?php
		}
?>
  <div id="viewcontent" class="content">

  <form id="formtoprocess" name="formtoprocess" novalidate method="post" action="<?php print $host; ?>" >

<?php
	$rown=1;
	$directions=array('I','O');
	foreach($directions as $direction){
		$bus=(array)get_bus('',$busname,$direction,'%');
		$stops=(array)list_bus_stops($bus['id']);
		$time=strtotime($bus['departuretime']);
		if($direction=='I'){$title='AM: '.$bus['name'];}
		else{$title='PM: '.$bus['name'];}
?>

	<div class="center">
	  <table class="listmenu sidtable">
		<caption><?php print $title;?></caption>
		<thead>
		  <tr>
			<th colspan="2" style="width:80%;">
			<label for="time"><?php print get_string('departuretime',$book);?>
			<input id="time<?php print $direction;?>" maxlength="8" type="time" tabindex="'.$tab++.'" name="deptime<?php print $direction;?>" value="<?php print $bus['departuretime'];?>" />
			</th>
			<th><?php print get_string('traveltime',$book);?></th>
			<th>&nbsp;</th>
		  </tr>
		</thead>

<?php
		$stopno=0;
		$stops[]=array('id'=>'0','name'=>'','traveltime'=>'');
		$stops[]=array('id'=>'0','name'=>'','traveltime'=>'');
	    $laststop=count($stops)+2;
		print '<input type="hidden" name="last'.$direction.'" value="'.$laststop.'" />';
		foreach($stops as $stop){
			$stopno++;
			print '<tr>';

			if($stop['traveltime']!=''){$time=strtotime('+'.$stop['traveltime'].' minutes', $time);}

			print '<td><input type="hidden" name="stids'.$direction.'[]" value="'.$stop['id'].'" />';
			print '<select name="sequences'.$direction.'[]">';
			print '<option value=""></option>';
			for($no=1;$no<($laststop+10);$no++){
				/* Sets the sequence number */
?>
			  <option value="<?php print $no;?>" <?php if($stopno==$no){print 'selected="selected"';}?>>
				<?php print $no;?>
			  </option>
<?php
				}
			print '</select>';
			print '</td>';
			print '<td style="width:70%;"><input maxlength="120" type="text" tabindex="'.$tab++.'" name="names'.$direction.'[]" value="'.$stop['name'].'"  /></td>';
			print '<td><input pattern="decimal" maxlength="2" type="text" tabindex="'.$tab++.'" name="times'.$direction.'[]" value="'.$stop['traveltime'].'" /></td>';
			print '<td>'.date('H:i',$time).'</td>';
			print '</tr>';
			}
?>
	  </table>
	</div>
<?
		}
?>


	<input type="hidden" name="busname" value="<?php print $busname;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

