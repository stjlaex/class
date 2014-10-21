<?php
/**							    import_profiles_action.php
 */

$action="import_profiles.php";
$cancel="import_profiles.php";

$action_post_vars=array('curryear');
include('scripts/sub_action.php');

$curryear=get_curriculumyear();

if($_POST['separator']=='semicolon'){$separator=';';}else{$separator=',';}

if($sub=='Submit'){
	$fname=$_FILES['importfile']['tmp_name'];
	$ext=pathinfo($_FILES['importfile']['name'], PATHINFO_EXTENSION);
	$data=array();
	$headers=array();
	if($fname!='' and $ext=='csv'){
		include('scripts/file_import_csv.php');
		if(count($inrows)>0){
			foreach($inrows as $rowno=>$inrow){
					foreach($inrow as $colno=>$invalue){
						if($rowno==0){
							if(strpos($invalue,'categorydef')!==false){
								$table='categorydef';
								$field=preg_replace('/categorydef_/','',$invalue);
								}
							elseif(strpos($invalue,'report')!==false){
								$table='report';
								$field=preg_replace('/report_/','',$invalue);
								}
							elseif(strpos($invalue,'statement')!==false){
								$table='report_skill';
								$field=preg_replace('/statement_/','',$invalue);
								}
							$headers[$colno]=array('table'=>$table,'field'=>$field);
							}
						else{
							if(count($headers)>0){
								$data[$headers[$colno]['table']][$rowno][$headers[$colno]['field']]=$invalue;
								if($headers[$colno]['table']=='categorydef' and $headers[$colno]['field']=='name'){
									$profiles[$invalue]=$invalue;
									}
								if($headers[$colno]['table']=='report' and $headers[$colno]['field']=='title'){
									$reports[$invalue]=$invalue;
									}
								}
							}
						}
				}
			foreach($data['categorydef'] as $rowno=>$profile){
				$d_p=mysql_query("SELECT * FROM categorydef WHERE name='".$profile['name']."' AND type='pro';");
				if(mysql_num_rows($d_p)==0){
					mysql_query("INSERT INTO categorydef SET name='".$profile['name']."', type='pro';");
					$profid=mysql_insert_id();
					foreach($profile as $field=>$inval){
						if($field!='id' and $field!='type' and $field!='name'){
							mysql_query("UPDATE categorydef SET $field='$inval' WHERE id='$profid';");
							}
						}
					}
				else{$profid=mysql_result($d_p,0,'id');}
				$profids[$profile['name']]=$profid;
				}
			$date=date('Y-m-d');
			foreach($data['report'] as $rowno=>$report){
				$d_r=mysql_query("SELECT * FROM report WHERE title='".$report['title']."';");
				if(mysql_num_rows($d_r)==0){
					mysql_query("INSERT INTO report SET title='".$report['title']."', date='$date', 
									deadline='$date', type='profile', year='$curryear';");
					$rid=mysql_insert_id();
					foreach($report as $field=>$inval){
						if($field!='id' and $field!='type' and $field!='title'){
							mysql_query("UPDATE report SET $field='$inval' WHERE id='$rid';");
							}
						}
					$profid=$profids[$data['categorydef'][$rowno]['name']];
					mysql_query("INSERT INTO ridcatid SET report_id='$rid', categorydef_id='$profid', subject_id='profile';");
					}
				else{$rid=mysql_result($d_r,0,'id');}
				$rids[$report['title']]=$rid;
				}
			foreach($data['report_skill'] as $rowno=>$statement){
				$statement['name']=preg_replace('/\"/',"'",$statement['name']);
				mysql_query('INSERT INTO report_skill SET name="'.clean_text($statement['name']).'";');
				$skid=mysql_insert_id();
				foreach($statement as $field=>$inval){
					if($field!='id' and $field!='profile_id' and $field!='name'){
						mysql_query("UPDATE report_skill SET $field='$inval' WHERE id='$skid';");
						}
					}
				$rid=$rids[$data['report'][$rowno]['title']];
				mysql_query("UPDATE report_skill SET profile_id='$rid' WHERE id='$skid';");
				$skids[]=$skid;
				}
			}
		else{
			$error[]='Empty file';
			}
		}
	else{
		$error[]='Invalid file extension '.$ext;
		}

	if(count($error)>0){
		$result=array();
		}
	}
include('scripts/results.php');
include('scripts/redirect.php');
?>
