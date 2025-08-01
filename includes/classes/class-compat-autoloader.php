<?php

namespace Aireset\Default\Compat;

// Exit if accessed directly.
defined('ABSPATH') || exit;

/**
 * Autoloader classes for compatibility with themes and plugins
 * 
 * @since 1.0.0
 * @version 3.7.0
 */
class Autoloader {

    /**
     * Constructor
     * 
     * @since 1.0.0
     * @version 3.7.0
     * @return void
     */
    public function __construct() {
        $this->load_and_run();
    }
    

    /**
     * Load and run all compatibility classes
     * 
     * @since 3.7.0
     * @return void
     */
    public function load_and_run() {
        // iterate for each compat class on directory
        foreach ( glob( AIRESET_DEFAULT_INC_PATH . 'classes/compat/class-compat-*.php' ) as $file ) {
            include_once $file;
        }
    }
}

new Autoloader();