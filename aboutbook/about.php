<?php
/**									about.php
 *
 */
if($subtype=="about" or $subtype==""){
?>
<div class="content modal-about">
        <h4><img src="images/classis_transparent_220x92.png" onClick="window.open('http://www.laex.org/class/index.html','ClaSS Homepage');"/><br /> version <?php print $CFG->version; ?></h4>
    <p> 
        <?php print_string('classblurb',$book);?>
    </p>
    <p>
        <?php print_string('formoreinformation',$book);?>
    </p>
    <hr>
    <h4>
        <img onClick="window.open('http://learningdata.ie/','Classis');" alt="Classis" src="images/ld-logo.png" />
    </h4>
    <p>
        <?php print_string('gplintro',$book);?>
        <h4>
            <img onClick="window.open('http://www.gnu.org/licenses/agpl.html','AGPL License');" src="images/agplv3.png" alt="GNU Affero General Public License version 3 or (at your option) any later version" />
        </h4>
    </p>
</div>
<?php
	}
elseif($subtype=='thanks'){
?>
<div class="content modal-thanks">
    <h2>Thanks for joining us.</h2>
    <h3>You’re looking at a prototype of our new website. <br /> Opt-out any time by clicking “current version” at the bottom of the page. </h3>
    <h5>We love feedback - <a href="#" title="let us know yours.">let us know yours.</a></h5>
    <div class="navigation">
        <h6><a href="#gotit"><span class="fa fa-rocket"></span>Got it</a></h6>
        <p><a href="#old-version"><span class="fa fa-rocket"></span>Opt-out and return to the current version</a></p>
    </div>
    <p><a href="aboutbook.php?subtype=about"></span>About Class</a></p>
</div>
<?php
	}
?>

