<?php
#php coad area
if(isset($_POST['exImportCsv'])){
    if(!empty($_FILES)){
        $alert = $ie->import();
    }
}
if(isset($_POST['exExportCsv'])){
    if($_POST['exCsvToExport'] != 'NONE'){
        if($_POST['exCsvToExport'] == 'QUES'){
            $alert = $ie->exportQuestions();
        } elseif ($_POST['exCsvToExport'] == 'RESULT') {
            $alert = $ie->exportResults();
        }
    }
}
?>
<div class="qWrap">
    <br/><h2>Exam Matrix Import Export System</h2><br/>
    <?php if(isset($alert['alert'])){ ?>
    <div id="exammatrix-msg" class="alert <?php echo $alert['alert']; ?>"> 
        <p><strong><?php echo $alert['msg']; ?></strong></p>
    </div>
    <?php } ?>
    <!-- Import Box -->
    <div id="exImportBox" class="postbox ">
        <h3 class="hndle"><span>Import System</span></h3>
        <div class="inside">
                <div class="main">
                    <ul>
                        <li><p> Only CSV ( Comma Delimited ) file will be imported </p></li>
                        <li>
                            <form method="post" enctype='multipart/form-data'>
                                <input type="file" name="exCsvToImport" id="exCsvToImport" />
                                <input type="Submit" value="Import" class="button button-primary" name="exImportCsv" />
                            </form>
                        </li>
                        <li><p>Please <a href="<?php echo $ie->getExampleCSV(); ?>">download</a> a example csv file for verifying format of your csv file</p></li>
                    </ul>
                </div>
        </div>
    </div>
    <!-- Export Box -->
    <div id="exExportBox" class="postbox ">
    <h3 class="hndle"><span>Export System</span></h3>
        <div class="inside">
                <div class="main">
                    <ul>
                        <li><p> Output format will be CSV ( Comma Delimited ) </p></li>
                        <li>
                            <form method="post">
                                <select name="exCsvToExport">
                                    <option value="NONE">Select</option>
                                    <option value="QUES">Questions</option>
                                    <option value="RESULT">Results</option>
                                </select>
                                <input type="Submit" value="Export" class="button button-primary" name="exExportCsv" />
                            </form>
                        </li>
                        <li><p>Please download a example csv file for verifying format of your csv file</p></li>
                    </ul>
                </div>
        </div>
    </div>
    <!-- donation add -->
    <?php require_once(plugin_dir_path( __FILE__ ).'donation.php'); ?>
    <!-- end donation add -->
</div>