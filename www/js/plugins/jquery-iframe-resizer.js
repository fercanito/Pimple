/**
 * @author Stephane Roucheray
 * @extends jquery
 *
 * Changed quite a lot sinze Stephane made something fairly odd... /Henrik Hofmeister
 */

jQuery.fn.iframeResize = function(options){
	var settings = jQuery.extend({
		width: "fill",
		height: "auto",
		autoUpdate : true
	}, options);
	var filler = 30;

	this.each(function() {
        var frame = $(this);
        var body = frame.contents().find("body");
        frame.css('overflow','hidden');

        var resize = function() {
            frame.css("width",  settings.width  == "fill" ? "100%" : parseInt(settings.height));
            var autoheight = 0;
            autoheight = body.height() + filler;
            frame.css("height", settings.height == "auto" ? autoheight : parseInt(settings.height));
           
        };
        frame.bind("load",resize);

		if (settings.autoUpdate) {
			if ($.browser.msie) {
				frame.attr("scrolling", "auto");
				setInterval(resize, 1000);
			}
			else {
				body.bind("DOMSubtreeModified",resize);
			}
		}
        resize();
    });
};
