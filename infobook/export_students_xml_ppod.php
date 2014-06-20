<?php
/**								   export_students_xml_ppod.php
 *
 */

$action='student_list.php';

if(isset($_POST['sids'])){$sids=(array)$_POST['sids'];}else{$sids=array();}

//$file=$CFG->eportfolio_dataroot. '/cache/files/';
//$file.='class_export.xml';

	$Students['Student']=array();
	foreach($sids as $sid){
		$Student=(array)fetchStudent($sid);
		if($Student['Gender']['value']!=''){$Student['Gender']['value_display']=''.get_string(displayEnum($Student['Gender']['value'], 'gender'),'infobook');}
		if($Student['Nationality']['value']!=''){$Student['Nationality']['value_display']=''.get_string(displayEnum($Student['Nationality']['value'], 'nationality'),'infobook');}
		if($Student['SecondNationality']['value']!=''){$Student['SecondNationality']['value_display']=''.get_string(displayEnum($Student['SecondNationality']['value'], 'nationality'),'infobook');}
		if($Student['Language']['value']!=''){$Student['Language']['value_display']=''.get_string(displayEnum($Student['Language']['value'], 'language'),'infobook');}
		if($Student['SecondLanguage']['value']!=''){$Student['SecondLanguage']['value_display']=''.get_string(displayEnum($Student['SecondLanguage']['value'], 'language'),'infobook');}
		if($Student['EnrolmentStatus']['value']!=''){$Student['EnrolmentStatus']['value_display']=''.get_string(displayEnum($Student['EnrolmentStatus']['value'], 'enrolstatus'),'infobook');}
		if($Student['TransportMode']['value']!=''){$Student['TransportMode']['value_display']=''.get_string(displayEnum($Student['TransportMode']['value'], 'transportmode'),'infobook');}
		foreach($Student['Contacts'] as $index=>$Contact){
			if($Contact['Order']['value']!=''){$Student['Contacts'][$index]['Order']['value_display']=''.get_string(displayEnum($Contact['Order']['value'], 'priority'),'infobook');}
			if($Contact['Relationship']['value']!=''){$Student['Contacts'][$index]['Relationship']['value_display']=''.get_string(displayEnum($Contact['Relationship']['value'], 'relationship'),'infobook');}
			if($Contact['Title']['value']!=''){$Student['Contacts'][$index]['Title']['value_display']=''.get_string(displayEnum($Contact['Title']['value'], 'title'),'infobook');}
			if($Contact['Nationality']['value']!=''){$Student['Contacts'][$index]['Nationality']['value_display']=''.get_string(displayEnum($Contact['Nationality']['value'], 'nationality'),'infobook');}
			if($Contact['Private']['value']!=''){$Student['Contacts'][$index]['Private']['value_display']=''.get_string(displayEnum($Contact['Private']['value'], 'private'),'infobook');}
			}
		$Students['Student'][]=$Student;
		}
	$returnXML=$Students;
	$rootName='Students';
	$xml=xmlpreparer($rootName,$returnXML);
	/*$xml='<?xml version="1.0" encoding="UTF-8"?>'.$xml;*/
	$xml=preg_replace('/&#.{0,5};/','',str_replace('"',"'",preg_replace("/\n/", "", $xml)));

	echo "
	<script src='//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js'></script>
	<script>
		$(document).ready(function(){
			var xml=\"".$xml."\";
			var apiURL=\"".$CFG->ppod_api."\";
			console.log(xml);
			console.log(apiURL);
			$.ajax({
			    url: apiURL,
			    type: \"post\",
			    data  : \"xml=\"+xml , 
			    success: function(response){
				console.log(\"Success\");
				return true;
			    	},
			    error : function(error, request){
				console.log(\"Error!\");
				return false;
			    	}
			    });
			});
	</script>";

	//include('scripts/results.php');
	include('scripts/redirect.php');
?>
