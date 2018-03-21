<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php
    $sets = $dba->getAllSet();
?>
<?php if($sets){ ?>
<div class="control-group">
    <label class="control-label">Select Question Set: </label>
    <div class="controls">
        <select name="_eme_selected_set">
            <?php foreach($sets as $k=>$v){ ?>
            <option <?php if($v->id == $data['_eme_selected_set']){ echo 'selected=selected'; } ?> value="<?php echo $v->id; ?>"><?php echo $v->name; ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php } else { ?>
<div class="control-group">
    <label class="control-label" style="color:red;">Please Define At lease one set: </label>
</div>
<?php } ?>
<div class="control-group">
    <label class="control-label">Negative Marking: </label>
    <div class="controls">
        <label class="checkbox">
            <input type="checkbox" name="_eme_negative_marking" <?php if($data['_eme_negative_marking']=='Y'){ ?> checked="checked" <?php } ?> value="Y"> Yes
        </label>
    </div>
</div>
<div class="control-group">
    <label class="control-label">Show Random Question: </label>
    <div class="controls">
        <label class="checkbox ex_check">
            <input type="checkbox" name="_eme_show_random" <?php if($data['_eme_show_random']=='Y'){ ?> checked="checked" <?php } ?> value="Y"> Yes
        </label>
    </div>
</div>