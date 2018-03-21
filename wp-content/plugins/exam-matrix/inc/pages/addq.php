<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
$sets = $db->getAllSet();
if(isset($_REQUEST['selectedSetId'])){
    $selectedSetId = $_REQUEST['selectedSetId'];
    $subset = $db->showAllSubset($_REQUEST['selectedSetId']);
}
if(isset($_REQUEST['addQuestion'])){
    $alert = $db->addQuestion($_REQUEST);
}
?>
<div class="qWrap">
<br/><h2> Add Questions </h2><hr/><br/>
<?php if($alert['msg'] != ''){ ?>
<div class="alert <?php echo $alert['alert']; ?>">
    <p><strong><?php echo $alert['msg']; ?></strong></p>
</div>
<?php } ?>
<!-- Hidden Form -->
<div style="display:none !important;">
    <form id="submitSelectedSetId" method="post" action="">
        <input type="hidden" id="selectedSetId" name="selectedSetId" value="" />
    </form>
</div>
<!-- End Hidden Form -->
<!-- Content-->
<div class="ex-add-quest">
    <div id="exAddQBox" class="postbox ">
        <h3 class="hndle"><span>Add Question</span></h3>
      <div class="inside">
          <div class="main">
            <form method="post" action="">
                <p>
                  <label for="set">Set Name : </label>
                  <select id="superSet" name="set">
                        <option value="NOSELECT">Select Set</option>
                      <?php foreach($sets as $key => $value){ ?>
                        <option <?php if($selectedSetId==$value->id){ echo 'selected="selected"'; } ?> value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                       <?php } ?>
                  </select>
               </p>
               <p>
                  <label for="set">Subset Name : </label>
                  <select id="subSet" name="subset">
                      <option value="NOSELECT">Select Subset</option>
                      <?php foreach($subset as $key => $value){ ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                       <?php } ?>
                  </select>
               </p>
               <p><?php wp_editor('Add your question here', 'question'); ?></p>
               <br/>
               <p><input type="checkbox" value="Y" name="multi" id="exMulti" />  Has Multiple Answers ?</p>
               <p><h3>Add Answers</h3></p>
               <!-- First -->
               <p>
                   <label for="opt1">Option One</label><br/>
                   <textarea name="opt1" style="margin: 1px; width: 480px; height: 50px;"></textarea>
                   <span class="correctOption">
                       <input type="checkbox" value="opt1" name="answer[]" id="answer1" />
                       <label for="answer1">Correct Option</label>
                   </span>
               </p>
               <!-- Second -->
               <p>
                   <label for="opt2">Option Two</label><br/>
                   <textarea name="opt2" style="margin: 1px; width: 480px; height: 50px;"></textarea>
                   <span class="correctOption">
                       <input type="checkbox" value="opt2" name="answer[]" id="answer2" />
                       <label for="answer2">Correct Option</label>
                   </span>
               </p>
               <!-- Third -->
               <p>
                   <label for="opt3">Option Three</label><br/>
                   <textarea name="opt3" style="margin: 1px; width: 480px; height: 50px;"></textarea>
                   <span class="correctOption">
                       <input type="checkbox" value="opt3" name="answer[]" id="answer3" />
                       <label for="answer3">Correct Option</label>
                   </span>
               </p>
               <!-- fourth -->
               <p>
                   <label for="opt4">Option Four</label><br/>
                   <textarea name="opt4" style="margin: 1px; width: 480px; height: 50px;"></textarea>
                   <span class="correctOption">
                       <input type="checkbox" value="opt4" name="answer[]" id="answer4" />
                       <label for="answer4">Correct Option</label>
                   </span>
               </p>
               <p><input type="submit" style="margin: auto;display: block;" class="button button-primary" name="addQuestion" value="Add Question" /></p>
            </form>
        </div>
      </div>
    </div>
</div>
<!-- End Content -->
<!-- donation add -->
<?php require_once(plugin_dir_path( __FILE__ ).'donation.php'); ?>
<!-- end donation add -->
</div>