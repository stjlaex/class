<?php
/**									about.php
 *
 */
?>

<div class="content">

<fieldset class="center" id="splash">
<h2><img src="images/orangelogo.png" /></h2>
<h4>version <?php print $CFG->version; ?></h4>

<p> 
<?php print_string('classblurb',$book);?>
</p>

<p>
<?php print_string('formoreinformation',$book);?>
<button onClick="window.open('http://www.laex.org/class/index.html','ClaSS Homepage');">
ClaSS Homepage
</button>
</p>

<hr width="80%">

<p>
<?php print_string('gplintro',$book);?>
<button onClick="window.open('http://www.gnu.org/licenses/gpl.html','GPL License');">
GNU General Public License Version 2, June 1991
</button>
</p>
</fieldset>
</div>
