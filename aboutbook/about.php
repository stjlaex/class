<?php
/**									about.php
 *
 */
?>
<div class="content modal-about">
        <h4><img src="images/classis_transparent_220x92.png" onClick="window.open('http://learningdata.ie/classis-school-information-management-system/help-centre/','Classis Support Centre');"/><br /> version <?php print $CFG->version; ?></h4>
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
