jQuery(document).ready(function(){
	
	if(jQuery('#toplevel_page_notifysnack').hasClass('current')) {
		jQuery('#toplevel_page_notifysnack img').attr('src', jQuery('#toplevel_page_notifysnack img').attr('src').replace('icon', 'icon_w') );
	}
	
});
