<?php
/**
 *												fees_lib_xml.php
 *
 * @package Class
 * @version 1.11
 * @date 2014-05-29
 * @author marius@learningdata.ie
 *
 * Validated with ING SEPA Validator and W2C XSD SEPA validator
 * XML formatted with PHP DOMDocument (CRLF)
 *
 */

require_once('phputf8/utf8_to_ascii.php');

$ftype='xml';

function create_fees_file($remid,$Students){
	global $CFG;
	$SEPA=array();
	$GrpHdr=array();$DrctDbtTxInf=array();$PmtInf=array();

	$NbOfTxs=0;$CtrlSum=0;

	$Remittance=fetchRemittance($remid);
	$Concepts=array();
	$concepts_ids=array();
	foreach($Remittance['Concepts'] as $Concept){
		$Concepts[$Concept['id_db']]=$Concept;
		foreach($Concept['Tarifs'] as $Tarif){
			$concept_ids[$Tarif['id_db']]=$Concept['id_db'];
			}
		}

	$issuedate=date("Y-m-d",strtotime($Remittance['IssueDate']['value']));
	$paymentdate=date("Y-m-d",strtotime($Remittance['PaymentDate']['value']));

	$nif=$CFG->feesdetails['nif'];
	$nifdigits=checksumDigits($nif);

	$pBIC=$CFG->feesdetails['bic'];

	$e2eid=1;
	foreach($Students as $sid => $Student){
		//if($e2eid<=99){
			$Account=(array)fetchAccount($Student['payee']['id']);
			if($Account['Iban']['value']!='' and checkIBAN($Account['Iban']['value'])){$IBAN=$Account['Iban']['value'];}
			else{
				$accountno=$Account['BankCode']['value'].$Account['Branch']['value'].$Account['Control']['value'].$Account['Number']['value'];
				$ibandigits=checksumDigits($accountno);
				$IBAN="ES".$ibandigits.$accountno;
				}
			if($Account['id_db']!=-1 and checkIBAN($IBAN)){
				$invoice=(array)create_invoice($Account['id_db'],$remid);

				$reciboamount=0;
				$reciboitems=array();
				$concepts="";
				foreach($Student['charges'] as $charge){
					set_charge_payment($charge['id'],'1',$invoice['id']);
					$reciboamount+=$charge['amount'];
					$conid=$concept_ids[$charge['tarif_id']];
					$concepts.=php_utf8_to_ascii($Concepts[$conid]['Name']['value'])." ".$charge['amount']." ";
					}
				}

			if($reciboamount>0 and checkIBAN($IBAN)){

				$mndtid='1';

				$DrctDbtTxInf['PmtId']['EndToEndId']="E2EID".$e2eid;
				$DrctDbtTxInf['InstdAmt']=sprintf ("%.2f", $reciboamount);
				$DrctDbtTxInf['ChrgBr']="SLEV";
				$CtrlSum+=$reciboamount;
				$DrctDbtTx['MndtId']="MNDT".str_pad($Student['EnrolNumber']['value'], 10, "0", STR_PAD_LEFT);
				$DrctDbtTx['DtOfSgntr']=$issuedate;
				$DrctDbtTx['AmdmntInd']="false";
				$DrctDbtTxInf['DrctDbtTx']['MndtRltdInf']=$DrctDbtTx;
				if($Account['Bic']['value']!=''){$dBIC=$Account['Bic']['value'];}
				if($dBIC!="" and $dBIC!=" "){$DrctDbtTxInf['DbtrAgt']['FinInstnId']['BIC']=$dBIC;}
				else{$DrctDbtTxInf['DbtrAgt']['FinInstnId']['BIC']=getBIC($Account['BankCode']['value']);}
				if($Account['AccountName']['value']!=''){$nm=$Account['AccountName']['value'];}
				else{$nm=$Student['DisplayFullName']['value'];}
				$DrctDbtTxInf['Dbtr']['Nm']=substr(php_utf8_to_ascii($nm),0, 34);
				$DrctDbtTxInf['Dbtr']['PstlAdr']['Ctry']="ES";
				$DrctDbtTxInf['Dbtr']['Id']['OrgId']['Othr']['Id']="ES".$nifdigits."000".$nif;;
				$DrctDbtTxInf['Dbtr']['Id']['OrgId']['Othr']['SchmeNm']['Prtry']="SEPA";
				$DrctDbtTxInf['Dbtr']['Id']['OrgId']['Othr']['Issr']=substr(php_utf8_to_ascii($Remittance['Account']['AccountName']['value']), 0,34);
				$DrctDbtTxInf['DbtrAcct']['Id']['IBAN']=$IBAN;
				$DrctDbtTxInf['RmtInf']['Ustrd']=$concepts;
				$DrctDbt[]=$DrctDbtTxInf;
				$NbOfTxs++;
				$e2eid++;
				}
			//}
		}

	$Account=$Remittance['Account'];

	$GrpHdr['MsgId']="PAY".date("YmdHis");
	$dttm=date("Y-m-d\TH:i:s");
	$GrpHdr['CreDtTm']=$dttm;
	$GrpHdr['NbOfTxs']=$NbOfTxs;
	$GrpHdr['CtrlSum']=sprintf ("%.2f", $CtrlSum);
	$GrpHdr['InitgPty']['Nm']=substr(php_utf8_to_ascii($Account['AccountName']['value']),0, 69);
	$GrpHdr['InitgPty']['Id']['OrgId']['Othr']['Id']="ES".$nifdigits."000".$nif;
	$GrpHdr['InitgPty']['Id']['OrgId']['Othr']['SchmeNm']['Cd']="SEPA";
	$GrpHdr['InitgPty']['Id']['OrgId']['Othr']['Issr']=substr(php_utf8_to_ascii($Account['AccountName']['value']),0, 34);

	$PmtInf['PmtInfId']="PMTINFID1";
	$PmtInf['PmtMtd']="DD";
	$PmtInf['BtchBookg']="false";
	$PmtInf['CtrlSum']=sprintf ("%.2f", $CtrlSum);
	$PmtTpInf['SvcLvl']['Cd']="SEPA";
	$PmtTpInf['LclInstrm']['Cd']="CORE";
	$PmtTpInf['SeqTp']="RCUR";
	$PmtTpInf['CtgyPurp']['Prtry']=substr(php_utf8_to_ascii($Remittance['Name']['value']), 0, 34);
	$PmtInf['PmtTpInf']=$PmtTpInf;
	$PmtInf['ReqdColltnDt']=$paymentdate;
	$PmtInf['Cdtr']['Nm']=substr(php_utf8_to_ascii($Account['AccountName']['value']),0, 69);
	$PmtInf['Cdtr']['PstlAdr']['Ctry']="ES";

	if($Account['Iban']['value']!='' and checkIBAN($Account['Iban']['value'])){$IBAN=$Account['Iban']['value'];}
	else{
		$accountno=$Account['BankCode']['value'].$Account['Branch']['value'].$Account['Control']['value'].$Account['Number']['value'];
		$ibandigits=checksumDigits($accountno);
		$IBAN="ES".$ibandigits.$accountno;
		}
	$PmtInf['CdtrAcct']['Id']['IBAN']=$IBAN;
	if($Account['Bic']['value']!=''){$pBIC=$Account['Bic']['value'];}
	$PmtInf['CdtrAgt']['FinInstnId']['BIC']=$pBIC;
	$PmtInf['ChrgBr']="SLEV";

	$CdtrSchmeId['Id']="ES".$nifdigits."000".$nif;
	$CdtrSchmeId['SchmeNm']['Prtry']="SEPA";
	$PmtInf['CdtrSchmeId']['Id']['PrvtId']['Othr']=$CdtrSchmeId;

	$SEPA['CstmrDrctDbtInitn']['GrpHdr']=$GrpHdr;
	$SEPA['CstmrDrctDbtInitn']['PmtInf']=$PmtInf;
	$SEPA['CstmrDrctDbtInitn']['PmtInf']['DrctDbtTxInf']=$DrctDbt;

	$returnXML=$SEPA;
	$rootName='Document';

	$xml=xml_preparer($rootName,$SEPA);
	return $xml;
	}


function checksumDigits($string,$country="ES"){
	$letters=array("A"=>"10","B"=>"11","C"=>"12","D"=>"13","E"=>"14","F"=>"15",
				"G"=>"16","H"=>"17","I"=>"18","J"=>"19","K"=>"20","L"=>"21",
				"M"=>"22","N"=>"23","O"=>"24","P"=>"25","Q"=>"26","R"=>"27",
				"S"=>"28","T"=>"29","U"=>"30","V"=>"31","W"=>"32","X"=>"33",
				"Y"=>"34","Z"=>"35"
				);
	$code=$string.$country."00";
	$ncode=strtr($code,$letters);
	$rest=bcmod($ncode,97);
	$digits=98-$rest;
	if($digits<10){$digits=str_pad($digits,2,"0",STR_PAD_LEFT);}
	return $digits;
	}

function case_Correct($array){
	if(is_array($array)){
		$newarray=array();
		foreach($array as $key => $value){
			$newarray[$key]=case_Correct($value);
			}
		}
	else{
		$newarray=$array;
		}
	return $newarray;
	}

function xml_preparer($root_element_name,$xmlarray,$options=''){
	if($options==''){
		$xmlarray=case_Correct($xmlarray);
		}

	$rootname=trim($root_element_name);
	$xml=new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><{$rootname} xmlns=\"urn:iso:std:iso:20022:tech:xsd:pain.008.001.02\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"></{$rootname}>");

	arraytoxml($xmlarray,$xml);

	$xmlstring=$xml->asXML();
	$xmldom=new DOMDocument();
	$xmldom->preserveWhiteSpace=false;
	$xmldom->formatOutput=true;
	$xmldom->loadXML($xmlstring);
	$xmlstring=$xmldom->saveXML(); 

	return $xmlstring;
	}

function arraytoxml($xmlarray, &$xml, $tagname=''){
	foreach($xmlarray as $key => $value){
		if(is_array($value)){
			if(!is_numeric($key)){
				$subkeys=array_keys($value);
				if(array_key_exists(0,$subkeys) and !is_numeric($subkeys[0])){
					$newnode = $xml->addChild($key);
					arraytoxml($value, $newnode, $key);
					}
				else{
					arraytoxml($value, $xml, $key);
					}
				}
			else{
					$subnode=$xml->addChild($tagname);
					arraytoxml($value, $subnode, $tagname);
				}
			}
		else{
			$value=htmlspecialchars($value);
			if(!is_numeric($key)){
				$xml->addChild($key,$value);
				}
			else{
				$xml->addChild($tagname,$value);
				}
			}
		$xml->InstdAmt->addAttribute('Ccy','EUR');
		$xml->Amt->addAttribute('Ccy','EUR');
		}
	}

function getBIC($bankcode){
	$bics=array(
		"9093"=>"XRVVESVVXXX",
		"9092"=>"XRBVES2BXXX",
		"9091"=>"XBCNESBBXXX",
		"0196"=>"WELAESMMXXX",
		"1480"=>"VOWAES21XXX",
		"0227"=>"UNOEESM1XXX",
		"1472"=>"UCSSESM1XXX",
		"2103"=>"UCJAES2MXXX",
		"8512"=>"UCINESMMXXX",
		"0226"=>"UBSWESMMXXX",
		"1524"=>"UBIBESMMXXX",
		"1491"=>"TRIOESMMXXX",
		"0108"=>"SOGEESMMXXX",
		"1490"=>"SELFESMMXXX",
		"0224"=>"SCFBESMMXXX",
		"8835"=>"SBFCESMMXXX",
		"0036"=>"SABNESMMXXX",
		"3501"=>"RENTESMMXXX",
		"0083"=>"RENBESMMXXX",
		"1210"=>"REDEESM1XXX",
		"1463"=>"PSABESM1XXX",
		"0200"=>"PRVBESB1XXX",
		"0211"=>"PROAESMMXXX",
		"0132"=>"PRNEESM1XXX",
		"1473"=>"PRIBESMXXXX",
		"1234"=>"PRBAESM1XXX",
		"1459"=>"PRABESMMXXX",
		"0075"=>"POPUESMMXXX",
		"0229"=>"POPLESMMXXX",
		"0233"=>"POPIESMMXXX",
		"0216"=>"POHIESMMXXX",
		"1193"=>"PKBSES21XXX",
		"0235"=>"PIESESM1XXX",
		"1488"=>"PICTESMMXXX",
		"0144"=>"PARBESMXXXX",
		"0073"=>"OPENESMMXXX",
		"0121"=>"OCBAESM1XXX",
		"1249"=>"NPBSES21XXX",
		"1479"=>"NATXESMMXXX",
		"0169"=>"NACNESMMXXX",
		"6814"=>"MNTYESMMXXX",
		"3661"=>"MLCEESMMXXX",
		"1506"=>"MLCBESM1XXX",
		"3563"=>"MISVESMMXXX",
		"0133"=>"MIKBESB1XXX",
		"0162"=>"MIDLESMMXXX",
		"9094"=>"MEFFESBBXXX",
		"0059"=>"MADRESMMXXX",
		"0236"=>"LOYIESMMXXX",
		"1457"=>"LLISESM1XXX",
		"3641"=>"LISEESMMXXX",
		"1534"=>"KBLXESMMXXX",
		"3669"=>"IVALESMMXXX",
		"1156"=>"IRVTESM1XXX",
		"9020"=>"IPAYESMMXXX",
		"0232"=>"INVLESMMXXX",
		"3575"=>"INSGESMMXXX",
		"1465"=>"INGDESMMXXX",
		"0113"=>"INBBESM1XXX",
		"0129"=>"INALESM1XXX",
		"1502"=>"IKBDESM1XXX",
		"1251"=>"IHZUES21XXX",
		"1000"=>"ICROESMMXXX",
		"1538"=>"ICBKESMMXXX",
		"9096"=>"IBRCESMMXXX",
		"1236"=>"HELAESM1XXX",
		"3682"=>"GVCBESBBETB",
		"0223"=>"GEECESB1XXX",
		"0167"=>"GEBAESMMXXX",
		"0487"=>"GBMNESMMXXX",
		"0046"=>"GALEES2GXXX",
		"0220"=>"FIOFESM1XXX",
		"0225"=>"FIEIESM1XXX",
		"0218"=>"FCEFESM1XXX",
		"0031"=>"ETCHES2GXXX",
		"1497"=>"ESSIESMMXXX",
		"9000"=>"ESPBESMMXXX",
		"1164"=>"ESBFESM1XXX",
		"1467"=>"EHYPESMXXXX",
		"1522"=>"EFGBESMMXXX",
		"0231"=>"DSBLESMMXXX",
		"1501"=>"DPBBESM1XXX",
		"0145"=>"DEUTESM1XXX",
		"0019"=>"DEUTESBBXXX",
		"0237"=>"CSURES2CXXX",
		"3351"=>"CSSOES2SXXX",
		"3656"=>"CSSOES2SFIN",
		"2108"=>"CSPAES2L108",
		"1460"=>"CRESESMMXXX",
		"1451"=>"CRCGESB1XXX",
		"0159"=>"COBAESMXXXX",
		"3035"=>"CLPEES2MXXX",
		"1474"=>"CITIESMXXXX",
		"0122"=>"CITIES2XXXX",
		"0151"=>"CHASESM3XXX",
		"0130"=>"CGDIESMMXXX",
		"2013"=>"CESCESBBXXX",
		"2000"=>"CECAESMMXXX",
		"2086"=>"CECAESMM086",
		"2056"=>"CECAESMM056",
		"2048"=>"CECAESMM048",
		"2045"=>"CECAESMM045",
		"3025"=>"CDENESBBXXX",
		"1475"=>"CCSEESM1XXX",
		"3058"=>"CCRIES2AXXX",
		"3188"=>"CCRIES2A188",
		"3186"=>"CCRIES2A186",
		"3179"=>"CCRIES2A179",
		"3165"=>"CCRIES2A165",
		"3160"=>"CCRIES2A160",
		"3157"=>"CCRIES2A157",
		"3152"=>"CCRIES2A152",
		"3137"=>"CCRIES2A137",
		"3135"=>"CCRIES2A135",
		"3123"=>"CCRIES2A123",
		"3121"=>"CCRIES2A121",
		"3119"=>"CCRIES2A119",
		"3118"=>"CCRIES2A118",
		"3112"=>"CCRIES2A112",
		"3105"=>"CCRIES2A105",
		"3095"=>"CCRIES2A095",
		"3045"=>"CCRIES2A045",
		"3029"=>"CCRIES2A029",
		"0234"=>"CCOCESMMXXX",
		"3146"=>"CCCVESM1XXX",
		"2085"=>"CAZRES2ZXXX",
		"3183"=>"CASDESBBXXX",
		"3604"=>"CAPIESMMXXX",
		"2100"=>"CAIXESBBXXX",
		"2038"=>"CAHMESMMXXX",
		"2080"=>"CAGLESMMVIG",
		"0094"=>"BVALESMMXXX",
		"0057"=>"BVADESMMXXX",
		"0154"=>"BSUIESMMXXX",
		"0049"=>"BSCHESMMXXX",
		"0081"=>"BSABESBBXXX",
		"0155"=>"BRASESMMXXX",
		"0152"=>"BPLCESMMXXX",
		"1470"=>"BPIPESM1XXX",
		"0160"=>"BOTKESMXXXX",
		"1485"=>"BOFAES2XXXX",
		"0149"=>"BNPAESMSXXX",
		"0058"=>"BNPAESMMXXX",
		"6852"=>"BMEUESM1XXX",
		"0219"=>"BMCEESMMXXX",
		"0061"=>"BMARES2MXXX",
		"0161"=>"BKTRESM1XXX",
		"0138"=>"BKOAES22XXX",
		"0128"=>"BKBKESMMXXX",
		"0186"=>"BFIVESBBXXX",
		"0488"=>"BFASESMMXXX",
		"0131"=>"BESMESMMXXX",
		"0184"=>"BEDFESM1XXX",
		"0003"=>"BDEPESM1XXX",
		"1005"=>"BCOEESMMXXX",
		"3191"=>"BCOEESMM191",
		"3190"=>"BCOEESMM190",
		"3187"=>"BCOEESMM187",
		"3177"=>"BCOEESMM177",
		"3174"=>"BCOEESMM174",
		"3166"=>"BCOEESMM166",
		"3162"=>"BCOEESMM162",
		"3159"=>"BCOEESMM159",
		"3150"=>"BCOEESMM150",
		"3144"=>"BCOEESMM144",
		"3140"=>"BCOEESMM140",
		"3138"=>"BCOEESMM138",
		"3134"=>"BCOEESMM134",
		"3130"=>"BCOEESMM130",
		"3127"=>"BCOEESMM127",
		"3117"=>"BCOEESMM117",
		"3116"=>"BCOEESMM116",
		"3115"=>"BCOEESMM115",
		"3113"=>"BCOEESMM113",
		"3111"=>"BCOEESMM111",
		"3110"=>"BCOEESMM110",
		"3104"=>"BCOEESMM104",
		"3102"=>"BCOEESMM102",
		"3098"=>"BCOEESMM098",
		"3096"=>"BCOEESMM096",
		"3089"=>"BCOEESMM089",
		"3085"=>"BCOEESMM085",
		"3081"=>"BCOEESMM081",
		"3080"=>"BCOEESMM080",
		"3076"=>"BCOEESMM076",
		"3070"=>"BCOEESMM070",
		"3067"=>"BCOEESMM067",
		"3063"=>"BCOEESMM063",
		"3059"=>"BCOEESMM059",
		"3023"=>"BCOEESMM023",
		"3020"=>"BCOEESMM020",
		"3018"=>"BCOEESMM018",
		"3017"=>"BCOEESMM017",
		"3016"=>"BCOEESMM016",
		"3009"=>"BCOEESMM009",
		"3008"=>"BCOEESMM008",
		"3007"=>"BCOEESMM007",
		"3001"=>"BCOEESMM001",
		"1494"=>"BCITESMMXXX",
		"1525"=>"BCDMESMMXXX",
		"0182"=>"BBVAESMMXXX",
		"0168"=>"BBRUESMXXXX",
		"0190"=>"BBPIESMMXXX",
		"2095"=>"BASKES2BXXX",
		"0065"=>"BARCESMMXXX",
		"0078"=>"BAPUES22XXX",
		"0125"=>"BAOFESM1XXX",
		"1544"=>"BACAESMMXXX",
		"0136"=>"AREBESMMXXX",
		"1505"=>"ARABESMMXXX",
		"0011"=>"ALLFESMMXXX",
		"0188"=>"ALCLESMMXXX",
		"3524"=>"AHCFESMMXXX",
		"1545"=>"AGRIESMMXXX",
		"0156"=>"ABNAESMMXXX",
		"1255"=>"AARBESM1XXX"
	);
	$correctbic="CAIXESBBXXX";
	foreach($bics as $code=>$bic){
		if($bankcode==$code){$correctbic=$bic;}
		}
	return $correctbic;
	}
?>
