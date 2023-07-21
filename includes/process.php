<?php


include_once 'DataEncryption.php';

class ProcessMap {
    private wpdb $db;
    private string $google_api_key;
    const GOOGLE_API_ENDPOINT = 'https://maps.googleapis.com/maps/api/geocode/json';


    public function __construct(public DataEncryption $data_encryption){
        global $wpdb;
        $this->db = $wpdb;
        $this->google_api_key = $this->getApiKey();
    }
    public function generateMap ():void{
       error_log("Updating map data...!");
       $result =  $this->getPoints();
       if (!$result) { error_log("Something went wrong!");}

    }
    private function getApiKey(): string {
        return $this->data_encryption->decrypt( get_option('bkj_map_google_api_key' ));

    }
    private function getPoints ():bool{
        $points = $this->db->get_results("SELECT * FROM get_all_poi_data");
        if ( $points ) {
            foreach ( $points as $point ) {
                // Process each row of the result


                $encodedAddress = $this->encodeAddress($point);
                $htmlAddress = $this->htmlAddress($point);

                // get coordinates
                $hasGeoCode = $this->verifyGeoCode($point);
                if($hasGeoCode){
                   $coordinates =  $this->getExistingGeoCode($point);
                } else{
                    $isValidAddress = $this->isValidAddress($point);
                    if(!$isValidAddress) { continue; } // exit if no address exists

                    $coordinates = $this->getGeoCodeRequest($encodedAddress);
                }






            }
        } else {
            // Handle the case when there are no results
            return 0;
            error_log('No results returned from the stored procedure.');
        }
        return 1;
    }

    private function encodeAddress ($point): string {

        return urlencode($point->address . ', ' .
            $point->city . ', ' . $point->state . ' ' .
            $point->zip_code);
    }
    private function htmlAddress ($point): string {
        return '<p>' . $point->address . '<br> ' .
            $point->city . ', ' . $point->state . ' ' .
            $point->zip_code . '<br> ' . $point->phone . '</p>';
    }
    private function isValidAddress($point): bool {
        return !(($point->address == ''));

    }
    private function verifyGeoCode($point):bool{
        return !(($point->geo_code == ''));
    }
    private function getExistingGeoCode($point): array
    {
         $coordinates = @$point->geo_code;

                $coordinates = explode(',',$coordinates);
                $data_lat_long['lat'] = $coordinates[0];
                $data_lat_long['lng'] = $coordinates[1];

        return $coordinates;
    }
    private function getGeoCodeRequest($encodeAddress): void
    {
        $url = self::GOOGLE_API_ENDPOINT . "?address=$encodeAddress&key=$this->google_api_key";
        error_log($url);
    }

}

$bkj_map = new ProcessMap(new DataEncryption());
$bkj_map->generateMap();





