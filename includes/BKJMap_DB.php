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

    public function create_tables():void {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        // Table names
        $usersTable = $this->wpdb->prefix . 'users';
        $dataTable = $this->wpdb->prefix . $this->prefix .'poi_data';
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
        if (!$mapCategoryTableExists){
            dbDelta("CREATE TABLE $mapCategoryTable (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(55)
        
            ) $this->charset" ) ;
        }

    }


}