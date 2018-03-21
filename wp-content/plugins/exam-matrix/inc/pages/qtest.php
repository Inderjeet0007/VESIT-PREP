<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
// operation
if(isset($_REQUEST['setSubmit'])){
    $alert = $db->addSet($_REQUEST['exSet'],'Y');
}
if(isset($_REQUEST['subsetSubmit'])){
    $alert = $db->addSubset($_REQUEST['parentSet'],$_REQUEST['exSubset'],'Y');
}
if(isset($_REQUEST['setIdForDelete'])){
    $alert = $db->deleteSet($_REQUEST['setIdForDelete']);
}
if(isset($_REQUEST['setIdForShow'])){
    $parent_id = $_REQUEST['setIdForShow'];
    $subset = $db->showAllSubset($_REQUEST['setIdForShow']);
}
if(isset($_REQUEST['subsetIdForDelete'])){
    $alert = $db->deleteSubset($_REQUEST['subsetIdForDelete']);
}
$sets = $db->getAllSet();
?>
<div class="qWrap">
<br/><h2> Set, Subset & Question </h2><hr/><br/>
<?php if($alert['msg'] != ''){ ?>
<div class="alert <?php echo $alert['alert']; ?>">
    <p><strong><?php echo $alert['msg']; ?></strong></p>
</div>
<?php } ?>
<div class="ex-set">
    <div id="exSetBox" class="postbox ">
        <h3 class="hndle"><span>Add Question Set</span></h3>
      <div class="inside">
          <div class="main">
            <form method="post" action="">
                <p>
                  <label for="exSet">Set Name : </label>
                  <input type="text" id="exSet" name="exSet" />
                  <input type="submit" class="button button-primary" name="setSubmit" value="Add" />
              </p>
            </form>
        </div>
      </div>
    </div>
</div>
<div class="ex-subset">
    <div id="exSubsetBox" class="postbox">
        <h3 class="hndle"><span>Add Subset</span></h3>
      <div class="inside">
          <div class="main">
            <form method="post" action="" >
                <p>
                    <label for="parentSet">Select Parent Set : </label>
                    <select name="parentSet" id="parentSet">
                        <?php foreach($sets as $key => $value){ ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                        <?php } ?>                      
                    </select>
                </p>
                <p>
                  <label for="exSubset">Subset Name : </label>
                  <input type="text" id="exSubset" name="exSubset" />
                  <input type="submit" class="button button-primary" name="subsetSubmit" value="Add" />
              </p>
            </form>
        </div>
      </div>
    </div>
</div>
<!-- Set Portion -->
<?php if(count($sets) > 0){ ?>
<div class="addedsets">
    <div class="list-group">
        <ul>
            <li class="list-group-item active" style="font-size:16px;">Avaliable Sets</li>
            <?php foreach($sets as $key => $value){ ?>
                <li class="list-group-item item-set">
                        <form class="showSubset" id="showSubset-<?php echo $value->id; ?>" action="" method="post">
                            <input type="hidden" name="setIdForShow" value="<?php echo $value->id; ?>" />
                            <?php echo $value->name; ?>
                        </form>
                        <form class="deleteSet" id="deleteSet-<?php echo $value->id; ?>" action="" method="post">
                            <input type="hidden" name="setIdForDelete" value="<?php echo $value->id; ?>" />
                            <a class="close">x</a>
                        </form>
                </li>
             <?php } ?> 
        </ul>
    </div>
</div>
<?php } ?>
<!-- Subset Portion -->
<?php if(count($subset) > 0){ ?>
<div class="addedsets">
    <div class="list-group">
        <ul>
            <li class="list-group-item active" style="font-size:16px;"><?php echo $db->getSetName($parent_id); ?>'s Subset</li>
            <?php foreach($subset as $key => $value){ ?>
                <li class="list-group-item item-set">
                            <?php echo $value->name; ?>
                        <form class="deleteSubset" id="deleteSubset-<?php echo $value->id; ?>" action="" method="post">
                            <input type="hidden" name="subsetIdForDelete" value="<?php echo $value->id; ?>" />
                            <a class="close">x</a>
                        </form>
                </li>
             <?php } ?> 
        </ul>
    </div>
</div>
<?php } ?>
</div>
