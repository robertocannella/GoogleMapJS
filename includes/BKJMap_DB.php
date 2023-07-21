<?php

class BKJMap_DB
{
    private mixed $wpdb;
    private string $prefix;
    private string $charset;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->prefix = 'bkj_map_';
        $this->charset = $wpdb->get_charset_collate();
    }

    public function create_tables(): void
    {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // Table names
        $usersTable = $this->wpdb->prefix . 'users';
        $dataTable = $this->wpdb->prefix . $this->prefix . 'poi_data';
        $mapCategoryTable = $this->wpdb->prefix . $this->prefix . 'poi_categories';
        // Check if tables exist
        $usersTableExists = $this->wpdb->get_var("SHOW TABLES LIKE '$usersTable'") === $usersTable;
        $defaultDataTableExists = $this->wpdb->get_var("SHOW TABLES LIKE '$dataTable'") === $dataTable;
        $mapCategoryTableExists = $this->wpdb->get_var("SHOW TABLES LIKE '$mapCategoryTable'") === $mapCategoryTable;

        // Name	Address	City	State	ZIP	Phone number	URL	Category	GEOCODE
        // Create tables if they don't exist
        if (!$defaultDataTableExists) {
            dbDelta("CREATE TABLE $dataTable (
                id int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                address VARCHAR(255) ,
                city VARCHAR(55),
                state CHAR(2),
                zip_code VARCHAR(10),
                phone VARCHAR(20),
                url VARCHAR(255),
                category_id INT,
                geo_code VARCHAR(255),
                FOREIGN KEY (category_id) REFERENCES $mapCategoryTable(id)
            ) $this->charset;");
        }
        if (!$mapCategoryTableExists) {
            dbDelta("CREATE TABLE $mapCategoryTable (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(55),
                hex_color VARCHAR(55)
        
            ) $this->charset");
        }
        $this->create_UpdatePOIData_procedure();
        $this->create_InsertPOIData_procedure();
        $this->create_InsertPOICategory_procedure();
    }

    public function create_UpdatePOIData_procedure(): void
    {
        $procedureName = 'UpdatePoiData';

        // Check if the procedure exists
        $checkQuery = "SHOW PROCEDURE STATUS WHERE Name = %s";
        $exists = $this->wpdb->get_row($this->wpdb->prepare($checkQuery, $procedureName));

        if (!$exists) {
            $procedureSql = "
        CREATE PROCEDURE UpdatePoiData(
                IN p_id INT,
                IN p_name VARCHAR(255),
                IN p_address VARCHAR(255),
                IN p_city VARCHAR(55),
                IN p_state CHAR(2),
                IN p_zip_code VARCHAR(10),
                IN p_phone VARCHAR(20),
                IN p_url VARCHAR(255),
                IN p_category_id INT,
                IN p_geo_code VARCHAR(255),
                OUT result INT
            )
            BEGIN
                SET result = 1;
                
                UPDATE rgo_bkj_map_poi_data
                SET 
                    name = p_name,
                    address = p_address,
                    city = p_city,
                    state = p_state,
                    zip_code = p_zip_code,
                    phone = p_phone,
                    url = p_url,
                    category_id = p_category_id,
                    geo_code = p_geo_code
                WHERE id = p_id;
            END;
            ";


        // Execute the procedure creation SQL statement
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $this->wpdb->query($procedureSql);
        }
    }

    public function create_InsertPOIData_procedure(): void
    {
        $procedureName = 'InsertPoiData';

        // Check if the procedure exists
        $checkQuery = "SHOW PROCEDURE STATUS WHERE Name = %s";
        $exists = $this->wpdb->get_row($this->wpdb->prepare($checkQuery, $procedureName));

        if(!$exists) {
            $procedureSql = "
                CREATE PROCEDURE InsertPoiData(
                    IN p_name VARCHAR(255),
                    IN p_address VARCHAR(255),
                    IN p_city VARCHAR(55),
                    IN p_state CHAR(2),
                    IN p_zip_code VARCHAR(10),
                    IN p_phone VARCHAR(20),
                    IN p_url VARCHAR(255),
                    IN p_category_id INT,
                    IN p_geo_code VARCHAR(255),
                    OUT result INT
                    )
                    BEGIN
                    SET result = 1;
                     
                    INSERT INTO rgo_bkj_map_poi_data (
                        name, address, city, state, zip_code, phone, url, category_id, geo_code
                    ) VALUES (
                        p_name, p_address, p_city, p_state, p_zip_code, p_phone, p_url, p_category_id, p_geo_code
                    );
                END;
            ";

        // Execute the procedure creation SQL statement
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $this->wpdb->query($procedureSql);
        }
    }
    public function create_InsertPOICategory_procedure(): void
    {
        $procedureName = 'InsertPoiCategory';

        // Check if the procedure exists
        $checkQuery = "SHOW PROCEDURE STATUS WHERE Name = %s";
        $exists = $this->wpdb->get_row($this->wpdb->prepare($checkQuery, $procedureName));

        if(!$exists){
            $procedureSql = "
                     CREATE PROCEDURE InsertPoiCategory(
                        IN p_id INT,
                        IN p_name VARCHAR(255),
                        IN p_hex_color VARCHAR(255),
                        OUT result INT
                        )
                        BEGIN
                        SET result = 1;
                         
                        INSERT INTO rgo_bkj_map_poi_categories (
                            id, name, hex_color 
                        ) VALUES (
                            p_id, p_name, p_hex_color
                        );
                    END;
                ";

            // Execute the procedure creation SQL statement
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            $this->wpdb->query($procedureSql);
        }

    }

}