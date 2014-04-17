<?php
/**									about.php
 *
 */
if($subtype=="about" or $subtype=="" or !isset($CFG->theme20)){
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
elseif($subtype=='thanks' and (isset($CFG->theme20) and $CFG->theme20!="")){
?>
<div class="content modal-thanks">
    <h2><?php print_string('thankstitle',$book);?></h2>
    <h3><?php print_string('thanksmessage',$book);?></h3>
    <h5><?php print_string('thankssurveylink',$book);?></a></h5>
    <div class="navigation">
        <p><a href="#" onclick="parent.window.location.href='../<?php echo $CFG->theme10;?>';"><span class="fa fa-rocket"></span><?php print_string('oldclassislink',$book);?></a></p>
    </div>
    <p><a href="aboutbook.php?subtype=about"></span><?php print_string('aboutclassis',$book);?></a></p>
</div>
<?php
	}
?>

