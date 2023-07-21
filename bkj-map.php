<?php

/*
Plugin Name: BKJ Map
Plugin URI: http://bkjproductions.com/wordpress/
Description: Google Map.js integration
Author: Various
Requires: PHP 8.0
Version: 0.0.1
Author URI: http://www.bkjproductions.com/
  Text Domain: bkj-map-js
  Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

include_once 'includes/BKJMap_DB.php';
include_once 'includes/BKJMap_API.php';
include_once 'includes/AdminFormConfiguration.php';
include_once 'includes/DataEncryption.php';
include_once 'includes/AdminFormPOI.php';


class BKJMap {
    private BKJMap_DB   $db;
    private BRJMap_API  $api;

    public function __construct()
    {

        // Initialize singleton objects
        $this->db = new BKJMap_DB();
        //$this->api = new BKJMap_API();

        // enqueue front end scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'bkjScriptLoader' ] );

        // enqueue admin scripts
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdminScripts' ] );

        // On Activate Plugin
        add_action( 'init', [ $this, 'init' ] );

    }
        public function init(): void {
            //Plugin Initialization
            $this->db->create_tables();

        }

    public function bkjScriptLoader(): void {

        wp_enqueue_script( 'bkj-map-index-js', plugins_url( '/build/index.js', __FILE__ ),  null, time() );

        wp_localize_script( 'bkj-map-index-js', 'globalSite', [
            'siteURL' => get_site_url()
        ] );
    }
    public function enqueueAdminScripts( $hook ): void {


        // Enqueue scripts only on your plugin's admin page

        if ( isset( $_GET['page'] ) &&
            ( $_GET['page'] === 'bkj-map-settings-page') ||
            ( $_GET['page'] === 'bkj-map-settings-poi' )
            ) {

            wp_enqueue_script( 'bkj-map-admin-js', plugins_url( '/bkj-map-admin.js', __FILE__ ), null, time() );
            wp_enqueue_style( 'bkj-map-admin-css', plugins_url( '/bkj-map-admin.css', __FILE__ ), null, time() );
            wp_enqueue_script('font-awesome-6', '//kit.fontawesome.com/a681bea563.js',null,6.0);


            // LOAD react page
            wp_enqueue_script( 'react-map-js', plugins_url( '/build/index.js', __FILE__ ), ['wp-element'], time() );
            wp_enqueue_style( 'react-map-css', plugins_url( '/build/index.css', __FILE__ ), null, time() );


            wp_localize_script('bkj-map-admin-js','globalSiteData', [
                'siteUrl' => get_site_url(),
                'nonceX' => wp_create_nonce('wp_rest')
            ]);

        }

    }

}

$bkj_map = new BKJMap();
$bkj_form =new AdminFormConfiguration( new DataEncryption() );
$bkj_poi = new AdminFormPOI();
$bkj_api = new BKJMap_API();