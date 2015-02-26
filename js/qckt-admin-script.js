(function($) {
	$(document).ready(function(){
		//Initilize and Load Datepicker
	    $('.qckt-datepicker').datepicker({
	        dateFormat : 'yy-mm-dd'
	    });

	    //Show Mode Handler
	    if( $('.qckt-utoken').length>0 ){
	    	$('.qckt-widget-options').each(function(){
		    	var showmode = $(this).find('.qckt-showmode select').val();
		    	//Show or hide 'User Token' field
		    	if( showmode == 2 ){
		    		$(this).find('.qckt-utoken').removeClass('hidden');
		    	}
	    	});
	    }

	    $('.qckt-showmode select').on('change', function() {
	    	$(this).closest('.qckt-widget-options').find('.qckt-utoken').toggleClass('hidden');
	    });

	});
})(jQuery);
