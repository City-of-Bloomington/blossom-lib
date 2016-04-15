<?php
/**
 * Where on the filesystem this application is installed
 */
define('APPLICATION_HOME', __DIR__.'/testApplication');
define('BLOSSOM', realpath(__DIR__.'/../'));

/**
 * Multi-Site support
 *
 * To allow multiple sites to use this same install base,
 * define the SITE_HOME variable in the Apache config for each
 * site you want to host.
 *
 * SITE_HOME is the directory where all site-specific data and
 * configuration are stored.  For backup purposes, backing up this
 * directory would be sufficient for an easy full restore.
 */
define('SITE_HOME', !empty($_SERVER['SITE_HOME']) ? $_SERVER['SITE_HOME'] : APPLICATION_HOME.'/data');
include SITE_HOME.'/site_config.inc';


#include_once BLOSSOM.'/src/Classes/ActiveRecord.php';
#include_once BLOSSOM.'/src/Classes/View.php';
#include_once BLOSSOM.'/src/Classes/Template.php';
#include_once BLOSSOM.'/src/Classes/Block.php';

/**
 * Minimal PSR-4 Autoloader for Blossom files
 */
spl_autoload_register(function ($class) {
    $prefix = 'Blossom\\';
    $base_dir = BLOSSOM . '/src/';

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});