<?php
/**									report_assessments_export.php
 *
 */

$action='report_assessments_view.php';
$action_post_vars=array('year','stage','comid','yid','cids','limitbid','profid','profid','gender','eids','template');

if(isset($_POST['year'])){$year=$_POST['year'];}
if(isset($_POST['stage'])){$stage=$_POST['stage'];}
if(isset($_POST['yid'])){$yid=$_POST['yid'];}else{$yid='';}
if(isset($_POST['comid'])){$comid=$_POST['comid'];}else{$comid='';}
if(isset($_POST['gender'])){$gender=$_POST['gender'];}else{$gender='';}
if(isset($_POST['profid'])){$profid=$_POST['profid'];}
if(isset($_POST['limitbid'])){$limitbid=$_POST['limitbid'];}
if(isset($_POST['eids'])){$eids=(array)$_POST['eids'];}else{$eids=array();}
if(isset($_POST['cids'])){$cids=(array)$_POST['cids'];}else{$cids=array();}

include('scripts/sub_action.php');

$viewtable=$_SESSION[$book.'viewtable'];

	require_once 'Spreadsheet/Excel/Writer.php';

	$file=$CFG->eportfolio_dataroot. '/cache/files/';
  	$file.='class_export.xls';
	$workbook = new Spreadsheet_Excel_Writer($file);
	$format_head =& $workbook->addFormat(array('Size' => 10,
											   'Align' => 'center',
											   'Color' => 'white',
											   'Pattern' => 1,
											   'Bold' => 1,
											   'FgColor' => 'gray'));
	$format_subhead =& $workbook->addFormat(array('Size' => 9,
												  'Align' => 'center',
												  'Color' => 'gray',
												  'Pattern' => 1,
												  'Bold' => 1,
												  'FgColor' => 'white'));
	$format =& $workbook->addFormat(array('Size' => 10,
										  'Align' => 'left',
										  'Bold' => 1
										  ));
	$worksheet =& $workbook->addWorksheet('ClaSS MarkBook Export');

if(!$file){
		$error[]='Unable to open file for writing!';
		}
	else{

		/* First do the column headers */
		$worksheet->setColumn(0,0,14);
		$worksheet->setColumn(1,2,25);
		$worksheet->setColumn(2,20,20);
		$worksheet->write(0, 0, 'ClaSS Id.', $format_head);
		$worksheet->write(0, 1, get_string('enrolmentnumber','infobook'), $format_head);
		$worksheet->write(0, 2, get_string('personalnumber','infobook'), $format_head);
		$worksheet->write(0, 3, 'Surname', $format_head);
		$worksheet->write(0, 4, 'Forename', $format_head);
		$worksheet->write(0, 5, 'Preferred Forename', $format_head);

		/* Write the column headers */
		$no=6;
		for($cellno=0;$cellno<sizeof($viewtable[0]['out']);$cellno++){
			  if($viewtable[0]['count'][$cellno]>0){
				  foreach($viewtable[0]['results'] as $result){
					  $worksheet->write(0, $no,$viewtable[0]['out'][$cellno], $format_head);
					  $worksheet->write(1, $no,$result['label'], $format_subhead);
					  $worksheet->write(2, $no,$result['date'], $format_subhead);
					  $no++;
					  }
				  }
			  }

		/* Now write each student row, looking up the cell values in the viewtable array. */
		$i=3;
		for($rowno=1;$rowno<sizeof($viewtable);$rowno++){
			$Student=$viewtable[$rowno]['Student'];
			$worksheet->writenumber($i, 0, $Student['id_db'], $format);
			$worksheet->write($i, 1, $Student['EnrolNumber']['value'], $format);
			$worksheet->write($i, 2, $Student['PersonalNumber']['value'], $format);
			$worksheet->write($i, 3, iconv('UTF-8','ISO-8859-1',$Student['Surname']['value']), $format);
			$worksheet->write($i, 4, iconv('UTF-8','ISO-8859-1',$Student['Forename']['value']), $format);
			$worksheet->write($i, 5, iconv('UTF-8','ISO-8859-1',$Student['PreferredForename']['value']), $format);

			$cells=$viewtable[$rowno]['results'];
			$no=6;
			foreach($cells as $cellno => $results){
				if($viewtable[0]['count'][$cellno]>0){
					foreach($eids as $eid){
						if(array_key_exists($eid,$results)){
							if($viewtable[0]['scoretype'][$cellno]=='value'){
								$worksheet->writenumber($i, $no, $results[$eid]);
								}
							elseif($viewtable[0]['scoretype'][$cellno]=='percentage'){
								$worksheet->writenumber($i, $no, $results[$eid]);
								} 
							else{
								$worksheet->write($i, $no, $results[$eid]);
								}
							}
						else{
							$worksheet->write($i, $no, '');
							}
						$no++;
						}
					}
				}
			$i++;
			}

		$workbook->close();
?>
		<input type="hidden" name="openexport" id="openexport" value="xls">
<?php
		}

include('scripts/redirect.php');
?>
