<?php
/**								   export_students.php
 *
 */

$action='student_list.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}

  	$file=fopen('/tmp/class_export.csv', 'w');
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{

		/*first do the column headers*/
		$csv=array();
		$Student=fetchStudent();
		$Contact=fetchContact();
		$Address_blank=fetchAddress();

		$csv[]='student_id_db';
		while(list($tagname,$field)=each($Student)){
			if(is_array($field) and isset($field['value'])){$csv[]=$field['label'];}
			}
		for($c=0;$c<4;$c++){
			reset($Contact);
			reset($Address_blank);
			$csv[]='';
			$csv[]='contact_id_db';
			while(list($tagname,$field)=each($Contact)){
				if(is_array($field) and isset($field['value'])){$csv[]=$field['label'];}
				}
			while(list($tagname,$field)=each($Address_blank)){
				if(is_array($field) and isset($field['value'])){$csv[]=$field['label'];}
				}
			}

		file_putcsv($file,$csv);

		/*cycle through the student rows*/
		while(list($index,$sid)=each($sids)){
			$csv=array();
			$Student=fetchStudent($sid);
			$Contacts=$Student['Contacts'];

			$csv[]=$sid;
			while(list($tagname,$field)=each($Student)){
				if(is_array($field) and isset($field['value'])){
					if($field['type_db']=='enum'){
						if($field['value']!='NOT'){
							$csv[]=displayEnum($field['value'],$field['field_db']);
							}
						else{$csv[]='';}
						}
					else{$csv[]=$field['value'];}
					}
				}
			while(list($index,$Contact)=each($Contacts)){
				$csv[]='';
				$csv[]=$Contact['id_db'];
				while(list($tagname,$field)=each($Contact)){
					if(is_array($field) and isset($field['value'])){
						if($field['type_db']=='enum'){
							if($field['value']!='NOT'){
								$csv[]=displayEnum($field['value'],$field['field_db']);
								}
							else{$csv[]='';}
							}
						else{$csv[]=$field['value'];}
						}
					}
				if(is_array($Contact['Addresses']) & sizeof($Contact['Addresses'][0])>1){
					$Address=$Contact['Addresses'][0];
					}
				else{
					$Address=$Address_blank;
					}		
				while(list($tagname,$field)=each($Address)){
					if(is_array($field) and isset($field['value'])){
						if($field['type_db']=='enum'){
							if($field['value']!='NOT'){
								$csv[]=displayEnum($field['value'],$field['field_db']);
								}
							else{$csv[]='';}
							}
						else{$csv[]=$field['value'];}
						}
					}
				}
			file_putcsv($file,$csv);
			}
	   	fclose($file);
		$result[]='exportedtofile';
?>
		<script>openFileExport();</script>
<?php
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
