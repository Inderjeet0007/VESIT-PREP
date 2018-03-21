/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
jQuery.noConflict();
(function ($) {
    $(document).ready(function(){
       $('.showSubset, .deleteSet, .deleteSubset,.deleteQuestion,.viewResult').click(function(){
           $(this).submit();
       });
       // add q
       $('#superSet').change(function(){
           if($(this).val()=='NOSELECT'){
               return false;
           } else {
            $('#selectedSetId').val($(this).val()); 
            $('#submitSelectedSetId').submit();
           }
       });
       // checkbox
       $('#exMulti').click(function(){
           $('.correctOption input').removeAttr('checked');
           $('.correctOption input').removeAttr('disabled');
       });
       $('.correctOption input').click(function(){
           if(!$('#exMulti').is(':checked')){
            if($(this).is(':checked')){
                for(i=1;i<=4;i++){
                    $('#answer'+i).attr('disabled','disabled');
                }
                $(this).removeAttr('disabled');
            } else {
                for(i=1;i<=4;i++){
                    $('#answer'+i).removeAttr('disabled','disabled');
                }
            }
           }
       });
    });
})(jQuery)
