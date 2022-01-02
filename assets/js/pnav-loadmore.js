(function ($) {

	var btn_loadmore = $('.btn-loadmore');
	var canbe_loaded = true,
	    bottom_offset = 2000;
	$(document).ready(function() {
		btn_loadmore.on('click', function() {
			var button = $(this);
			px_load_more(button);
		});
		// $(window).scroll(function(){
		// 	px_scroll_loadmore();
		// });
	});
	function px_load_more(button) {
		var data = {
			'action': 'loadmore',
			'page' : pnav_loadmore_params.current_page,
			'current_url' : pnav_loadmore_params.current_url
		};
		$.ajax({
			url: pnav_loadmore_params.ajaxurl,
			type: 'POST',
			data : data,
			beforeSend : function ( xhr ) {
				button.text('Loading...');
			},
			success: function(res) {
        		//console.log(res);
        		if (res) {
        			button.text( 'Load more' ).parent().prev().before(res);
        			pnav_loadmore_params.current_page++;
        			if ( pnav_loadmore_params.current_page == pnav_loadmore_params.max_page ) 
						button.remove(); 
        		} else {
					button.remove();
				}
			},
			error: function(error) {
				console.log(error);
			}
		})
	}

	function px_scroll_loadmore() {
		var data = {
			'action': 'loadmore',
			'page' : pnav_loadmore_params.current_page,
			'current_url' : pnav_loadmore_params.current_url,
		};
		if( ($(document).scrollTop() > ( $(document).height() - bottom_offset )) && canbe_loaded == true ){
			$.ajax({
				url: pnav_loadmore_params.ajaxurl,
				type: 'POST',
				data : data,
				beforeSend : function ( xhr ) {
					canbe_loaded = false; 
				},
				success: function(res) {
	        		if( data ) {
						$(res).insertBefore( ".pointer-append" );
						canbe_loaded = true;
						pnav_loadmore_params.current_page++;
						if ( pnav_loadmore_params.current_page == pnav_loadmore_params.max_page)
							canbe_loaded = false;
					} else {
						canbe_loaded = false;
					}
				},
				error: function(error) {
					console.log(error);
				}
			})
		}
	}
})(jQuery);