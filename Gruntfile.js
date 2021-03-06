module.exports = function(grunt) {
  "use strict";
  var banner =
    '/**\n * <%= pkg.homepage %>\n * Copyright (c) <%= grunt.template.today("yyyy") %>\n * This file is generated automatically. Do not edit.\n */\n';

  var pkgInfo = grunt.file.readJSON("package.json");
  // Project configuration
  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),

    addtextdomain: {
      options: {
        textdomain: "edd-advanced-discounts"
      },
      target: {
        files: {
          src: [
            "*.php",
            "**/*.php",
            "!node_modules/**",
            "!php-tests/**",
            "!bin/**"
          ]
        }
      }
    },

    wp_readme_to_markdown: {
      your_target: {
        files: {
          "README.md": "readme.txt"
        }
      }
    },

    makepot: {
      target: {
        options: {
          domainPath: "/languages",
          mainFile: "edd-advanced-discounts.php",
          potFilename: "edd-advanced-discounts.pot",
          potHeaders: {
            poedit: true,
            "x-poedit-keywordslist": true
          },
          type: "wp-plugin",
          updateTimestamp: true
        }
      }
    },

    copy: {
      main: {
        options: {
          mode: true
        },
        src: [
          "**",
          "*.zip",
          "!node_modules/**",
          "!build/**",
          "!css/sourcemap/**",
          "!.git/**",
          "!bin/**",
          "!.gitlab-ci.yml",
          "!bin/**",
          "!tests/**",
          "!phpunit.xml.dist",
          "!*.sh",
          "!*.map",
          "!Gruntfile.js",
          "!package.json",
          "!.gitignore",
          "!phpunit.xml",
          "!README.md",
          "!sass/**",
          "!codesniffer.ruleset.xml",
          "!vendor/**",
          "!admin/bsf-core/vendor/**",
          "!composer.json",
          "!composer.lock",
          "!package-lock.json",
          "!phpcs.xml.dist"
        ],
        dest: "edd-advanced-discounts/"
      }
    },

    compress: {
      main: {
        options: {
          archive: "edd-advanced-discounts-" + pkgInfo.version + ".zip",
          mode: "zip"
        },
        files: [
          {
            src: ["./edd-advanced-discounts/**"]
          }
        ]
      }
    },

    clean: {
      main: ["edd-advanced-discounts"],
      zip: ["*.zip"]
    }
  });

  grunt.loadNpmTasks("grunt-contrib-copy");
  grunt.loadNpmTasks("grunt-contrib-compress");
  grunt.loadNpmTasks("grunt-contrib-clean");

  grunt.loadNpmTasks("grunt-wp-i18n");
  grunt.loadNpmTasks("grunt-wp-readme-to-markdown");
  grunt.registerTask("i18n", ["addtextdomain", "makepot"]);
  grunt.registerTask("readme", ["wp_readme_to_markdown"]);

  // Grunt release - Create installable package of the local files
  grunt.registerTask("release", [
    "clean:zip",
    "copy",
    "compress",
    "clean:main"
  ]);

  grunt.util.linefeed = "\n";
};
