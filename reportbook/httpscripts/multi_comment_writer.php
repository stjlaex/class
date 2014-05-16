<?php
/**                    httpscripts/comment_writer.php
 */

require_once('../../scripts/http_head_options.php');
require_once($CFG->dirroot.'/lib/statementbank.php');

if(isset($_GET['sid'])){$sid=$_GET['sid'];}
elseif(isset($_POST['sid'])){$sid=$_POST['sid'];}
if(isset($_GET['rid'])){$rid=$_GET['rid'];}
elseif(isset($_POST['rid'])){$rid=$_POST['rid'];}
if(isset($_GET['bid'])){$bid=$_GET['bid'];}
elseif(isset($_POST['bid'])){$bid=$_POST['bid'];}
if(isset($_GET['pid'])){$pid=$_GET['pid'];}
elseif(isset($_POST['pid'])){$pid=$_POST['pid'];}
if(isset($_GET['entryn'])){$entryn=$_GET['entryn'];}
elseif(isset($_POST['entryn'])){$entryn=$_POST['entryn'];}
if(isset($_GET['openid'])){$openid=$_GET['openid'];}

//The following code is taken from report_reports_list to get all reports
//used later to get student subjects -- this functonality may exist elsewhere
$rids=array();
$d_rid=mysql_query("SELECT categorydef_id AS report_id FROM ridcatid WHERE
    report_id='$rid' AND subject_id='wrapper' ORDER BY categorydef_id;");
$rids[]=$rid;//add to the start of the rids
while($rid=mysql_fetch_array($d_rid,MYSQL_ASSOC)){
    $rids[]=$rid['report_id'];
    }
$reportdefs=array();
$formVals=array();
foreach($rids as $rid){
    $reportdefs[]=(array)fetch_reportdefinition($rid);
    /*this is to feed the rids to the javascript function*/
    //print '<rids>'.$rid.'</rids>';
    //$input_elements.=' <input type="hidden" name="rids[]" value="'.$rid.'" />';
    }

$Student=fetchStudent_short($sid);
$yid=$Student['YearGroup']['value'];
//TODO ensure these permissions are valid
$yearperm=getYearPerm($yid);
$formperm=$yearperm;
?>

<?php 


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>ClaSS Comment Writer</title>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta http-equiv="Content-Script-Type" content="text/JavaScript" />
<meta name="copyright" content="Copyright 2002-2012 S T Johnson.  All trademarks acknowledged. All rights reserved" />
<meta name="version" content='<?php print "$CFG->version"; ?>' />
<meta name="licence" content="Affero General Public License version 3" />
<link rel="stylesheet" type="text/css" href="../../css/bookstyle.css" />
<link rel="stylesheet" type="text/css" href="../../css/commentwriter.css" />
<script src="../../js/jquery-1.8.2.min.js" type="text/javascript"></script>
<script src="../../js/editor.js" type="text/javascript"></script>
<script src="../../js/book.js" type="text/javascript"></script>

<script src="../../js/qtip.js" type="text/javascript"></script>
<?php
//$bver=(array)explode('.',$browser['version']);
?>
<script src="../../js/tinymce/tinymce.min.js" type="text/javascript"></script>
<!--<script src="../../lib/tiny_mce/loadeditor.js" type="text/javascript"></script>-->
<script src="../../js/commentwriter.js" type="text/javascript"></script>
</head>
<body onload="parent.loadRequired('reportbook');activateCommentEditor();">

    <div id="bookbox" class="newcommentwriter">
      <div id="heading">
        <label><?php print_string('student'); ?></label>
            <?php print $Student['DisplayFullName']['value'];?>
      </div>
      
      <div style="width:98%;left:0%;top:10%;position:relative;">
<?php

for ($index=0; $index < count($reportdefs); $index++) {
    $rid = $reportdefs[$index]['report']['id'];
    if ($reportdefs[$index]['report']['course_id'] == 'wrapper'){
        comment_box_form($rid, $sid, $bid, $pid, $entryn, $openid, $reportdefs[$index]);
        }
    else{
        $crid=$reportdefs[$index]['report']['course_id'];
        $addcomment=$reportdefs[$index]['report']['addcomment'];
        $compstatus=$reportdefs[$index]['report']['component_status'];
        $subjectclasses=(array)list_student_course_classes($sid,$reportdefs[$index]['report']['course_id']);
        foreach($subjectclasses as $class){
            $bid=$class['subject_id'];
            $cid=$class['id'];
            $d_teacher=mysql_query("SELECT teacher_id FROM tidcid WHERE class_id='$cid';");
            $reptids=array();
            $subjectperm['x']=0;
            while($teacher=mysql_fetch_array($d_teacher)){
                $reptids[]=$teacher['teacher_id'];	
                if($tid==$teacher['teacher_id']){$subjectperm['x']=1;}
                }
            $components=array();
            if($compstatus!='None'){
                $components=(array)list_subject_components($bid,$crid,$compstatus);
                }
            if(sizeof($components)==0){$components[]=array('id'=>' ','name'=>'');}
            foreach($components as $component){
                $pid=$component['id'];
                $strands=(array)list_subject_components($pid,$crid);

                $scoreno=0;
                $eidno=0;
                foreach($eids as $eid){
                    $eidno++;
                    $scoreno+=count_student_assessments($sid,$eid,$bid,$pid);
                    foreach($strands as $strand){
                        $scoreno+=count_student_assessments($sid,$eid,$bid,$strand['id']);
                        }
                    }
                
                $reportentryno=checkReportEntry($rid,$sid,$bid,$pid);
                /* removed permissions; if you can access */
                if($addcomment=='yes' ){
                    if($reportentryno==0){$reportentryno=1;$cssclass='class=""';}
                    else{$cssclass='class="special"';}
                    for($en=0;$en<$reportentryno;$en++){
                        $openid=$rid.'-'.$sid.'-'.$bid.'-'.$pid.'-'.$en;
                        if($success<1){
                            //foreach($reptids as $reptid){print $reptid.' ';}                
                            print get_subjectname($bid);
                            comment_box_form($rid, $sid, $bid, $pid, $entryn, $openid, $reportdefs[$index]);
                            }
                        }
                    }
                else {
                    //TODO add existing comments
                    }
                }
            }
            
        }
    }
?>
        </div>
    </div>
    <form id="vex-alert" style="display: none" class="vex-dialog-form">
        <div class="vex-dialog-message"><?print_string('leavewithoutsaving');?></div>
        <div class="vex-dialog-input">
            <input name="vex" type="hidden" value="_vex-empty-value">
        </div>
        <div class="vex-dialog-buttons">
            <button type="button" class="vex-dialog-button-primary vex-dialog-button vex-first">
            <?print_string('yes');?>
            </button>
            <button type="button" class="vex-dialog-button-secondary vex-dialog-button vex-last">
            <?print_string('cancel');?>
            </button>
        </div>
    </form>
</body>
</html>
