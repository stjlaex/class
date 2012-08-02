<?php
/**								   export_students.php
 *
 */

$action='student_list.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}
if(isset($_POST['privfilter'])){$privfilter=$_POST['privfilter'];}else{$privfilter='visible';}

$displayfields=array();
$displayfields_no=0;
if(isset($_POST['colno'])){
	$displayfields_no=$_POST['colno'];
	for($dindex=0; $dindex < ($displayfields_no); $dindex++){
		if(isset($_POST['displayfield'.$dindex])){$displayfields[$dindex]=$_POST['displayfield'.$dindex];}
		}
	}
elseif(isset($_POST['catid'])){
	$catid=$_POST['catid'];
	$d_c=mysql_query("SELECT comment FROM categorydef WHERE id='$catid' AND type='col';");
	$taglist=mysql_result($d_c,0);
	$displayfields=(array)explode(':::',$taglist);
	$displayfields_no=sizeof($displayfields);
	}


require_once 'Spreadsheet/Excel/Writer.php';

include('scripts/sub_action.php');

if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
   		include('scripts/results.php');
   		include('scripts/redirect.php');
		exit;
		}



	$file=$CFG->eportfolio_dataroot. '/cache/files/';
  	$file.='class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$workbook->setVersion(8);
	$format_hdr_bold =& $workbook->addFormat(array('Size' => 11,
		                                  //'Align' => 'center',
		                                  'Color' => 'white',
		                                  'Pattern' => 1,
		                                  'Bold' => 1,
		                                  'FgColor' => 'gray'));
	$format_line_bold =& $workbook->addFormat(array('Size' => 10,
		                                  //'Align' => 'center',
		                                  'Bold' => 1
		                                  ));
	$format_line_normal =& $workbook->addFormat(array('Size' => 10,
		                                  //'Align' => 'center',
		                                  'Bold' => 0
		                                  ));
	$worksheet =& $workbook->addWorksheet('Export_Students');
  	
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$worksheet->setInputEncoding('UTF-8');
		$worksheet->write(0, 0, 'ClaSS Id.', $format_hdr_bold);
		$worksheet->write(0, 1, 'Enrolment No.', $format_hdr_bold);
		$worksheet->write(0, 2, 'Surname', $format_hdr_bold);
		$worksheet->write(0, 3, 'Forename', $format_hdr_bold);
		$worksheet->write(0, 4, 'Preferred Forename', $format_hdr_bold);
		$coloffset=5;
		for($colno=0;$colno<$displayfields_no;$colno++){
			if(substr_count($displayfields[$colno],'Assessment')>0){
				$eid=substr($displayfields[$colno],10);
				$AssDef=(array)fetchAssessmentDefinition($eid);
				$header=$AssDef['Description']['value'];
				unset($AssDef);
				}
			else{
				$header=$displayfields[$colno];
				}
			$worksheet->write(0, $colno+$coloffset, $header, $format_hdr_bold);
			if(substr_count($header,'PostalAddress')){$coloffset=$coloffset+4;}
			elseif(substr_count($header,'ContactPhone')){$coloffset=$coloffset+3;}
			elseif(substr_count($header,'Phone')){$coloffset=$coloffset;}
			}

		/*cycle through the student rows*/
		$rown=1;
		foreach($sids as $sid){
			$Student=(array)fetchStudent_short($sid);
			$EnrolNumber=(array)fetchStudent_singlefield($sid,'EnrolNumber');

			$worksheet->write($rown, 0, $sid, $format_line_bold);
			$worksheet->write($rown, 1, $EnrolNumber['EnrolNumber']['value'], $format_line_bold);
			$worksheet->write($rown, 2, $Student['Surname']['value'], $format_line_bold);
			$worksheet->write($rown, 3, $Student['Forename']['value'], $format_line_bold);
			$worksheet->write($rown, 4, $Student['PreferredForename']['value'], $format_line_bold);

			$col=5;
			foreach($displayfields as $displayfield){
				$displayout='';
				if(!array_key_exists($displayfield,$Student)){
					$field=fetchStudent_singlefield($sid,$displayfield);
					$Student=array_merge($Student,$field);
					}
				//trigger_error('!!!!!!!!!'.$displayfield,E_USER_WARNING);
				if(isset($Student[$displayfield]['type_db']) and $Student[$displayfield]['type_db']=='enum'){
					$displayout=displayEnum($Student[$displayfield]['value'],$Student[$displayfield]['field_db']);
					$displayout=get_string($displayout,$book);
					}
				elseif(isset($Student[$displayfield]['type_db']) and $Student[$displayfield]['type_db']=='date'){
					$displayout=display_date($Student[$displayfield]['value'],'export');
					}
				elseif(isset($Student[$displayfield]['value_db'])){
					/* Use the raw value from db if exists, so that one field value per column is exported. */
					$displayout=(array)explode(':::',$Student[$displayfield]['value_db']);
					if(isset($Student[$displayfield]['private'])){
						$privs=(array)explode(':::',$Student[$displayfield]['private']);
						foreach($privs as $privindex => $priv){
							if($priv=='Y' and $privfilter='hidden'){
								$displayout[$privindex]='';
								}
							}
						}
					if(substr_count($displayfield,'PostalAddress')>0){
						/* Make sure every record has same name number of fields even if blank. */
						if(sizeof($displayout)<5){
							while(sizeof($displayout)<5){
								$displayout[]='';
								}
							}
						}
					elseif(substr_count($displayfield,'ContactPhone')>0){
						/* Make sure every record has same name number of fields even if blank. */
						if(sizeof($displayout)<4){
							while(sizeof($displayout)<4){
								$displayout[]='';
								}
							}
						else{
							$displayout=array_slice($displayout,0,4);
							}
						}
					}
				elseif(array_key_exists($displayfield,$Student) and $Student[$displayfield]['value']!='' and (!isset($Student[$displayfield]['private']) or $Student[$displayfield]['private']=='N' or $privfilter=='visible')){
					$displayout=$Student[$displayfield]['value'];
					}
				else{
					if(substr_count($displayfield,'PostalAddress')){$displayno=5;}
					elseif(substr_count($displayfield,'ContactPhone')){$displayno=4;}
					else{$displayno=1;}
					while(sizeof($displayout)<$displayno){
						$displayout[]='';
						}
					}

				if(is_array($displayout)){
					foreach($displayout as $out){
						$worksheet->write($rown, $col, $out, $format_line_normal);
						$col++;
						}
					}
				else{
					$worksheet->write($rown, $col, $displayout, $format_line_normal);
					$col++;
					}
				}
			$rown++;
			}

		/*send the workbook with the spreadsheet and close it*/ 
		$workbook->close();
		//$result[]='exportedtofile';
?>
		<script>openFileExport('xls');</script>
<?php
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
