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
			<th colspan="2" style="width:80%;">&nbsp;</th>
			<th><?php print get_string('traveltime',$book);?></th>
			<th><?php print get_string('departuretime',$book);?></th>
		  </tr>
		</thead>

<?php
	    $laststop=count($stops);
		print '<input type="hidden" name="last'.$direction.'" value="'.$laststop.'" />';
		$stopno=0;
		$stops[]=array('name'=>'','traveltime'=>'');
		$stops[]=array('name'=>'','traveltime'=>'');
		foreach($stops as $stop){
			$stopno++;
			print '<tr>';

			if($stop['traveltime']!=''){$time=strtotime('+'.$stop['traveltime'].' minutes', $time);}

			print '<td><input type="hidden" name="stids[]" value="'.$stop['id'].'" />';
			print '<select name="sequences[]">';
			print '<option value=""></option>';
			for($no=1;$no<($laststop+10);$no++){
?>
			  <option value="<?php print $no;?>" <?php if($stopno==$no){print 'selected="selected"';}?>>
				<?php print $no;?>
			  </option>
<?php
				}
			print '</select>';
			print '</td>';
			print '<td><input maxlength="40" type="text" tabindex="'.$tab++.'" name="names[]" value="'.$stop['name'].'" style="width:30em;" /></td>';
			print '<td><input pattern="decimal" maxlength="2" type="text" tabindex="'.$tab++.'" name="times[]" value="'.$stop['traveltime'].'" /></td>';
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

