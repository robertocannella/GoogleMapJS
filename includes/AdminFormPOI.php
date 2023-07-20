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

        // PHP READEr
//        if ($_SERVER["REQUEST_METHOD"] == "POST") {
//            if (isset($_FILES["csv_file"])) {
//                $file = $_FILES["csv_file"]["tmp_name"];
//
//                if (($handle = fopen($file, "r")) !== false) {
//                    // Read the first row as the header
//                    $header = fgetcsv($handle, 1000, ",");
//                    // Define the column names you are interested in
//                    $desiredColumns = array("column_name_1", "column_name_2", "column_name_3");
//
//                    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
//                        // Extract only the desired columns based on the header
//                        $rowData = array_intersect_key($data, array_flip($desiredColumns));
//
//                        // Log the extracted row data into the error log
//                        error_log(print_r($rowData, true));
//                    }
//                    fclose($handle);
//                }
//            }
//        }

        ?>
            <!-- This is handled by react -->

        <div id="poi-root">

        </div>
    <?php
    }
}