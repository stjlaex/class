<?php 
/** 									column_level.php
 */

$action='column_level_action.php';

if(!isset($_POST{'checkmid'})){
		$action='class_view.php';
		$result[]='Choose a column to level!';
		include('scripts/results.php');
	   	include('scripts/redirect.php');
	   	exit;
		}

$mids=$_POST{'checkmid'};

if(sizeof($mids)>1){
		$action='class_view.php';
		$result[]='Choose only one column to level!';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

$mid=$mids[0];
$d_mark=mysql_query("SELECT * FROM mark WHERE id='$mid'");
$mark=mysql_fetch_array($d_mark,MYSQL_ASSOC);

if($mark['marktype']!='score'){
		$action='class_view.php';
		$result[]='It is not possible to level this type ('.$mark['marktype'].') of column!';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}

$markdefname=$mark['def_name'];
$d_markdef=mysql_query("SELECT * FROM markdef WHERE name='$markdefname'");
$markdef=mysql_fetch_array($d_markdef,MYSQL_ASSOC);

if($markdef['scoretype']!='percentage' and $markdef['scoretype']!='value'){
		$action='class_view.php';
		$result[]='It is not possible to level this type ('.$markdef['scoretype'].') of values!';
		include('scripts/results.php');
		include('scripts/redirect.php');
		exit;
		}


	$levelling=array();
/*	select levelling schemes by the crid/bid of the displayed classes*/
	for($c=0;$c<sizeof($cids);$c++){
		$cid=$cids[$c];	
		$d_cridbid=mysql_query("SELECT subject_id, course_id 
									FROM class WHERE id='$cid'");
		$bid=mysql_result($d_cridbid,0,0);
		$crid=mysql_result($d_cridbid,0,1);
		$d_levelling=mysql_query("SELECT * FROM levelling
					WHERE (subject_id LIKE '$bid' OR subject_id='%') 
						AND (course_id LIKE '$crid' OR course_id='%')");
		$c2=0;
		while ($new=mysql_fetch_array($d_levelling,MYSQL_ASSOC)){
			if(!in_array($new,$levelling)){
				$levelling[$c2]=$new;
				$c2++;
				}
			}
		}

three_buttonmenu();
?>
	<div class="content">
	  <form id="formtoprocess" name="formtoprocess" method="post" action="<?php print $host;?>"> 
		<table class="listmenu">
		  <caption>Choose a Levelling Scheme</caption>
<?php
    	for($c=0;$c<sizeof($levelling);$c++){
?>
		<tr>
		  <td>
			<input type="radio" name="lena" id="lena"
			  tabindex="<?php print $c;?>" value="<?php print
				$levelling[$c]['name'];?>" />
		  </td>
		  <td>
			<?php print $levelling[$c]['name'];?> 
		  </td>
		  <td>
			<?php print $levelling[$c]['comment'];?></td>
		  <td>
			<?php print $levelling[$c]['levels'];?></td>
		</tr>
<?php
			}
?>			
		<tr class="special">
		  <td><input type="radio" name="lena" id="lena" value="new" /></td>
		  <td>NEW scheme</td>
		  <td></td>
		  <td>Create a New Levelling Scheme</td>
		</tr>
	  </table>

	<input type="hidden" name="mid" value="<?php print $mid; ?>" />
	<input type="hidden" name="markdefname" value="<?php print $markdefname; ?>" />
	<input type="hidden" name="bid" value="<?php print $bid; ?>" />			
	<input type="hidden" name="crid" value="<?php print $crid; ?>" />			
	<input type="hidden" name="current" value="<?php print $action;?>" />
	<input type="hidden" name="choice" value="<?php print $choice;?>" />
	<input type="hidden" name="cancel" value="<?php print $choice;?>" />
	  </form>  				
	</div>
