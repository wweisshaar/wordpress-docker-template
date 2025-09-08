<?php

define( 'WP_SITEURL', 'http://localhost' );
define( 'WP_HOME', 'http://localhost' );

// Memory limits for better performance
define( 'WP_MEMORY_LIMIT', '500M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );

// File system method for plugin/theme installations
define( 'FS_METHOD', 'direct' );

// Security enhancements
define( 'DISALLOW_FILE_EDIT', true );  // Disable file editing in WordPress admin
define( 'AUTOMATIC_UPDATER_DISABLED', true );  // Disable automatic updates (handle via container updates)

// Cache and optimization
define( 'WP_CACHE', true );

// Debug settings (set to false in production)
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_LOG', false );
define( 'WP_DEBUG_DISPLAY', false );

// Increase revision limits
define( 'WP_POST_REVISIONS', 10 );

// Compression
define( 'COMPRESS_CSS', true );
define( 'COMPRESS_SCRIPTS', true );