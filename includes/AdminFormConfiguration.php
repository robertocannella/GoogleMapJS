<?php

use JetBrains\PhpStorm\NoReturn;

class AdminFormConfiguration
{
    public function __construct(public DataEncryption $data_encryption )
    {
        add_action( 'admin_menu', [ $this, 'adminPage' ] );
        add_action( 'admin_init', [ $this, 'settings' ] );

        add_filter( 'pre_update_option_bkj_map_google_api_key', [ $this, 'encryptData' ] );

        add_action( 'admin_post_run_custom_script', [ $this, 'handle_custom_script' ] );

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

        // Google Zoom
        add_settings_field(
            id: 'bkj_map_snazzy_map', // slug-name to identify the field
            title: 'Snazzy JSON',      // Show as the label for the field during input
            callback: [
                $this,
                'snazzyMapHtml'
            ],   // Function that fills the field with desired inputs. Should echo its output
            page: 'bkj-map-settings-page', // slug-name of the section of the settings page in which to show the box
            section: 'bkj_map_first_section' ); // a reference to the section to attach to


        register_setting(
            option_group: 'bkjmapplugin',               // Option Group
            option_name: 'bkj_map_snazzy_map',      // Option name in the database
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
        <label for="bkj_map_google_api_key" class="flex">Encrypted in the database.</label>
        <input type="password" class="key-field" name="bkj_map_google_api_key" id="bkj_map_google_api_key"
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


            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                <?php
                // Add the WordPress nonce field for security
                wp_nonce_field( 'custom_action', 'custom_action_nonce' );
                ?>
                <input type="hidden" name="action" value="run_custom_script">
                <button type="submit" class="button button-primary"><?php esc_html_e( 'Generate Map', 'bkj-map-js' ); ?></button>
            </form>

        </div>
        <?php
    }

    function snazzyMapHtml(): void {
        $code = get_option( 'bkj_map_snazzy_map' );
        ?>
        <textarea name="bkj_map_snazzy_map" id="bkj_map_snazzy_map" cols="100" rows="20"><?php echo esc_attr( $code ) ?></textarea>


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

    // Callback function to handle the custom script
    #[NoReturn] public function handle_custom_script(): void
    {
        // Verify the nonce for security
        if ( ! isset( $_POST['custom_action_nonce'] ) || ! wp_verify_nonce( $_POST['custom_action_nonce'], 'custom_action' ) ) {
            wp_die( 'Invalid nonce.' );
        }

        require_once plugin_dir_path( __FILE__ ) . 'process.php';


        // Redirect back to the admin page after processing
        wp_safe_redirect( admin_url( 'admin.php?page=bkj-map-settings-page' ) );
        exit;
    }
}