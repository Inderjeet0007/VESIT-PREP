<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
if(isset($_REQUEST['questionIdForDelete'])){
    $alert = $db->deleteQuestion($_REQUEST['questionIdForDelete']);
}
$questions = $db->getRecentQuestions();
$sets = $db->getAllSet();
if(isset($_REQUEST['selectedSetIdForFilter'])){
    $selectedSetId = $_REQUEST['selectedSetIdForFilter'];
    $subset = $db->showAllSubset($_REQUEST['selectedSetIdForFilter']);
}
if(isset($_REQUEST['submitFilter'])){
    $questions = $db->applyFilter($_REQUEST['set'],$_REQUEST['subset']);
    if(isset($questions['alert'])){
        $alert = $questions;
        $questions = 0;
    }
}
?>
<div class="qWrap">
<br/><h2> Manage Questions </h2><hr/><br/>
<?php if($alert['msg'] != ''){ ?>
<div class="alert <?php echo $alert['alert']; ?>">
    <p><strong><?php echo $alert['msg']; ?></strong></p>
</div>
<?php } ?>
<!-- Hidden Form -->
<div style="display:none !important;">
    <form id="submitSelectedSetId" method="post" action="">
        <input type="hidden" id="selectedSetId" name="selectedSetIdForFilter" value="" />
    </form>
</div>
<!-- End Hidden Form -->
<!-- Filter -->
<div class="ex-question">
    <div id="exManageBox" class="postbox">
        <h3 class="hndle"><span>Question Filter</span></h3>
      <div class="inside">
          <div class="main">
            <form method="post" action="">
                <ul class="filter">
                    <li>
                      <label for="superSet">Parent Set : </label>
                      <select id="superSet" name="set">
                            <option value="NOSELECT">Select Set</option>
                          <?php foreach($sets as $key => $value){ ?>
                            <option <?php if($selectedSetId==$value->id){ echo 'selected="selected"'; } ?> value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                           <?php } ?>
                      </select>
                    </li>
                    <li>
                      <label for="set">Subset Name : </label>
                      <select id="subSet" name="subset">
                          <option value="NOSELECT">Select Subset</option>
                          <?php foreach($subset as $key => $value){ ?>
                            <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                           <?php } ?>
                      </select>
                    </li>
                    <li>
                      <input type="submit" class="button button-primary" name="submitFilter" value="Filter Questions" />
                    </li>
                </ul>
            </form>
        </div>
      </div>
    </div>
</div>
<!-- Recent Questions -->    
<?php if($questions){ ?>
<div class="questions">
    <div class="list-group">
        <ul>
            <li class="list-group-item active" style="font-size:16px;">Available Questions</li>
            <?php foreach($questions as $key => $value){ ?>
                <li class="list-group-item item-set">
                    <ul>
                        <li> <?php echo $value->id; ?> </li>
                        <li style="width: 450px;"><?php echo substr($value->question,0,50); ?></li>
                        <li><?php echo $db->getSetName($value->set); ?></li>
                        <li><?php echo $db->getSubsetName($value->subset); ?></li>
                    </ul>
                        <?php echo $value->name; ?>
                    <form class="deleteQuestion" id="deleteQuestion-<?php echo $value->id; ?>" action="" method="post">
                        <input type="hidden" name="questionIdForDelete" value="<?php echo $value->id; ?>" />
                        <a class="close">x</a>
                    </form>
                </li>
             <?php } ?> 
        </ul>
    </div>
</div>
<?php } ?>
<!-- donation add -->
<?php require_once(plugin_dir_path( __FILE__ ).'donation.php'); ?>
<!-- end donation add -->
</div>