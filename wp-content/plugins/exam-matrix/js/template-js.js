/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/* Registration Ajax */
jQuery.noConflict();
(function ($) {
$('document').ready(function(){
    // registration script
	 $('#register-me').on('click',function(){
		 var action = 'register_action';
		 var username = $("#st-username").val();
		 var mail_id = $("#st-email").val();
		 var firname = $("#st-fname").val();
		 var lasname = $("#st-lname").val();
		 var passwrd = $("#st-psw").val();
                 var addr = $('#st-address').val();
	 
		var ajaxdata = {
			 action: 'register_action',
			 username: username,
			 mail_id: mail_id,
			 firname: firname,
			 lasname: lasname,
			 passwrd: passwrd,
                         address: addr,
		};
	 
		$.post( ajaxurl, ajaxdata, function(res){ // ajaxurl must be defined previously
                        if(res=='goToImageUpload'){
                            $('.user_registration_form').remove();
                            $('.avtar_upload').show();
                        }
			$("#error-message").html(res);
		});
	 });
     // Login script
        $('form#login').on('submit', function(e){
           $('form#login p.status').show().text('Loging In....');
           $.ajax({
               type: 'POST',
               dataType: 'json',
               url: ajaxurl,
               data: { 
                   'action': 'ajaxlogin', //calls wp_ajax_nopriv_ajaxlogin
                   'username': $('form#login #username').val(), 
                   'password': $('form#login #password').val(), 
                   'security': $('form#login #security').val() },
               success: function(data){
                   $('form#login p.status').text(data.message);
                   if (data.loggedin == true){
                       location.reload();
                   }
               }
           });
           e.preventDefault();
       });
       // question slider
       $('#testQuestion').bxSlider({
           mode: 'fade',
           infiniteLoop: false,
           adaptiveHeight: true,
           pager:false,
           auto: false,
           onSliderLoad: function(){
                            $('.bx-prev').attr('id','exPrev');
                            $('.bx-next').attr('id','exNext');
                        },
           onSlideNext: function($slideElement, oldIndex, newIndex){
                            //alert($slideElement.html());
                             saveOption($slideElement.prev());
                         },
            onSlidePrev: function($slideElement, oldIndex, newIndex){
                             saveOption($slideElement.next());
                         }
       });
       function saveOption($slideElement){
           var qid = $slideElement.find('.questionId').val();
           var type = $slideElement.find('.answerType').val();
           var actReg = $("#activeRegID").val();
           if(type == 'multi'){
               var answer = $('input[name=answer-'+qid+']:checked').map(function(){
                    return $(this).val();
                }).get();
           } else if(type == 'single'){
                var answer = $("input:radio[name=answer-"+qid+"]:checked").val();
            }
           if(answer === undefined){
                
            } else {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: { 
                        'action': 'saveoption', //calls wp_ajax_nopriv_saveoption
                        'answer': answer,
                        'regID': actReg,
                        'qid': qid },
                    success: function(data){
                        //alert(data);
                    }
                });
            }
       }
});
})(jQuery)

