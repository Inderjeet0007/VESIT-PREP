/* Script for Intro */
function fixIframeSize(){
	var video = jQuery('iframe');
	jQuery.each(video, function(key, val){
		var w = jQuery(this).parent().width();
		var h = w * 0.5625;
		jQuery(this).width(w);
		jQuery(this).height(h);	
	});
}
jQuery(document).ready(function(){
	fixIframeSize();
	jQuery(window).resize(function(){
		fixIframeSize();
	});	
	jQuery('#new-lessons-tab').click(function(){
		setTimeout(function(){
			jQuery('iframe').toggle();
			jQuery('iframe').toggle();
			fixIframeSize();	
		}, 250);
	});
	jQuery('#jam-track-toggle').click(function(){
		jQuery('#jam-track-lightbox').fadeToggle();
	});
	jQuery('.lightbox-close').click(function(){
		jQuery(this).parent().parent().fadeToggle();
	});
});
// scroll the lesson nav to the current lesson
jQuery(document).ready(function() {
    if (jQuery('.active-lesson-button').length) {
        var pos = jQuery('.active-lesson-button:first').position();
      	console.log(pos);
        var nav = jQuery('.lesson-nav');
        nav.animate({
            scrollTop: pos.top,
        }, 1000);
    }
});
// toolbar functionality
jQuery(document).ready(function(){
	var tools = jQuery('.tools-container');
	var toggleButton = jQuery('.tool-toggle');
	toggleButton.click(function(){
		jQuery(this).children('.toolbar-content').toggle('fast');
	});
});