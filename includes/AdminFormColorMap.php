<?php

class AdminFormColorMap
{
    private $db;
    public function __construct() {
        global $wpdb;
        $this->db = $wpdb;

        add_action( 'admin_menu', [ $this, 'adminPage' ]);


    }
    public function adminPage():void {
        add_submenu_page(
            parent_slug: 'bkj-map-settings-page',
            page_title: esc_html__( 'Color Mappings', 'bkj-map-js' ),
            menu_title: esc_html__( 'Color Map', 'bkj-map-js' ),
            capability: 'manage_options',
            menu_slug: 'bkj-map-settings-color-map',
            callback: [ $this, 'colorMapHtml' ],
        );
    }
    public function colorMapHtml(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle form submission and update the database
            if (isset($_POST['update_colors'])) {
                $table_name = $this->db->prefix . 'bkj_map_poi_categories';

                foreach ($_POST['id'] as $category_id => $hex_color) {
                    // Sanitize and validate the input if needed.
                    $category_id = (int) $category_id;

                    // Update the database.
                    $table_name = $this->db->prefix . 'bkj_map_poi_categories';
                    $this->db->update($table_name, ['hex_color' => $hex_color], ['id' => $category_id]);
                }
            }
        }

        ?>
        <h2 class="text-2xl">Color Map </h2>

        <form method="post" action="">

            <table>
                <thead>
                <tr>
                    <td class="font-bold">Category</td>
                    <td class="font-bold">Hex Code</td>
                    <td class="font-bold">Current Color</td>
                </tr>
                </thead>
                <tbody>
                <?php
                $table_name = $this->db->prefix . 'bkj_map_poi_categories';
                $results = $this->db->get_results("SELECT * FROM $table_name ");

                if ($results) {
                    foreach ($results as $result) {
                        ?>
                        <tr>
                            <td><?php echo esc_html($result->name); ?></td>
                            <td><input  maxlength="6" type="text" name="id[<?php echo esc_attr($result->id); ?>]" value="<?php echo esc_attr($result->hex_color); ?>" /></td>
                            <td><input type="color" disabled value="#<?php echo esc_attr($result->hex_color); ?>" /></td>
                        </tr>
                        <?php
                    }
                }
                ?>
                </tbody>
            </table>

            <input type="submit" name="update_colors" value="Update Colors" class="button button-primary" />
        </form>
        <?php
    }

}