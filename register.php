<?php 
/**		   											register.php
 *	This is the hostpage for the register
 */

$host='register.php';
$book='register';
$current='register_list.php';
$choice='register_list.php';
$action='';
$cancel='';

include('scripts/head_options.php');

if(!isset($_SESSION['registergroup'])){$_SESSION['registergroup']='';}

if(isset($_SESSION['registercurrent'])){$current=$_SESSION['registercurrent'];}
if(isset($_SESSION['registerchoice'])){$choice=$_SESSION['registerchoice'];}
if(isset($_POST['current'])){$current=$_POST['current'];}
if(isset($_POST['choice'])){$choice=$_POST['choice'];}
if(isset($_POST['cancel'])){$cancel=$_POST['cancel'];}
if(isset($_GET['choice'])){$choice=$_GET['choice'];}
if(isset($_GET['cancel'])){$cancel=$_GET['cancel'];}
if(isset($_GET['current'])){$current=$_GET['current'];}

$community=$_SESSION['registergroup'];
if(isset($_POST['newfid'])){
	$community=array('id'=>'','type'=>'form','name'=>$_POST['newfid']);
	}
//elseif(isset($_POST['newyid'])){
//	$community=array('id'=>'','type'=>'year','name'=>$_POST['newyid']);
//	}
if(is_array($community)){
	if($community['type']=='form'){$fid=$community['name'];}
	elseif($community['type']=='year'){$yid=$community['name'];}
	elseif($community['type']=='reg'){$comid=$community['id'];}
	}
else{
	$pastorals=listPastoralRespon($respons);
	$rfids=$pastorals['forms'];
	$ryids=$pastorals['years'];
	if(sizeof($rfids)!=0){
		$fid=$rfids[0];
		$community=array('id'=>'','type'=>'form','name'=>$fid);
		}
//	elseif(sizeof($ryids)!=0 and $fid==''){
//		$yid=$ryids[0];
//		$community=array('id'=>'','type'=>'year','name'=>$yid);
//		}
	else{
		$current='';
		$choice='';
		$community='';
		}
	}
?>
  <div id="bookbox" class="registercolor">
<?php
	if($current!=''){
		$currentevent=currentEvent();
		$_SESSION['registercurrent']=$current;
		$_SESSION['registerchoice']=$choice;
		$_SESSION['registergroup']=$community;
		$view='register/'.$current;
		include($view);
		}
	else{
?>
	<div class="content">
	  <fieldset class="center">
	  </fieldset>
	</div>
<?php
		}
?>
  </div>

  <div style="visibility:hidden;" id="hiddenbookoptions" class="bookoptions">	

	<form id="registerchoice" name="registerchoice" method="post" 
							action="register.php" target="viewregister">

	<fieldset class="register">
	  <legend><?php print_string('currentsession',$book);?></legend>
	  <label>
		  <?php print get_string('registrationsession',$book).': '.$currentevent['period'];?><br />
		  <?php print $currentevent['date'];?><br />
			<?php print date('H:i:s');?>
		</label>

	</fieldset>

	  <fieldset class="register">
		<legend><?php print_string('registrationgroups',$book);?></legend>
<?php
		$onsidechange='yes'; include('scripts/list_form.php'); 		
//		$onsidechange='yes'; include('scripts/list_year.php');
?>
	  </fieldset>
	</form>
  </div>
<?php
	  include('scripts/end_options.php'); 
?>
