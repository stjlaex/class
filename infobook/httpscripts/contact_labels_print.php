<?php
/**						httpscripts/contact_labels_print.php
 */

require_once('../../scripts/http_head_options.php');

if(isset($_GET['sids'])){$sids=(array)$_GET['sids'];}else{$sids=array();}
if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}

	if(sizeof($sids)==0){
		$result[]=get_string('youneedtoselectstudents');
		$returnXML=$result;
		$rootName='Error';
		}
	else{
		$Students=array();
		$Students['Student']=array();
		for($c=0;$c<sizeof($sids);$c++){
			$sid=$sids[$c];
			$Student=fetchStudent_short($sid);
			$EnrolNumber=fetchStudent_singlefield($sid,'EnrolNumber');
			$Student=array_merge($Student,$EnrolNumber);
			$Contacts=(array)fetchContacts($sid);
			$Student['Contacts']['Contact']=array();
			while(list($index,$Contact)=each($Contacts)){
				$mailing=$Contact['ReceivesMailing']['value'];
					/* Options are:
					'0' => 'nomailing', '1' => 'allmailing', '2' => 'reportsonly'
					so currently configured for reports mailing */
				if(($mailing=='1' or $mailing=='2')){
					unset($Contact['Phones']);
					$Student['Contacts']['Contact'][]=nullCorrect($Contact);
					}
				}
			if(sizeof($Student['Contacts']['Contact'])>0){
				$Students['Student'][]=$Student;
				}
			}
		$Students['transform']='labels';
		$Students['paper']='portrait';
		$returnXML=$Students;
		$rootName='Students';
		}

require_once('../../scripts/http_end_options.php');
exit;


// Prints to an Avery 5160 label sheet which is a label
// 2 5/8" wide by 1" tall, they are 3 accross on a page
// and 10 rows per page. (30 per page). The upper left
// corner is label(0,0) The X co-ord goes horizontally
// accross the page and Y goes vertically down the page
// Left/Right page margins are 4.2 MM (1/6 inch)
// Top/Botton page margines are 12.7 MM (.5 inch)
// Horizontal gap between labels is 4.2 MM (1/6 inch)
// There is no vertial gap between labels
// Labels are 66.6 MM (2 5/8") Wide
// Labels are 25.4 MM (1" ) Tall
// X co-ord of label (0-2)
// Y co-ord of label (0-9)
function makelabel_Avery5160($x,$y,&$pdf,$Data){
	$LeftMargin=4.2;
	$TopMargin=12.7;
	$LabelWidth=66.6;
	$LabelHeight=25.45;
	// Create Co-Ords of Upper left of the Label
	$AbsX=$LeftMargin + (($LabelWidth + 4.22) * $x);
	$AbsY=$TopMargin + ($LabelHeight * $y);
	
	// Fudge the Start 3mm inside the label to avoid alignment errors
	$pdf->SetXY($AbsX+3,$AbsY+3);
	$pdf->MultiCell($LabelWidth-8,4.5,$Data);

	return;
	}

function PrintAddressLabels($Contacts){
	$pdf=new FPDF();
	$pdf->Open();
	$pdf->AddPage();
	$pdf->SetFont('Arial','B',10);
	$pdf->SetMargins(0,0);
	$pdf->SetAutoPageBreak(false);
	
	$x=0;
	$y=0;
	while(list($index,$Address)=each($Contacts)){
		$LabelText=sprintf("%s\n%s\n%s, %s, %s",
							 $row['MailName'],
							 $row['Address'],
							 $row['City'],$row['State'],$row['Zip']);
		makelabel_Avery5160($x,$y,$pdf,$LabelText);
		
		$y++; // next row
		if($y==10){ // end of page wrap to next column
			$x++;
			$y=0;
			if($x==3){ // end of page
				$x=0;
				$y=0;
				$pdf->AddPage();
				}
			}
		}

	$pdf->Output();
	}

?>