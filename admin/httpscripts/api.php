<?php
/**
 *											classis_api.php
 *
 *	@package		Classis
 *	@author		stj@laex.org
 *	@copyright	S T Johnson 2004-2014
 *	@version
 *	@since
 */
 require_once('../../../school.php');

	if(isset($_GET['action']) and $_GET['action']!=''){$action=$_GET['action'];}else{$action='';}
	if(isset($_GET['username']) and $_GET['username']!=''){$username=$_GET['username'];}else{$username='';}
	if(isset($_GET['password']) and $_GET['password']!=''){$token=$_GET['password'];}else{$token='';}

	if($username!='' and $token!='' and $action!=''){
		$postdata=1;
		$params='';
		if($_GET['post']=='true'){
			$script='api_postqueries.php';
			$content=file_get_contents('php://input');
			$postdata=array('data'=>$content);
			}
		else{
			$script='api_getqueries.php';
			foreach($_GET as $paramname=>$paramvalue){
				if($paramname!='action' and $paramname!='user' and $paramname!='password'){
					$params.='&';
					$params.=$paramname.'='.$paramvalue;
					}
				}
			}

		if(isset($_SERVER['HTTPS'])){$http='https';}
		else{$http='http';}
		$url=$http.'://'.$CFG->siteaddress.$CFG->sitepath.'/'.$CFG->theme20.'/admin/httpscripts/'.$script.'?username=' 
				.$username.'&password='.$token.'&action='.$action.$params;

		$curl=curl_init();
		curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
		curl_setopt($curl,CURLOPT_URL,$url);
		curl_setopt($curl,CURLOPT_POST,1);
		curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$postdata);
		$response=curl_exec($curl);
		curl_close($curl);
		}
	else{
		$response['success']=false;
		$response['errors'][]='Invalid request!';
		$response['errors'][]='Authentication and action needed.';
		$response['errors'][]='Go to http://wiki.classlearning.net/doku.php?id=api for documentation.';
		$response=json_encode($response);
		}

	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Content-Type");
	header("Content-Type: application/json; charset=utf-8"); 
	echo $response;
?>
