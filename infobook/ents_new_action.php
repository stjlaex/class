<?php
/**									ents_new_action.php
 *
 */

$action='ents_list.php';
$action_post_vars=array('tagname');

$entid=$_POST['id_db'];
$tagname=$_POST['tagname'];
$detail=clean_text($_POST['detail']);
$entrydate=$_POST['entrydate'];
if(isset($_POST['bid'])){$bid=$_POST['bid'];}else{$bid='%';}
if(isset($_POST['catid'])){$catid=$_POST['catid'];}else{$catid='';}
if(isset($_POST['ratvalue'])){$ratvalue=$_POST['ratvalue'];}else{$ratvalue='';}
if(isset($_POST['newyid'])){$newyid=$_POST['newyid'];}else{$newyid='';}

include('scripts/sub_action.php');

if($sub=='Submit'){
	$d_c=mysql_query("SELECT subtype FROM categorydef WHERE type='ent' AND name='$tagname';");
	$type=mysql_result($d_c,0);
	if($bid=='' OR $bid=='%'){$bid='General';}
	$category=$catid.':'.$ratvalue.';';


	if($entid>0){
		mysql_query("UPDATE background SET 
						detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid',
						subject_id='$bid', category='$category', teacher_id='$tid' WHERE id='$entid';");
		}
	else{
		mysql_query("INSERT INTO background SET student_id='$sid', type='$type',
					detail='$detail', entrydate='$entrydate', yeargroup_id='$newyid', subject_id='$bid',
					category='$category', teacher_id='$tid';");
		$entid=mysql_insert_id();

		require_once('lib/eportfolio_functions.php');
		link_files($Student['EPFUsername']['value'],'background',$entid);

		}
	}

include('scripts/redirect.php');
?>
