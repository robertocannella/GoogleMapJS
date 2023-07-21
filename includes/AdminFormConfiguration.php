<?php

class AdminFormConfiguration
{
    public function __construct(public DataEncryption $data_encryption )
    {
        add_action( 'admin_menu', [ $this, 'adminPage' ] );
        add_action( 'admin_init', [ $this, 'settings' ] );

        add_filter( 'pre_update_option_bkj_map_google_api_key', [ $this, 'encryptData' ] );

    }
    function settings(): void {

        /******************************* SECTION ONE *********/
        // Create Section
        add_settings_section(
            id: 'bkj_map_first_section',  // slug-name ot identify the section
            title: 'Google Configuration', // Formatted Title of the section, shown as the heading for the section
            callback: [
                $this,
                'serverToServerHtml'
            ], // A function that echos out any content at the top of the section (between heading and fields)
            page: 'bkj-map-settings-page', // the slug-name of the settings page
            args: [
                'before_section' => '<hr>', // HTML content to prepend to the section's HTML output.
                'after_section'  => '',     // HTML content to append to the section's HTML output.
                'section_class'  => 'class_name' // The class name for the section.
            ]
        );

        // Register FIELDS HERE
        add_settings_field(
            id: 'bkj_map_google_api_key', // slug-name to identify the field
            title: 'Google API Key',      // Show as the label for the field during input
            callback: [
                $this,
                'googleApiHtml'
            ],   // Function that fills the field with desired inputs. Should echo its output
            page: 'bkj-map-settings-page', // slug-name of the section of the settings page in which to show the box
            section: 'bkj_map_first_section' ); // a reference to the section to attach to


        register_setting(
            option_group: 'bkjmapplugin',               // Option Group
            option_name: 'bkj_map_google_api_key',      // Option name in the database
            args: [
                'sanitize_callback' => null,            // Sanitize Callback
                'default'           => ''               // Default value
            ] );

        // Google Zoom
        add_settings_field(
            id: 'bkj_map_google_zoom', // slug-name to identify the field
            title: 'Map Zoom (0-30)',      // Show as the label for the field during input
            callback: [
                $this,
                'googleZoomHtml'
            ],   // Function that fills the field with desired inputs. Should echo its output
            page: 'bkj-map-settings-page', // slug-name of the section of the settings page in which to show the box
            section: 'bkj_map_first_section' ); // a reference to the section to attach to


        register_setting(
            option_group: 'bkjmapplugin',               // Option Group
            option_name: 'bkj_map_google_zoom',      // Option name in the database
            args: [
                'sanitize_callback' => null,            // Sanitize Callback
                'default'           => ''               // Default value
            ] );
        // Register FIELDS HERE
        add_settings_field(
            id: 'bkj_map_color_map', // slug-name to identify the field
            title: 'Color Mapping',      // Show as the label for the field during input
            callback: [
                $this,
                'googleApiHtml'
            ],   // Function that fills the field with desired inputs. Should echo its output
            page: 'bkj-map-settings-page', // slug-name of the section of the settings page in which to show the box
            section: 'bkj_map_first_section' ); // a reference to the section to attach to


        register_setting(
            option_group: 'bkjmapplugin',               // Option Group
            option_name: 'bkj_map_color_map',      // Option name in the database
            args: [
                'sanitize_callback' => null,            // Sanitize Callback
                'default'           => ''               // Default value
            ] );

    }

    function serverToServerHtml(): void { ?>
        <button class="button button-secondary" type="button" id="reveal-secrets-button">Reveal</button>
        <?php
    }
    function adminPage(): void {
        add_menu_page(
            page_title: 'MAP.js Settings',
            menu_title: esc_html__( 'Map.js', 'bkj-map-js' ),
            capability: 'manage_options',
            menu_slug: 'bkj-map-settings-page',
            callback: [ $this, 'bkjMapHtml' ],
            icon_url: 'dashicons-admin-site-alt2'
        );
    }
    function googleZoomHtml(): void {
        $zoom = get_option( 'bkj_map_google_zoom' );
        ?>
        <input type="number" min="0" max="30" name="bkj_map_google_zoom"
               value="<?php echo esc_attr( $zoom ) ?>"/>
        <?php
    }
    function googleApiHtml(): void {
        $encrypted_key = get_option( 'bkj_map_google_api_key' );
        if ( $encrypted_key === '' ) {
            $decrypted_key = 'Not Set';
        } else {
            $decrypted_key = $this->decryptData( get_option( 'bkj_map_google_api_key' ) );
        }

        ?>
        <input type="password" class="key-field" name="bkj_map_google_api_key"
               value="<?php echo esc_attr( $decrypted_key ) ?>"/>

        <?php
    }
    function bkjMapHtml(): void {
        ?>
        <!--        The WordPress docs recommend keeping entire contents inside a div with a class of 'wrap'-->
        <div class="wrap">
            <h1>BKJ Map.js Settings</h1>

            <form action="options.php" method="post">
                <?php
                settings_fields( 'bkjmapplugin' );
                do_settings_sections( 'bkj-map-settings-page' );
                submit_button( __( 'Save Settings', 'bkj-map-js' ) );
                ?>
            </form>



        </div>
        <?php
    }

    /**** ENCRYPTION FUNCTIONS */

    function encryptData( $input ): string {

        $submitted_key = sanitize_text_field( $input );

        return $this->data_encryption->encrypt( $submitted_key );

    }

    function decryptData( $input ): string {

        if ( $input ) {
            return $this->data_encryption->decrypt( $input );
        }

        return 'Not Set';
    }
}