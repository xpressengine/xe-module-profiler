(function($) {
	/* DOM READY */
	$(function(){
		var $debugbar = $('#xe-debugbar');

		if(!$debugbar.length) return;

		var $sidePanel = $('.side-panel', $debugbar);
		var $mainPanel = $('.main-panel', $debugbar);
		var $handle = $('.handle', $debugbar);


		$handle.on('click', function() {
			$sidePanel.toggle();
			$mainPanel.toggle();
		});
	});

}) (jQuery);
