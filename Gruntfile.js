module.exports = function(grunt) {
	"use strict";

	grunt.file.defaultEncoding = 'utf8';

	grunt.initConfig({
		phplint: {
			default : {
				options: {
					phpCmd: "php",
				},

				src: [
					"**/*.php",
					"!node_modules/**"
				],
			},
		},
		jshint: {
			files: [
				'Gruntfile.js',
				'tpl/js/*.js',
			],
			options : {
				globalstrict: false,
				undef : false,
				eqeqeq: false,
				browser : true,
				globals: {
					"jQuery" : true,
					"console" : true,
					"window" : true
				},
				ignores : [
					'**/jquery*.js',
					'**/*.min.js',
					'**/*-packed.js',
					'**/*.compressed.js'
				]
			}
		},
		csslint: {
			'tpl/css': {
				options: {
					'import' : 2,
					'adjoining-classes' : false,
					'box-model' : false,
					'duplicate-background-images' : false,
					'ids' : false,
					'important' : false,
					'overqualified-elements' : false,
					'qualified-headings' : false,
					'star-property-hack' : false,
					'underscore-property-hack' : false,
				},
				src: [
					'tpl/css/*.css',
					'!**/*.min.css',
				]
			}
		},
		uglify: {
			'tpl/js': {
				options: {
					sourceMap: true,
				},
				files: {
					'tpl/js/dashboard.admin.min.js': ['tpl/js/dashboard.admin.js'],
				}
			}
		},
		cssmin: {
			'tpl/css': {
				files: {
					'tpl/css/dashboard.admin.min.css': ['tpl/css/dashboard.admin.css'],
					'tpl/css/profiler.admin.min.css': ['tpl/css/profiler.admin.css']
				}
			}
		},
	});

	grunt.loadNpmTasks('grunt-contrib-jshint');
	grunt.loadNpmTasks('grunt-contrib-csslint');
	grunt.loadNpmTasks('grunt-phplint');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-cssmin');

	grunt.registerTask('default', ['lint']);
	grunt.registerTask('lint', ['jshint', 'csslint', 'phplint']);
	grunt.registerTask('minify', ['lint', 'cssmin', 'uglify']);
};
