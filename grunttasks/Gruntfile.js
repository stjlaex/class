module.exports = function(grunt) {

    // 1. All configuration goes here 
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        githooks: {
            all: {
                options: {
                    hashbang: '#!/bin/bash',
                    dest: '../.git/hooks'
                },
                'post-merge': {
                    command: 'vagrant ssh classis -c' +
                    '"node /Projects/classis/class/grunttasks/pre-commit-hook"',
                }
            }
        },
        concat: {
            dist: {
                src: [
                    '../js/host.js',
                    '../js/jquery.uniform.min.js',
                    '../js/vex.combined.min.js'
                ],
                dest: '../js/apphost.js'
                },
            book_js: {
                options: {
                    seperator: '\n',
                    
                },
                src: ['../js/book.js',
                    '../js/qtip.js',
                    '../lib/jscalendar/calendar.js',
                    '../lib/jscalendar/calendar-setup.js',
                    '../js/jquery.uniform.min.js',
                    '../js/jquery.table_sort.js',
                    '../js/documentdrop.js'
                ],
                dest: '../js/appbook.js'
                },
            hostcss: {
                src: [
                     '../css/selery.css',
                     '../css/uniform.edit.css',
                     '../css/vex.css',
                     '../css/vex-ld-theme.css',
                     '../css/hoststyle.css'
                ],
                dest: '../css/apphost.css'
                },
            logbook_css: {
                src: ['../css/bookstyle.css',
                      '../css/logbook.css',
                      '../css/uniform.edit.css'
                ],
                dest: '../css/applogbook.css'
                },
            logbook_js: {
                options: {
                    seperator: '\n',
                    
                },
                src: ['../js/qtip.js',
                      '../js/book.js'
                ],
                dest: '../js/applogbook.js'
                },
            appbook_css: {
                src: ['../css/bookstyle.css',
                    '../css/selery.css',
                    '../css/seneeds.css',
                     '../css/entrybook.css',
                     '../css/aboutbook.css',
                     '../css/register.css',
                     '../css/infobook.css',
                     '../css/markbook.css',
                     '../css/reportbook.css',
                     '../css/admin.css',
                     '../css/calendar.css',
                     '../css/lms.css',
                    '../css/uniform.edit.css',
                    '../css/modal-contents.css'
                ],
                dest: '../css/appbook.css'
            },
            /*markbook: {
                src: [
                    '../js/book.js',
                    '../js/qtip.js',
                    'jscalendar/lang/calendar-en.js',
                    'jscalendar/calendar-setup.js',
                    '../js/jcrop/jquery.min.js',
                    '../js/documentdrop.js'
                ],
                dest: '../js/markbookbuild.js'
            }*/
        },
        uglify: {
            js: {
                files: {
                    '../js/apphost.min.js': ['../js/apphost.js'],
                    '../js/applogbook.min.js' : ['../js/applogbook.js'],
                    '../js/appbook.min.js' : ['../js/appbook.js']//,
                    //'../js/markbookbuild.min.js': ['../js/markbookbuild.js']
                }
            },
        },
        cssmin: {
            combine: {
                files: {
                    '../css/apphost.min.css' : ['../css/apphost.css'],
                    '../css/applogbook.min.css' : ['../css/applogbook.css'],
                    '../css/appbook.min.css' : ['../css/appbook.css'],
                }
            }
        },
        /*Replace object
        hashres: {
            options: {
                fileNameFormat: '${name}.${hash}.${ext}',
                renameFiles: false
            },
            prod: {
                src: ['../js/apphost.min.js', '../js/appbook.min.js'],
                dest: ['../tempindex.php', '../scripts/end_options']
            }
        }*/
    })

    // 3. Tell Grunt the required plug-ins.
    grunt.loadNpmTasks('grunt-githooks');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-newer');
    //grunt.loadNpmTasks('grunt-hashres');
    // 4. Default is when grunt is called without a task following it.
    grunt.registerTask('default', ['githooks', 'commit']);
    grunt.registerTask('deploy', ['newer:concat', 'newer:cssmin', 'newer:uglify']);
    grunt.registerTask('all', [ 'concat', 'cssmin', 'uglify']);
};
