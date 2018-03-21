<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace ExamMatrix;

/**
 * Description of DigitalClock
 *
 * @author Udit Rawat
 */
class DigitalClock {
    private $noLimit;
    public function __construct(){
        global $wpdb,$post;
        $this->noLimit = FALSE;
        if(!get_post_meta($post->ID,'_eme_estimated_time',true))
            $this->noLimit = TRUE; 
    }
    public function _showClock($for){ ?>
        <div id="exTimer"></div>
        <script type="text/javascript">
            var TimeLimit = new Date('<?php echo $this->_getTimeLimit(); ?>');
            var forForm = '<?php echo $for; ?>';
            function countdownto() {
              var date = Math.round((TimeLimit-new Date())/1000);
              var hours = Math.floor(date/3600);
              date = date - (hours*3600);
              var mins = Math.floor(date/60);
              date = date - (mins*60);
              var secs = date;
              if (hours<10) hours = '0'+hours;
              if (mins<10) mins = '0'+mins;
              if (secs<10) secs = '0'+secs;
              document.getElementById('exTimer').innerHTML = hours+':'+mins+':'+secs;
              if(hours=='00' && mins == '00' && secs == '00'){
                document.getElementById('exPrev').disabled=true
                document.getElementById('exNext').disabled=true
                document.getElementById(forForm).submit();
              }
              setTimeout("countdownto()",1000);
              }
            countdownto();
        </script> <?php
        # Original Content
        /*
         * var TimeLimit = new Date('<?php echo $this->_getTimeLimit(); ?>');
            function countdownto() {
              var date = Math.round((TimeLimit-new Date())/1000);
              var hours = Math.floor(date/3600);
              date = date - (hours*3600);
              var mins = Math.floor(date/60);
              date = date - (mins*60);
              var secs = date;
              if (hours<10) hours = '0'+hours;
              if (mins<10) mins = '0'+mins;
              if (secs<10) secs = '0'+secs;
              document.getElementById('exTimer').innerHTML = hours+':'+mins+':'+secs;
              if(hours=='00' && mins == '00' && secs == '00'){
                //alert('fuck');
              }
              setTimeout("countdownto()",1000);
              }
            countdownto();
         */
    }
    private function _getTimeLimit(){
        global $wpdb,$post;
        $timer = time() + intval(get_post_meta($post->ID,'_eme_estimated_time',true)*60);
        return date('r', $timer);
    }
}
