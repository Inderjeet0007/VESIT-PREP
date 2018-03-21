<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db
 *
 * @author Emerico
 */
namespace ExamMatrix;
class Database {
    private $tblSet;
    private $tblSubset;
    private $tblQuestions;
    private $tblResults;
    private $tblMapping;
    private $tblPostmeta;
    private $tblSession;
   function __construct(){
       global $table_prefix;
       $this->tblSet = $table_prefix.'ex_set';
       $this->tblSubset = $table_prefix.'ex_subset';
       $this->tblQuestions = $table_prefix.'ex_questions';
       $this->tblResults = $table_prefix.'ex_result';
       $this->tblMapping = $table_prefix.'ex_mapping';
       $this->tblPostmeta = $table_prefix.'postmeta';
       $this->tblSession = $table_prefix.'ex_session';
   }
   public function validate($value,$type,$parent){
       global $wpdb;
       if($type=='set'){
          $chk = $wpdb->get_var("SELECT id FROM $this->tblSet WHERE name='$value'");
       }
       if($type=='subset'){
          $chk = $wpdb->get_var("SELECT id FROM $this->tblSubset WHERE name='$value' AND parent_id=$parent");
       }
       return $chk;
   }
   public function getSetName($id){
       global $wpdb;
       if($id == '')
           return 'Wrong ID';
       return $wpdb->get_var("SELECT name FROM $this->tblSet WHERE id=$id");
       
   }
   public function getSetId($name){
       global $wpdb;
       if(trim($name) == '')
           return 'Wrong Name';
       return $wpdb->get_var("SELECT id FROM $this->tblSet WHERE `name`='".$name."'");
       
   }
   public function addSet($name,$status){
       global $wpdb;
       if($name == '')
           return array('alert'=>'alert-danger','msg'=>'Set Name is empty');
       if($this->validate($name,'set',0))
           return array('alert'=>'alert-info','msg'=>'Set Name already exist');
       if($status == '')
           $status = 'Y'; 
       $wpdb->insert( 
                $this->tblSet, 
                array( 
                        'name' => $name, 
                        'status' => $status 
                ), 
                array( 
                        '%s', 
                        '%s' 
                ) 
        );
       return array('alert'=>'alert-success','msg'=>'Sub Added !!');
   }
   public function deleteSet($id){
       global $wpdb;
       $wpdb->delete( $this->tblSet, 
                      array( 'id' => $id ), 
                      array( '%d' ) 
               );
       return array('alert'=>'alert-success','msg'=>'Set Deleted');
   }
   public function getAllSet(){
       global $wpdb;
       $sets = $wpdb->get_results( 
                        "
                        SELECT * FROM $this->tblSet
                        "
                );
       return $sets;
   }
   public function addSubset($parent,$name,$status){
       global $wpdb;
       if($name == '')
           return array('alert'=>'alert-danger','msg'=>'Subset Name is empty');
       if($this->validate($name,'subset',$parent))
           return array('alert'=>'alert-info','msg'=>'Subset Name already exist');
       if($parent == '')
           return array('alert'=>'alert-danger','msg'=>'Select Parent Set');
       if($status == '')
           $status = 'Y'; 
       $wpdb->insert( 
                $this->tblSubset, 
                array( 
                        'parent_id'=> $parent,
                        'name' => $name, 
                        'status' => $status 
                ), 
                array( 
                        '%d',
                        '%s', 
                        '%s' 
                ) 
        );
       return array('alert'=>'alert-success','msg'=>'Subset Added !!');
   }
   public function showAllSubset($id){
       global $wpdb;
       $sets = $wpdb->get_results( 
                        "
                        SELECT * FROM $this->tblSubset
                            WHERE parent_id=$id
                        "
                );
       return $sets;
   }
   public function getSubsetName($id){
       global $wpdb;
       if($id == '')
           return 'Wrong ID';
       return $wpdb->get_var("SELECT name FROM $this->tblSubset WHERE id=$id");
       
   }
   public function getSubsetId($name,$parent){
       global $wpdb;
       if(trim($name) == '')
           return 'Wrong Name';
       if(empty($parent))
           return 'Null Parent Id';
       return $wpdb->get_var("SELECT `id` FROM $this->tblSubset WHERE `name`='".$name."' AND `parent_id`=$parent");
       
   }
   public function deleteSubset($id){
       global $wpdb;
       $wpdb->delete( $this->tblSubset, 
                      array( 'id' => $id ), 
                      array( '%d' ) 
               );
       return array('alert'=>'alert-success','msg'=>'Subsetet Deleted');
   }
   public function addQuestion($data){
       global $wpdb;
       if(!is_array($data))
           return array('alert'=>'alert-info','msg'=>'Please submit complete data !!');
       if($data['set']=='' || $data['set'] == 'NOSELECT')
           return array('alert'=>'alert-danger','msg'=>'Please select set !!');
       if($data['subset']=='' || $data['subset'] == 'NOSELECT')
           return array('alert'=>'alert-danger','msg'=>'Please select subset or create one for belonging set !!');
       if($data['question']=='')
           return array('alert'=>'alert-danger','msg'=>'Enter question !!');
       if($data['opt1']=='')
           return array('alert'=>'alert-danger','msg'=>'Option one is empty !!');
       if($data['opt2']=='')
           return array('alert'=>'alert-danger','msg'=>'Option two is empty !!');
       if($data['opt3']=='')
           return array('alert'=>'alert-danger','msg'=>'Option three is empty !!');
       if($data['opt4']=='')
           return array('alert'=>'alert-danger','msg'=>'Option four is empty !!');
       if(count($data['answer'])==0)
           return array('alert'=>'alert-danger','msg'=>'Please select correct answer for this question !!');
       $multi = 'N';
       if(isset($data['multi']))
           $multi = $data['multi'];
       foreach($data['answer'] as $k=>$v){
           $answer .= $v.'-';
       }
       $answer = rtrim($answer, "-");
       $wpdb->insert( 
                $this->tblQuestions, 
                array( 
                        'set' => $data['set'], 
                        'subset' => $data['subset'], 
                        'question' => $data['question'],
                        'opt1' => $data['opt1'],
                        'opt2' => $data['opt2'],
                        'opt3' => $data['opt3'],
                        'opt4' => $data['opt4'],
                        'answer' => $answer,
                        'multi' => $multi
                ), 
                array( 
                        '%d', 
                        '%d',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s',
                        '%s'
                ) 
        );
       return array('alert'=>'alert-success','msg'=>'Question Added !!');
   }
   public function getRecentQuestions(){
       global $wpdb;
       $questions = $wpdb->get_results( 
                        "
                        SELECT * FROM $this->tblQuestions
                            LIMIT 0,20
                        "
                );
       return $questions;
   }
   public function deleteQuestion($id){
       global $wpdb;
       $wpdb->delete( $this->tblQuestions, 
                      array( 'id' => $id ), 
                      array( '%d' ) 
               );
       return array('alert'=>'alert-success','msg'=>'Question Deleted');
   }
   public function applyFilter($onSet,$onSubset){
       global $wpdb;
       if($onSet == '' || $onSet == 'NOSELECT')
           return array('alert'=>'alert-info','msg'=>'Data Incomplete !!');
       if($onSubset == '' || $onSubset == 'NOSELECT')
           return array('alert'=>'alert-info','msg'=>'Data Incomplete !!');
       $onSet = intval($onSet);
       $onSubset = intval($onSubset);
       $questions = $wpdb->get_results( 
                        "
                        SELECT * FROM $this->tblQuestions
                            WHERE `set` = $onSet AND `subset` = $onSubset
                        "
                );
       return $questions;
   }
   public function getAllTestByUsername($username){
       global $wpdb;
       if(empty($username))
           return array('alert'=>'alert-info','msg'=>'Username is empty !!');
       $us = get_userdatabylogin($username);
       $test = $wpdb->get_results( 
                        " SELECT * FROM $this->tblResults WHERE `userID` = $us->ID LIMIT 0,20 "
                );
       
       return $test;
   }
   public function getAllTestByRegID($regid){
       global $wpdb;
       if(empty($regid))
           return array('alert'=>'alert-info','msg'=>'REG ID is empty !!');
       $test = $wpdb->get_results( 
                        " SELECT * FROM $this->tblResults WHERE `regID` = '$regid' LIMIT 0,20 "
                );
       
       return $test;
   }
   public function getExamName($regid){
       global $wpdb,$post;
       if(empty($regid))
           return array('alert'=>'alert-info','msg'=>'REG ID is empty !!');
       $metavalue = $wpdb->get_var("SELECT testID FROM $this->tblMapping WHERE `regID`='$regid'");
       $postid = $wpdb->get_var("SELECT post_id FROM $this->tblPostmeta WHERE `meta_key`='_eme_selected_set' AND `meta_value`=$metavalue");
       return get_the_title($postid);
   }
   public function getTestDetail($regid){
       global $wpdb;
       if(empty($regid))
           return 0;
       $userid = $wpdb->get_var("SELECT userID FROM $this->tblMapping WHERE `regID`='$regid'");
       $uData = get_userdata($userid);
       $dt = $wpdb->get_var("SELECT date FROM $this->tblMapping WHERE `regID`='$regid'");
       $detail = $wpdb->get_results( 
                        "
                        SELECT * 
                        FROM  `$this->tblSession` 
                        WHERE regID =  '$regid'
                        "
                );
       $return = array();
       foreach($detail as $k=>$v){
           $row = $wpdb->get_row("SELECT * FROM $this->tblQuestions WHERE id = {$v->question}", ARRAY_A);
           $return[$v->question] = array(
                    'answer' => $v->answer,
                    'question' => $row['question'],
                    'correct_answer' => $row['answer']
           );
       }
       return $test = array(
                    'msg' =>$uData->first_name.' '.$uData->last_name.' has taken this test on date '.$dt,
                    'quest' => $return
            );
   }
   public function getAllQuestions(){
       global $wpdb;
       $questions = $wpdb->get_results("SELECT * FROM $this->tblQuestions",'ARRAY_A');
       foreach($questions as $key=>$value){
           unset($questions[$key]['id']);
           $questions[$key]['set'] = $this->getSetName($value['set']);
           $questions[$key]['subset'] = $this->getSubsetName($value['subset']);
       }
       return $questions;
   }
   public function getAllResult(){
       global $wpdb;
       $results = $wpdb->get_results("SELECT * FROM $this->tblResults",'ARRAY_A');
       foreach($results as $key=>$value){
           unset($results[$key]['id']);
       }
       return $results;
   }
}

?>
