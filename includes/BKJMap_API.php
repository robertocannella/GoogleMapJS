<?php
class POI_ITEM {
    public function __construct(
        public $id,
        public $name,
        public $address,
        public $city,
        public $state,
        public $zip_code,
        public $phone,
        public $url,
        public $geo_code,
        public $category_id
    ){}
}
class POI_CATEGORY{
    public function __construct(
        public $id,
        public $name,
        public $hex_color
    ){}
}

class BKJMap_API {
    private $db;
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        add_action('rest_api_init', [$this, 'register_endpoints']);

    }
    public function register_endpoints():void {
        register_rest_route(
            'bkj-map/v1',
            '/poi-data',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_poi_data'],
                    'login_user_id'       =>      get_current_user_id(),
                    'permission_callback' => [$this, 'check_permission']
                ], [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'set_poi_data'],
                    'permission_callback' => [$this, 'check_permission'],
                    //'args'                => $this->get_endpoint_args_for_item_schema( true ),
            ]
            ]
        );
        register_rest_route(
            'bkj-map/v1',
            '/poi-data-new',
            [

                [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'set_poi_data_new'],
                'permission_callback' => [$this, 'check_permission'],
                //'args'                => $this->get_endpoint_args_for_item_schema( true ),
            ]
            ]
        );
        register_rest_route(
            'bkj-map/v1',
            '/poi-categories-new',
            [

                [
                    'methods' => WP_REST_Server::CREATABLE,
                    'callback' => [$this, 'set_poi_categories_new'],
                    'permission_callback' => [$this, 'check_permission'],
                    //'args'                => $this->get_endpoint_args_for_item_schema( true ),
                ]
            ]
        );
        register_rest_route(
            'bkj-map/v1',
            '/poi-categories',
            [
                [
                    'methods' => WP_REST_Server::READABLE,
                    'callback' => [$this, 'get_poi_categories'],
                    'login_user_id'       =>      get_current_user_id(),
                    'permission_callback' => [$this, 'check_permission']
                ], [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'set_poi_categories'],
                'permission_callback' => [$this, 'check_permission'],
                //'args'                => $this->get_endpoint_args_for_item_schema( true ),
            ]
            ]
        );
    }
    public function get_poi_categories(WP_REST_Request $request): WP_Error | WP_REST_Response | WP_HTTP_Response {
        $view_name = 'get_all_poi_categories';
        $results = $this->db->get_results("SELECT * FROM $view_name");
        $data = [];

        if ($results) {
            // Loop through the results and access the data

            foreach ($results as $result) {
                $poi_item = new POI_CATEGORY(
                    id: $result->id,
                    name: $result->name,
                    hex_color: $result->hex_color
                );

                $data [] = $poi_item;
            }
        } else {
            // Query failed, handle the error
            error_log("Error retrieving categories from the view.");
        }
        return rest_ensure_response(array(
            'poiCategories' => $data,
            'success' => true,
        ));

    }
    public function get_poi_data(WP_REST_Request $request): WP_Error|WP_REST_Response|WP_HTTP_Response {
        global $wpdb; // Add this line to make $wpdb accessible

        $view_name = 'get_all_poi_data';

        // Retrieve the results using $wpdb->get_results
        $results = $wpdb->get_results("SELECT * FROM $view_name");

        $data = [];
        // Check if the query was successful
        if ($results) {
            // Loop through the results and access the data

            foreach ($results as $result) {
                $poi_item = new POI_ITEM(
                    id: $result->entry_id,
                    name: $result->name,
                    address:  $result->address,
                    city: $result->city,
                    state:  $result->state,
                    zip_code: $result->zip_code,
                    phone:  $result->phone,
                    url:   $result->url,
                    geo_code:   $result->geo_code,
                    category_id:  $result->category_id
                );

                $data [] = $poi_item;

            }
        } else {
            // Query failed, handle the error
            error_log("Error retrieving data from the view.");
        }


        return rest_ensure_response(array(
            'success' => true,
            'message' => 'data',
            'data' => [
                'user' => get_current_user_id(),
                'data' => "Request Finished",
                'poiData' => $data
            ]
        ));

    }
    public function check_permission(): bool {


        // Implement your authentication and permission checks here
        if (is_user_logged_in() && current_user_can('edit_posts')) {

            // User is logged in and has the required capability, so they have permission
            return true;
        }

        // If the above condition fails, the user does not have permission
        return false;
    }

    public function set_poi_data(WP_REST_Request $request): WP_Error|WP_REST_Response|WP_HTTP_Response{
        $data = json_decode($request->get_body(), true);
        // Perform data validation if needed
        // ...
        $poi_item = new POI_ITEM(
            id: $data['id'],
            name: $data['name'],
            address:  $data['address'],
            city:  $data['city'],
            state:  $data['state'],
            zip_code:  $data['zip_code'],
            phone:  $data['phone'],
            url:  $data['url'],
            geo_code:  $data['geo_code'],
            category_id:  $data['category_id']
        );
        // Update the data in the database
        // ...

        // Assuming you have an update function in your database layer
        // Replace 'update_poi_data_in_database' with your actual update function
        $result = $this->update_poi_data_in_database($poi_item);

        if ($result) {
            // Data updated successfully
            return new WP_REST_Response(['message' => "Data updated successfully"]);
        } else {
            // Failed to update data
            return new WP_Error(
                'update_failed',
                'Failed to update Point of Interest data.',
                ['status' => 500]
            );
        }

    }
    public function set_poi_data_new(WP_REST_Request $request): WP_Error|WP_REST_Response|WP_HTTP_Response{
        $data = json_decode($request->get_body(), true);
        // Perform data validation if needed
        // ...
        $error = false;
        // DROP ALL ROWS


        $count_id = 1;
        foreach ($data as $item) {
            $poi_item = new POI_ITEM(
                id: $count_id++,
                name: $item['name'],
                address:  $item['address'],
                city:  $item['city'],
                state:  $item['state'],
                zip_code:  $item['zip_code'],
                phone:  $item['phone'],
                url:  $item['url'],
                geo_code:  $item['geo_code'],
                category_id:  $item['category_id']
            );


            $result = $this->add_poi_data_in_database($poi_item);

            if (!$result){
                $error = true;
            }

        }

        if (!$error) {
            // Data updated successfully
            return new WP_REST_Response(['message' => "Data updated successfully"]);
        } else {
            // Failed to update data
            return new WP_Error(
                'update_failed',
                'Failed to update Point of Interest data.',
                ['status' => 500]
            );
        }


    }
    public function update_poi_data_in_database($poi): bool
    {
        global $wpdb;


        // Prepare the SQL query with placeholders for the parameters
        $sql = $wpdb->prepare(
            "CALL UpdatePoiData(%d, %s, %s, %s, %s, %s, %s, %s, %d, %s, @result)",
            $poi->id, $poi->name, $poi->address, $poi->city, $poi->state, $poi->zip_code, $poi->phone, $poi->url, $poi->category_id, $poi->geo_code
        );


        // Execute the stored procedure
        // Return true if the update was successful, false otherwise
        $query_result = $wpdb->query($sql);
        $result = $wpdb->get_var("SELECT @result");

        return !empty($result);
    }
    public function add_poi_data_in_database($poiItem): bool
    {

        $name = $poiItem->name;
        $address = $poiItem->address;
        $city = $poiItem->city;
        $state = $poiItem->state;
        $zip_code = $poiItem->zip_code;
        $phone = $poiItem->phone;
        $url = $poiItem->url;
        $category_id = $poiItem->category_id;
        $geo_code = $poiItem->geo_code;


        // Call the stored procedure using a prepared statement
        $sql = $this->db->prepare("
        CALL InsertPoiData(
            %s, %s, %s, %s, %s, %s, %s, %d, %s, @result
           )
        ", $name, $address, $city, $state, $zip_code, $phone, $url, $category_id, $geo_code);

        $this->db->query($sql);

        // Get the result from the output parameter
        $result = $this->db->get_var("SELECT @result");

        return !empty($result);
    }


    public function set_poi_categories_new (WP_REST_Request $request): WP_Error|WP_REST_Response|WP_HTTP_Response {

        // DROP ALL TABLES .  NEW CATEGORY IDs (Foreign Key Constraints )
        // Replace 'table_name' with the actual table name, including the table prefix
        $data_table_name = $this->db->prefix . 'bkj_map_poi_data';
        $category_table_name = $this->db->prefix .'bkj_map_poi_categories';


        // Prepare the SQL query to delete all rows from the table
        $data_sql = "DELETE FROM $data_table_name";
        $category_sql = "DELETE FROM $category_table_name";

        // Execute the query to delete all rows
        $this->db->query($data_sql);
        $this->db->query($category_sql);

        $data = json_decode($request->get_body(), true);

        // Perform data validation if needed
        // ...
        $error = false;
        foreach ($data as $datum) {
            $poi_category = new POI_CATEGORY(
                id: $datum['id'],
                name: $datum['name'],
                hex_color: $datum['hex_color']
            );
            $result = $this->add_poi_category_in_database($poi_category);

            if (!$result){
                $error = true;
            }

        }

        if (!$error) {
            // Data updated successfully
            return new WP_REST_Response(['message' => "Data updated successfully"]);
        } else {
            // Failed to update data
            return new WP_Error(
                'update_failed',
                'Failed to update Point of Interest data.',
                ['status' => 500]
            );
        }

        // Update the data in the database

    }
    public function add_poi_category_in_database($poi_category) : bool{
        $id = $poi_category->id;
        $name = $poi_category->name;
        $hex_color = $poi_category->hex_color;

        // Call the stored procedure using a prepared statement
        $sql = $this->db->prepare("
        CALL InsertPoiCategory(
            %d, %s, %s, @result
           )
        ", $id, $name, $hex_color);

        $this->db->query($sql);

        // Get the result from the output parameter
        $result = $this->db->get_var("SELECT @result");

        return !empty($result);
    }
}