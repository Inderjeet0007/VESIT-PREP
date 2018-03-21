<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ImportExport
 *
 * @author Emerico
 */
namespace ExamMatrix;
class ImportExport {
    private $filename = null;
    private $upload_path = null;
    private $output_file = null;
    function __construct(){
        $this->createUploadDirectory();
    }
    function Import(){
        global $wpdb, $table_prefix;
        $alert = $this->uploadFile();
        if(trim($alert['msg'])=='Success'){
            $db = new Database();
            $csv = new CSV();
            $csv->auto($this->upload_path.$this->filename);
            foreach ($csv->data as $key => $row){
                $set_id = $db->getSetId(trim($row['set']));
                if($set_id > 0){
                    $subset_id = $db->getSubsetId(trim($row['subset']),$set_id);
                    if($subset_id > 0){
                        
                    } else {
                        $db->addSubset($set_id, trim($row['subset']), 'Y');
                        $subset_id = $db->getSubsetId(trim($row['subset']),$set_id);
                    }
                } else {
                    $db->addSet(trim($row['set']), 'Y');
                    $set_id = $db->getSetId(trim($row['set']));
                    $db->addSubset($set_id, trim($row['subset']), 'Y');
                    $subset_id = $db->getSubsetId(trim($row['subset']),$set_id);
                }
                $wpdb->insert( 
                        $table_prefix.'ex_questions', 
                        array( 
                                'set' => $set_id,
                                'subset' => $subset_id,
                                'question' => trim($row['question']),
                                'opt1' => trim($row['opt1']),
                                'opt2' => trim($row['opt2']),
                                'opt3' => trim($row['opt3']),
                                'opt4' => trim($row['opt4']),
                                'answer' => trim($row['answer']),
                                'multi' => trim($row['multi'])
                        )
                );
            }
            $this->deleteFile();
        } else {
            return $alert;
        }
    }
    // uploading file
    function uploadFile(){
        $extension = end(explode(".", $_FILES["exCsvToImport"]["name"]));
        $upload_path = $this->getUploadPath();
        if($extension == 'csv'){
            if ($_FILES["exCsvToImport"]["error"] > 0){
                    return array('alert'=>'alert-danger','msg'=>'FILE ERROR:: '.$_FILES["file"]["error"]);
                }
            else{
                $this->filename = 'File_'.rand(5000,50000).'.csv';
                if (file_exists($upload_path.$this->filename)){
                    return array('alert'=>'alert-info','msg'=>'File Already Exist');
                  }
                else{
                  move_uploaded_file($_FILES["exCsvToImport"]["tmp_name"],$upload_path.$this->filename);
                    return array('alert'=>'alert-success','msg'=>'Success');
                  }
            }
        } else {
            return array('alert'=>'alert-danger','msg'=>'FILE ERROR:: Invalid file extension ! ');
        }
    }
    // creating upload directory
    function createUploadDirectory(){
        $upload_path = wp_upload_dir();
        $upload_path = $upload_path['basedir'];
        $dir_name = 'ExamMatrixUploads';
        $full_path = $upload_path.'/'.$dir_name;
        try{
            if (!file_exists($full_path)) {
                mkdir($full_path, 0777, true);
            }
        } catch(Exception $e){
            return;
        }
    }
    // upload path
    function getUploadPath(){
        $upload_path = wp_upload_dir();
        $upload_path = $upload_path['basedir'];
        $dir_name = 'ExamMatrixUploads';
        $full_path = $upload_path.'/'.$dir_name.'/';
        $this->upload_path = $full_path;
        return $full_path;
    }
    function buildDownloadPath(){
        $upload_path = wp_upload_dir();
        $upload_path = $upload_path['baseurl'];
        $dir_name = 'ExamMatrixUploads';
        $full_path = $upload_path.'/'.$dir_name.'/';
        return $full_path;
    }
    // example file url
    function getExampleCSV(){
        $plugin_dir_name = explode('/',plugin_basename(__FILE__));
        $plugin_dir_name = $plugin_dir_name[0];
        return plugins_url( $plugin_dir_name.'/templates/csv/example.csv');
    }
    // delete file
    function deleteFile(){
        $file_path = $this->upload_path;
        $file_path = $file_path.$this->filename;
        if (file_exists($file_path)){
            unlink($file_path);
        }
    }
    // delete all temporary output
    function deleteAllTemps($path){
        $files = glob($path.'*'); // get all file names
        foreach($files as $file){ // iterate files
          if(is_file($file))
            unlink($file); // delete file
        }
    }
    // Export Questions
    function exportQuestions(){
        $db = new Database();
        $data = $this->formatData($db->getAllQuestions());
        $alert = $this->creatCSV($data);
        if(isset($alert['alert'])){
            return $alert;
        } else {
            return array('alert'=>'alert-danger','msg'=>'There are some error in file genetation. Try Again !');
        }
    }
    // export results
    function exportResults(){
        $db= new Database();
        $data = $this->formatData($db->getAllResult());
        $alert = $this->creatCSV($data);
        if(isset($alert['alert'])){
            return $alert;
        } else {
            return array('alert'=>'alert-danger','msg'=>'There are some error in file genetation. Try Again !');
        }
    }
    // create csv file
    function creatCSV($data){
        $upload_path = $this->getUploadPath();
        $this->deleteAllTemps($upload_path);
        $file_name = 'output_'.rand(5000,50000).'.csv';
        $file_path = $upload_path.$file_name;
        $download_path = $this->buildDownloadPath().$file_name;
        try{
            $fp = fopen($file_path, 'w');
            fputs($fp,$data);
            fclose($fp);
        } catch(Exception $e){
            return;
        }
        return array('alert'=>'alert-success','msg'=>'EXPORT SUCCESSFULL ::  Download file from <a href="'.$download_path.'" >here</a> ');
    }
    // data formation to csv
    function formatData($array){
        $header_row = true;
        $col_sep = ","; 
        $row_sep = "\n";
        $qut = '"';
        // formatting 
        if (!is_array($array) or !is_array($array[0])){ 
            return false;
        }
	//Header row.
	if ($header_row){
		foreach ($array[0] as $key => $val)
		{
			//Escaping quotes.
			$key = str_replace($qut, "$qut$qut", $key);
			$output .= "$col_sep$qut$key$qut";
		}
		$output = substr($output, 1)."\n";
	}
	//Data rows.
	foreach ($array as $key => $val){
		$tmp = '';
		foreach ($val as $cell_key => $cell_val){
			//Escaping quotes.
			$cell_val = str_replace($qut, "$qut$qut", $cell_val);
			$tmp .= "$col_sep$qut$cell_val$qut";
		}
		$output .= substr($tmp, 1).$row_sep;
	}
	return $output;
    }
}
