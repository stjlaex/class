<?php
/**								   timetable_export.php
 *
 */

$action='orders.php';


  	$file=fopen('/tmp/class_export.xml', 'w');
	if(!$file){
		$error[]='unabletoopenfileforwriting';
		}
	else{
		$xmllines=array();
		$xmllines['Institution_Name']=$CFG->schoolname;
		$xmllines['Comments']='Testing';

		$status=array_search('closed',getEnumArray('action'));
		$orders=(array)list_orders('%',$status);
		while(list($indexo,$order)=each($orders)){
			$xmllines['Orders']['Order'][]=fetchOrder($order['id']);
			trigger_error($ordid,E_USER_WARNING);
			}

		
		$options=array(
					   'addDecl' => true,
					   'encoding' => 'UTF-8',
					   'indent' => '  ',
					   'mode' => 'simplexml',
					   'rootName' => 'Accounts_Transfer',
					   'rootAttributes'=> array('version'=>'ClaSS '.$CFG->version),
					   'addDoctype' => true,
					   'doctype' => array(
					   				  'uri' => '',
					   				  'id'  => '')
					   );

		$xml=xmlpreparer('Accounts_Transfer',$xmllines,$options);
		fwrite($file,$xml);

	   	fclose($file);
		$result[]='exportedtofile';
?>
		<script>openXMLExport('xml');</script>
<?php
		}

	include('scripts/results.php');
	include('scripts/redirect.php');
?>
