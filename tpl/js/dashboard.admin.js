/* Copyright (C) NAVER <http://www.navercorp.com> */

jQuery(function($) {
	Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function(color) {
		return {
			radialGradient: {
				cx: 0.5,
				cy: 0.3,
				r: 0.7
			},
			stops: [
				[0, color],
				[1, Highcharts.Color(color).brighten(-0.3).get("rgb")]
			]
		};
	});
	$("#slowlog-trigger-graph").highcharts({
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		exporting: {
			buttons: {
				contextButton: {
					enabled: false
				}
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: "pointer",
				dataLabels: {
					enabled: false,
					format: "<strong>{point.name}</strong>: {point.y:.2f}" + xe.lang.unit_sec,
					style: {
						color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || "black"
					}
				},
				showInLegend: false
			}
		},
		series: [{
			type: "pie",
			name: xe.lang.cmd_profiler_runtime,
			data: profiler.tgd
		}],
		title: {
			text: xe.lang.profiler_unit.trigger
		},
		tooltip: {
			pointFormat: "{series.name}: <strong>{point.y:.2f}" + xe.lang.unit_sec + "</strong>"
		}
	});
	$("#slowlog-addon-graph").highcharts({
		chart: {
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		exporting: {
			buttons: {
				contextButton: {
					enabled: false
				}
			}
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: "pointer",
				dataLabels: {
					enabled: false,
				},
				showInLegend: false
			}
		},
		series: [{
			type: "pie",
			name: xe.lang.cmd_profiler_runtime,
			data: profiler.agd
		}],
		title: {
			text: xe.lang.profiler_unit.addon
		},
		tooltip: {
			pointFormat: "{series.name}: <strong>{point.y:.2f}" + xe.lang.unit_sec + "</strong>"
		}
	});

	setTimeout(function() {$(".dashboard-message").fadeOut(1000)}, 2500);
});

/* End of file */
