<?php
/**									about.php
 *
 */
?>

<div class="content">

<fieldset class="center" id="splash">

<h4><img src="images/orangelogo.png" onClick="window.open('http://www.laex.org/class/index.html','ClaSS Homepage');"/> &nbsp;&nbsp;&nbsp;version <?php print $CFG->version; ?></h4>

<p> 
<?php print_string('classblurb',$book);?>
</p>

<p>
<?php print_string('formoreinformation',$book);?>
</p>

<hr width="80%">

<p>
<img onClick="window.open('http://classis.co.uk/support','ClaSS IS');"
alt="ClaSS IS" src="images/classis_transparent_120x100.png" />
</p>

<p>
<?php print_string('gplintro',$book);?>
<img onClick="window.open('http://www.gnu.org/licenses/agpl.html','AGPL License');" src="images/agplv3-88x31.png"
alt="GNU Affero General Public License version 3 or (at your option) any later version" />
</p>
</fieldset>
</div>
