<?php
/**								usage.php
 *	a simple log counter
 */

$choice='usage.php';
$action='usage.php';

include('scripts/sub_action.php');

$extrabuttons['usagestatistics']=array('name'=>'current','value'=>'usage_statistics.php');
two_buttonmenu($extrabuttons);
?>

  <div id="viewcontent" class="content">
	<table class="listmenu">
	  <tr>
		<th><?php print_string('username');?></th>
		<th><?php print_string('logcount',$book);?></th>
		<th><?php print_string('lastlogin',$book);?></th>
		<th><?php print_string('status');?></th>
	  </tr>	
<?php
$tot=0;
$tot_on=0;

$d_user=mysql_query("SELECT uid, username, logcount,
                UNIX_TIMESTAMP(logtime) AS logtime FROM users ORDER BY logtime DESC");
while($user=mysql_fetch_array($d_user,MYSQL_ASSOC)){
	if($user['logcount']>0){
  	   $userid=$user['uid'];
	   $d_history=mysql_query("SELECT UNIX_TIMESTAMP(MAX(time))
				FROM history WHERE uid='$userid'");
	   $lasttime=mysql_result($d_history,0);
	   print "<tr><td>".$user['username']."</td><td>".$user['logcount']."</td>";
	   $userdate=date('Y-m-j',$user['logtime']);
	   $usertime=date('H:i:s',$user['logtime']);
	   print "<td>$userdate, $usertime</td>";
	   if(time() - $lasttime < mktime(0,15,0,0,0,1) - mktime(0,0,0,0,0,1)){
		   print '<td>';
		   print_string('online',$book);
		   print '</td>';
		   $tot_on++;
		   }
	   else{
	   print '<td>';
	   print_string('offline',$book);
	   print '</td>';
				}
	   print '</tr>';
	   $tot=$user['logcount']+$tot;
	   }
	}
	  ?>
	  <tr>
		<td><?php print_string('total');?></td>
		<td><?php print $tot; ?></td>
		<td>&nbsp</td>
		<td><?php print $tot_on; ?></td>
	  </tr>
	</table>
 	  <form id="formtoprocess" name="formtoprocess" method="post"
		action="<?php print $host; ?>" >

	  <input type="hidden" name="current" value="<?php print $action;?>" />
	  <input type="hidden" name="choice" value="<?php print $choice;?>" />
	  <input type="hidden" name="cancel" value="<?php print '';?>" />
	</form>

  </div>
