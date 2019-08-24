module.exports = function( grunt ) {

  'use strict';
  var banner = '/**\n * <%= pkg.homepage %>\n * Copyright (c) <%= grunt.template.today("yyyy") %>\n * This file is generated automatically. Do not edit.\n */\n';
  // Project configuration
  grunt.initConfig( {

    pkg: grunt.file.readJSON( 'package.json' ),

    wp_readme_to_markdown: {
      your_target: {
        files: {
          'README.md': 'readme.txt'
        }
      },
    },
    zip: {
      'location/to/zip/files.zip': ['file/to/zip.js', 'another/file.css']
    },
      copy: {
          main: {
              options: {
                  mode: true
              },
              src: [
                  '**',
                  '*.zip',
                  '!node_modules/**',
                  '!build/**',
                  '!css/sourcemap/**',
                  '!.git/**',
                  '!bin/**',
                  '!.gitlab-ci.yml',
                  '!bin/**',
                  '!tests/**',
                  '!phpunit.xml.dist',
                  '!*.sh',
                  '!*.map',
                  '!Gruntfile.js',
                  '!package.json',
                  '!.gitignore',
                  '!phpunit.xml',
                  '!README.md',
                  '!sass/**',
                  '!codesniffer.ruleset.xml',
                  '!vendor/**',
                  '!composer.json',
                  '!composer.lock',
                  '!package-lock.json',
                  '!phpcs.xml.dist',
              ],
              dest: 'edd-advanced-discounts/'
          }
      },
      compress: {
          main: {
              options: {
                  archive: 'edd-advanced-discounts.zip',
                  mode: 'zip'
              },
              files: [
                  {
                      src: [
                          './edd-advanced-discounts/**'
                      ]

                  }
              ]
          }
      },

      clean: {
          main: ['edd-advanced-discounts'],
          zip: ['edd-advanced-discounts.zip'],
      },
      
      addtextdomain: {
          options: {
              textdomain: 'advanced-discount-edd',
          },
          target: {
              files: {
                  src: ['*.php', '**/*.php', '!node_modules/**', '!php-tests/**', '!bin/**', '!admin/bsf-core/**']
              }
          }
      }

  });

  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-wp-i18n');
  grunt.loadNpmTasks('grunt-zip');
  
  grunt.registerTask('i18n', ['addtextdomain']);
  grunt.registerTask('release', ['clean:zip', 'copy', 'compress', 'clean:main']);


  grunt.loadNpmTasks('grunt-wp-readme-to-markdown');

  grunt.registerTask('readme', ['wp_readme_to_markdown']);

  grunt.util.linefeed = '\n';

};