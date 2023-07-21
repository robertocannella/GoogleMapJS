<?php

class AdminFormPOI
{
    public function __construct() {
        add_action( 'admin_menu', [ $this, 'adminPage' ]);

    }
    public function adminPage():void {
        add_submenu_page(
            parent_slug: 'bkj-map-settings-page',
            page_title: esc_html__( 'Points of Interest', 'bkj-map-js' ),
            menu_title: esc_html__( 'POI data', 'bkj-map-js' ),
            capability: 'manage_options',
            menu_slug: 'bkj-map-settings-poi',
            callback: [ $this, 'pointOfInterestHtml' ],
        );
    }
    public function pointOfInterestHtml():void {

        ?>
            <!-- This is handled by react -->

        <div id="poi-root">

        </div>
    <?php
    }
}