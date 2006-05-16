<?php
/**		   					counter.php
 *	a simple log counter
 */

$choice='counter.php';
?>
  <div class="content">
	<table class="listmenu">
	  <tr>
		<th><?php print_string('username');?></th>
		<th><?php print_string('logcount',$book);?></th>
		<th><?php print_string('lastlogin',$book);?></th>
		<th><?php print_string('status');?></th>
	  </tr>	
<?php
$tot=0;
$d_user=mysql_query("SELECT uid, username, logcount,
		UNIX_TIMESTAMP(logtime) AS logtime FROM users ORDER BY logtime DESC");
while($user=mysql_fetch_array($d_user,MYSQL_ASSOC)){
	if($user{'logcount'}>0){
  	   $userid=$user['uid'];
	   $d_history=mysql_query("SELECT UNIX_TIMESTAMP(MAX(time))
				FROM history WHERE uid='$userid'");
	   $lasttime=mysql_result($d_history,0);
	   print "<tr><td>".$user{'username'}."</td><td>".$user{'logcount'}."</td>";
	   $userdate=date('Y-m-j',$user['logtime']);
	   $usertime=date('H:i:s',$user['logtime']);
	   print "<td>$userdate, $usertime</td>";
	   if(time() - $lasttime < mktime(0,15,0,0,0,1) - mktime(0,0,0,0,0,1)){
		   print '<td style="background-color:#ff6600;" >';
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
	   $tot=$user{'logcount'}+$tot;
	   }
	}
	  ?>
	  <tr>
		<td><?php print_string('total');?></td>
		<td><?php print $tot; ?></td>
		<td></td>
		<td><?php print $tot_on; ?></td>
	  </tr>
	</table>
  </div>