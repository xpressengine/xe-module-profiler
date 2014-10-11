(function($){
	/* DOM Ready */
	$(function(){
		var $btnSlowlogTruncate = $('#action-profiler-truncate-slowlog');
		if($btnSlowlogTruncate.length) {
			$btnSlowlogTruncate.on('click', function() {
				exec_json('profiler.procProfilerAdminTruncateSlowlog', {}, function(result) {
					if(result.message) alert(result.message);
					reloadDocument();
				});
			});
		}
	});
}) (jQuery);

