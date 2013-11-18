<?php
/*
 *	Return ids for a search string given $searchstring
 *
 *	@param searchstring
 *	@param formid
 *	@param yeargroup
 *	@param table
 *
 *	@return array 
 *
 */ 
function search_student($searchstring,$formid='',$yeargroup='',$table='student'){
	/*It requires Perl Combinatorics*/
	require_once 'Math/Combinatorics.php';
	/*Sets locale to utf8 for last option in strings comparation (accented letters)*/
	setlocale(LC_ALL, "en_GB.UTF-8");
	if($searchstring!=''){
		/*Add yeargroup or formgroup to the mysql query*/
		if($yeargroup!=''){$yearg=" yeargroup_id LIKE '$yeargroup' ";}
		if($formid!=''){
			$d_f=mysql_query("SELECT name FROM community WHERE id='$formid';");
			$formgroup=mysql_result($d_f,0);
			$formg=" form_id LIKE '$formgroup' ";
			}
		if($formid!='' and $yeargroup==''){$query=' AND '.$formg;}
		elseif($formid=='' and $yeargroup!=''){$query=' AND '.$yearg;}
		elseif($formid!='' and $yeargroup!=''){$query=' AND ('.$formg.' OR '.$yearg.')';}
		else{$query='';}
		$d_s=mysql_query("SELECT student_id,surname,forename,middlenames,preferredforename,
							form_id,yeargroup_id,formerupn,epfusername
							FROM student JOIN info ON student.id=info.student_id 
							WHERE form_id NOT LIKE '%alumni%' AND form_id!='' $query 
							ORDER BY surname, forename, student_id;");
		/*$d_g=mysql_query("SELECT DISTINCT guardian_id,surname,forename,middlenames,epfusername,relationship,student_id
							FROM guardian JOIN gidsid ON guardian.id=gidsid.guardian_id
							ORDER BY student_id ASC, guardian_id ASC;");
		  $keys=array("surname","forename","relationship");
							*/
		/*$d_a=mysql_query("SELECT DISTINCT gidsid.guardian_id,address_id,street,neighbourhood,country,postcode,student_id
							FROM address, gidaid, gidsid 
							WHERE address.id=gidaid.address_id AND gidaid.guardian_id=gidsid.guardian_id
							ORDER BY student_id ASC, gidsid.guardian_id ASC;");
		  $keys=array("street","neighbourhood","country","postcode");
							*/
		/*Array for replacing all numbers by string numbers*/
		$nums=array("0"=>"zero"
					,"1"=>"one"
					,"2"=>"two"
					,"3"=>"three"
					,"4"=>"four"
					,"5"=>"five"
					,"6"=>"six"
					,"7"=>"seven"
					,"8"=>"eight"
					,"9"=>"nine"
					);
		if(!preg_match('/(\d+)/', $searchstring) and !is_numeric($searchstring)){
			$array_num=0;
			$keys=array("surname"
						,"forename"
						,"middlenames"
						,"preferredforename"
						);
			}
		/*If there isn't any number in the string it ignores it*/
		elseif(preg_match('/(\d+)/', $searchstring) and !is_numeric($searchstring)){
			$array_num=1;
			$keys=array("surname"
						,"forename"
						,"middlenames"
						,"preferredforename"
						,"form_id"
						//,"yeargroup_id"
						);
			/*foreach($nums as $key=>$num){
				$searchstring=preg_replace('/'.$key.'/', $num, $searchstring, 5);
				}*/
			}
		/*Combinatorics does a full combinations between all keys*/
		$searchstring=trim(strtolower($searchstring));
		$searchstring_transformed=iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$searchstring);
		$combinatorics = new Math_Combinatorics;
		for($i=count($keys);$i>=1;$i--){
			$combinations[]=$combinatorics->permutations($keys, $i);
			}
		while($stds=mysql_fetch_array($d_s,MYSQL_ASSOC)){
			$students[]=$stds;
			}
		/*Starts the search comparing each student's fields combinations*/
		foreach($students as $student){
			/*If the search string is not a number*/
			if(!is_numeric($searchstring)){
				foreach($combinations as $combination){
					foreach($combination as $fields){
						$string='';
						foreach($fields as $field){
							$sub=$student[$field];
							/*Replace numbers in db string*/
							/*if(preg_match('/(\d+)/', $student[$field]) and $array_num==1){
								foreach($nums as $key=>$num){
									$sub=preg_replace('/'.$key.'/', $num, $sub, 5);
									}
								}*/
								$string.=$sub." ";
							}
						/*Compare the distance between search string and db string*/
						$string=trim(strtolower($string));
						$lev=levenshtein($searchstring, $string);
						/*Exact match*/
						if($lev==0){
							$exact_id=$student['student_id'];
							$ids[$exact_id]=$exact_id;
							}
						/*Closest matches*/
						elseif($lev>0 and $lev<25){
							$closest_id=$student['student_id'];
							/*By percentage*/
							$percent=1-levenshtein($searchstring, $string)/max(strlen($searchstring), strlen($string));
							/*More than 70% or the distance is 1 or the plain string is contained in plain searchstring or vice versa*/
							if($percent*100>=70 or $lev==1){
								$closest_ids[$closest_id]=$closest_id;
								$levels[$closest_id]=$lev;
								}
							/*Between 20% and 70%*/
							elseif($percent*100>=20 and $percent*100<70){
								/*The last option is to search if the string is contained in the other*/
								$string_transformed=strtolower(iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$string));
								if(strpos($searchstring_transformed,$string_transformed) or strpos($string_transformed,$searchstring_transformed)){
									$closest_ids2[$student['student_id']]=$student['student_id'];
									$levels2[$closest_id]=$lev;
									}
								/*elseif(preg_match('/'.metaphone($searchstring_transformed).'/',metaphone($string_transformed)) or preg_match('/'.metaphone($string_transformed).'/',metaphone($searchstring_transformed))){
									$closest_ids2[$student['student_id']]=$student['student_id'];
									$levels2[$closest_id]=$lev;
									}*/
								}
							}
						}
					}
				}
			/*If search string is a number it displays the student id or the enrolment number*/
			else{
				$enrol_number=$student['formerupn'];
				$student_id=$student['student_id'];
				$yeargroupid=$student['yeargroup_id'];
				if($enrol_number==$searchstring or $student_id==$searchstring){
					$ids[$student_id]=$student_id;
					}
				elseif($yeargroupid==$searchstring){$ids[$student_id]=$student_id;}
				ksort($ids);
				}
			}
		/*Returns only a specific array of ids if exact matches weren't found and sorts it by levels*/
		if(count($ids)==0 or !isset($ids)){
			//$result[]="No results matched exactly. Similar results listed below (".count($closest_ids).").";
			if(count($closest_ids)!=0){
				array_multisort($levels,SORT_ASC,$closest_ids);
				$ids=$closest_ids;
				}
			elseif(count($closest_ids)==0 and count($closest_ids2)!=0){
				array_multisort($levels2,SORT_ASC,$closest_ids2);
				$ids=$closest_ids2;
				}
			}
		}
	return $ids;
	}

$start=microtime(true);
$ids=array();

if(isset($_POST['newyid']) and $_POST['newyid']!=''){
	$yeargroup=$_POST['newyid'];
	
	}
if(isset($_POST['newfid']) and $_POST['newfid']!=''){
	$fid=$_POST['newfid'];
	}

$searchvalue=clean_text($_POST['searchvalue']);
$ids=search_student($searchvalue,$fid,$yeargroup);

if(count($ids)==0){
	$rows=0;
	$result[]='No matches found! Please, be more specific.';
	}
else{
	$rows=count($ids);
	}

if($rows>0){
	$_SESSION['medbooksid']=$ids;
	$action='med_search_student.php';
	}
else{
	$_SESSION['medbooksid']='';
	$result[]=get_string('nostudentsfoundtryanothersearch',$book);
	$action='med_search_student.php';
	include('scripts/results.php');
	}

$end=microtime(true);
$_SESSION['searchtime']=$end-$start;
$_SESSION['searchstring']=$_POST['searchvalue'];

include('scripts/redirect.php');
?>
