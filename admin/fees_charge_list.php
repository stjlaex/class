<?php
/**                                  fees_charge_list.php
 *
 *
 */

$action='fees_charge_list_action.php';

include('scripts/sub_action.php');

if((isset($_POST['conceptid']) and $_POST['conceptid']!='')){$conceptid=$_POST['conceptid'];}else{$conceptid=-1;}
if((isset($_GET['conceptid']) and $_GET['conceptid']!='')){$conceptid=$_GET['conceptid'];}
if((isset($_POST['comids']) and $_POST['comids']!='')){$comids=(array)$_POST['comids'];}else{$comids=array();}
if((isset($_GET['comids']) and $_GET['comids']!='')){$comids=(array)$_GET['comids'];}
if((isset($_POST['comids']) and $_POST['comids']!='')){$comids=(array)$_POST['comids'];}else{$comids=array();}
if((isset($_GET['comids']) and $_GET['comids']!='')){$comids=(array)$_GET['comids'];}

$students=array();
if(sizeof($comids)>0){
	foreach($comids as $comid){
		$com=get_community($comid);
		$coms[]=$com;
		$students=array_merge(listin_community($com),$students);
		}
	}

$extrabuttons=array();
/*
$extrabuttons['changes']=array('name'=>'current',
							   'pathtoscript'=>$CFG->sitepath.'/'.$CFG->applicationdirectory.'/admin/',
							   'value'=>'transport_print.php',
							   'xmlcontainerid'=>'changes',
							   'onclick'=>'checksidsAction(this)');
*/
three_buttonmenu($extrabuttons,$book);

$charges=(array)list_concept_fees($conceptid);
$Concept=fetchConcept($conceptid);
$Tarifs=(array)$Concept['Tarifs'];
$tarifs=array();
//$tarifs[0]='notarif';
foreach($Tarifs as $Tarif){
	$tarifs[$Tarif['id_db']]=$Tarif['Name']['value'];
	print '<input type="hidden" id="'.$Tarif['id_db'].'" value="'.$Tarif['Amount']['value'].'">';
	}
print '<input type="hidden" id="0" value="0">';

$total=0;
foreach($students as $student){
	$sid=$student['id'];
	if(array_key_exists($sid,$charges)){$total+=$charges[$sid][0]['amount'];}
	}

?>
  <div id="heading">
	<label><?php print_string('fees',$book);?></label>
  </div>

  <div id="viewcontent" class="content">
  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host; ?>" >

	<div class="center">
	  <table class="listmenu sidtable">
		<caption><?php print get_string($com['type'],$book).': '.$com['name'];?></caption>
		<thead>
		  <tr>
			<th colspan="4">&nbsp;</th>
			<th>
<?php 
		$tab=1;
		$listlabel='concept';
		$liststyle='width:240px;font-size:9pt;';
		$listname='conceptid';
		$onchange='yes';
		$concepts=list_concepts();
		include('scripts/set_list_vars.php');
		list_select_list($concepts,$listoptions,$book);

		print '<div style="float:right;margin:5px;">'.get_string('total',$book).': '.$total.'</div>';
		print '<div style="float:right;margin:5px;">'.get_string('total',$book).' '.get_string('preview',$book).': <span id="totalpreview">'.$total.'</span></div>';
?>
<?php

		if($conceptid>0){
			print '</th></tr><tr><th colspan="4">&nbsp;</th><th>';

?>
		<div class="center">
		  <p>Set all students to this tarif.</p>
<?php
			$listlabel='notarif';
			$liststyle='font-size:9pt;';
			$listname='floodtarifid';
			/*Improve this inline onchange*/
			$onchangeaction="totalvalue=parseFloat(document.getElementById('totalpreview').innerHTML); totalvalue=document.getElementsByName('sids[]').length*parseFloat(document.getElementById(this.value).value); document.getElementById('totalpreview').innerHTML=totalvalue; $(this).blur();";
			include('scripts/set_list_vars.php');
			list_select_list($tarifs,$listoptions,$book);
?>
			<?php include('scripts/check_yesno.php');?>
		</div>
<?php
			}
?>
			</th>
		  </tr>
		</thead>
<?php
	$rown=1;
	$tarifs[0]='';
	ksort($tarifs);
	foreach($students as $student){
		$sid=$student['id'];

		print '<tr id="sid-'.$sid.'">';
		print '<td>'.'<input type="checkbox" name="sids[]" value="'.$sid.'" />'.$rown++.'</td><td></td>';
		print '<td class="student"><a target="viewinfobook" onclick="parent.viewBook(\'infobook\');" href="infobook.php?current=student_fees.php&cancel=student_view.php&sids[]='.$sid.'&sid='.$sid.'">'.$student['surname'].', '. $student['forename'].'</a></td>';
		print '<td>'.$student['form_id'].'</td>';
		print '<td>';

		${'tarifid'.$sid}=0;
		if(array_key_exists($sid,$charges)){

			/*
			$student_charges=$charges[$sid];
			foreach($student_charges as $charge){
				${'tarifid'.$sid}=$charge['tarif_id'];
				}
			*/

			${'tarifid'.$sid}=$charges[$sid][0]['tarif_id'];
			}

		$listlabel='';
		$liststyle='font-size:9pt;';
		$listname='tarifid'.$sid;
		/*Improve this onchange: onfocus needed for previous value*/
		$onchangeaction="currentvalue=parseFloat(document.getElementById(this.value).value); totalvalue=parseFloat(document.getElementById('totalpreview').innerHTML); console.log(previousvalue); totalvalue=(totalvalue-previousvalue)+currentvalue; document.getElementById('totalpreview').innerHTML=totalvalue; $(this).blur();\" onfocus=\"previousvalue=parseFloat(document.getElementById(this.value).value);";
		include('scripts/set_list_vars.php');
		list_select_list($tarifs,$listoptions,$book);

		print '</td>';
		}
?>
	  </table>
	</div>

<?php
foreach($comids as $comid){
	print '<input type="hidden" name="comids[]" value="'.$comid.'" />';
	}
?>
	<input type="hidden" name="conceptid" value="<?php print $conceptid;?>" />
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
  </form>

  </div>
