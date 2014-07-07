#! /usr/bin/php -q
<?php
/**
 *												class_update.php
 *
 * @package Class
 * @version 0.7
 * @date 2014-03-25
 * @author marius@learningdata.ie
 *
 */

/* The path is passed as a command line argument. */
function arguments($argv){
	$ARGS=array();
	foreach($argv as $arg){
		if(ereg('--([^=]+)=(.*)',$arg,$reg)){
			$ARGS[$reg[1]]=$reg[2];
			} 
		elseif(ereg('-([a-zA-Z0-9])',$arg,$reg)){
			$ARGS[$reg[1]]='true';
			}
		}
	return $ARGS;
	}
$ARGS=arguments($_SERVER['argv']);

if($ARGS['path']!=""){$top=$ARGS['path'];}else{$top="";}

if($top!=""){
	require_once($top.'/school.php');

	$rep_file=$top.$CFG->applicationdirectory.'/install/toplevel/school.php';
	$file=$top.'/school.php';
	$copy=$top.'/school_copy.php';

	$rep_reading=fopen($rep_file,'r');
	$reading=fopen($file,'r');
	$writing=fopen($copy,'w+');

	//chmod($file, 0775);
	chmod($copy, 0775);

	if(!$writing){
		unlink($copy);
		}

	$rep_schoolphp=getCFG($rep_reading);
	$rep_features=$rep_schoolphp['features'];
	$rep_other=$rep_schoolphp['other'];

	$old_schoolphp=getCFG($reading);
	$old_features=$old_schoolphp['features'];
	$old_other=$old_schoolphp['other'];
	$old_lines=$old_schoolphp['lines'];

	$html="";

	foreach($rep_features as $lineno=>$rfeature){
		$exists=false;
		foreach($old_features as $ofeature){
			$rfeaturename=explode("[",$rfeature['Variable']);
			$ofeaturename=explode("[",$ofeature['Variable']);
			if(trim($rfeature['Variable'])==trim($ofeature['Variable']) or $rfeaturename[0]==$ofeaturename[0]){
				$exists=true;
				break;
				}
			}
		if(!$exists){$newlines[$lineno]=$rfeature;}
		}

	foreach($old_lines as $lineno=>$line){
		if($lineno==count($old_lines)){
			foreach($newlines as $newline){
				$description=$newline["Description"];
				if(isset($newline) and $newline['Variable']!=""){
					$html.=$description.$newline['Line'];
					}
				}
			foreach($rep_other as $lno=>$rother){
				$exists=false;
				$previouslineno=$lno-1;
				foreach($old_other as $oother){
					if($rother==$oother){
						$exists=true;
						break;
						}
					}
				if(!$exists){$html.=$rother;}
				}
			}
		$html.=$line;
		}

	if(fwrite($writing,$html)){
		$check_syntax="php -l $copy";
		$result_syntax=exec(escapeshellcmd($check_syntax));
		$size=filesize($copy);
		include_once ($copy);
		if($result_syntax && $size>0 && !empty($CFG->sitepath) && !empty($CFG->installpath) && !empty($CFG->siteaddress)){
			if(rename($copy,$file)){
				echo "The configuration has been written with success.";
				}
			else{unlink($copy);}
			}
		else{
			unlink($copy);
			}
		}
	}

/*
 * Returns all the lines, the features lines with description, variable and value 
 * and the other lines with functions,if for a given school.php file.
 */
function getCFG($reading){
	$row=1;
	$lineno=1;
	$lines=array();
	$other=array();
	$opencomment=false;
	$blockstart=false;
	$blockstartno=0;
	$found=true;
	while(!feof($reading)){
		$conf_line='/^\$CFG.+/';
		$comm_line='/^\/\*|.*\*\/|.*\*.*/';
		$quotes[$row]=1;
		$changed=0;
		$line=fgets($reading);
		if($line==false){break;}
		$lines[$lineno]=$line;
		$code.=$line;

		if(preg_match($conf_line,$line)){
			$parts=explode("=",$line);
			$cfg_line=1;

			if(preg_match("/^'/",$parts[1])){$types[]='string';}
			else{$types[]='array_other';}

			$value_parts=explode("'",$parts[1]);
			$value=$value_parts[1];
			$variable=trim($parts[0]);

			$features[$row]["Line"]=$line;
			$features[$row]["Variable"]=$variable;

			if(preg_match("/'.*';/",$parts[1])){$features[$row]["Value"]=$value;}
			else{
				if(preg_match("/={1,}/",$line) && preg_match("/(?<!');/",$line)){
					$j=0;
					for($i=1;$i<count($parts);$i++){
						$parts_value[$j]=$parts[$i];
						$j++;
						}
					$val1=implode("=",$parts_value);
					$val2=explode(";",$val1);
					$quotes[$row]=0;
					}
				else{
					$val2=explode(";",$parts[1]);
					$quotes[$row]=1;
					}
				$features[$row]["Value"]=$val2[0];
				}
			$row++;
			}
		if(preg_match($comm_line,$line)){
			$features[$row]["Description"].=$line;
			if(preg_match('/^\/\*/',$line) and !preg_match('/.*\*\//',$line)){
				$opencomment=true;
				}
			elseif(!preg_match('/^\/\*/',$line) and preg_match('/.*\*\//',$line)){
				$opencomment=false;
				}
			}
		if(!preg_match($comm_line,$line) and !preg_match($conf_line,$line) and !$opencomment){
			$previousline=$lineno-1;
			if((preg_match("/^.*(function|if)/",$line) or preg_match("/^\s*\s$/",$line)) and $blockstartno==0){
				if(!preg_match("/^.*\{.*\}/",$line)){
					$blockstart=true;
					$blockstartno=1;
					}
				else{
					$blockstart=false;
					$other[$row].=$line;
					$row++;
					}
				}
			if($blockstart==true and !preg_match("/^\s*\?>$/",$line)){
				if(preg_match($comm_line,$lines[$previousline])){
					for($i=$previousline;;$i--){
						if(!preg_match($comm_line,$lines[$i]) or ($i<$previousline and (preg_match("/^.*\*\//",$lines[$i]) or preg_match("/^\s*$/",$lines[$i])))){
							break;
							}
						$description[$i]=$lines[$i];
						}
					ksort($description);
					foreach($description as $key=>$l){
						if(!preg_match("/.*\/\*/",reset($description))){unset($description[$key]);}
						}
					foreach($description as $l){
						if(preg_match("/^.*(function|if)/",$l) or (count($description)==1 and !preg_match("/.*\/\*.*\*\//",$l))){
							break;
							}
						$other[$row].=$l;
						}
					}
				$other[$row].=$line;
				if($blockstartno>=1 and preg_match("/^.*\{/",$line) and !preg_match("/^.*\}/",$line)){
					$blockstartno++;
					}
				if($blockstartno>1 and preg_match("/^.*\}/",$line) and !preg_match("/^.*\{/",$line)){
					$blockstartno--;
					}
				if($blockstartno==1 and preg_match("/^.*\}/",$line) and !preg_match("/^.*\{/",$line)){
					$blockstart=false;
					$blockstartno=0;
					$row++;
					}
				}
			}

		$lineno++;
		}
	$school['lines']=$lines;
	$school['other']=$other;
	$school['features']=$features;
	return $school;
	}
?>
