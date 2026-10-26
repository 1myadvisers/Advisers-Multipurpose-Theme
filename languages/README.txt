Translation files (.pot/.po/.mo) go in this folder.

This theme is translation-ready: all visible strings use WordPress's
standard gettext functions with the text domain "advisers-multipurpose", and
load_theme_textdomain() points here.

If the theme is hosted on WordPress.org, translations are handled
automatically via translate.wordpress.org and no files need to be
placed here manually.

For local development, generate a .pot file with WP-CLI:
wp i18n make-pot . languages/advisers-multipurpose.pot
