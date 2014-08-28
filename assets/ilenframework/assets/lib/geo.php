<?php
if ( !class_exists('IF_classGEO') ) {
class IF_classGEO {

    //the geoPlugin server
    static $host = 'http://www.geoplugin.net/php.gp?ip={IP}&base_currency={CURRENCY}';
        
    //the default base currency
    static $currency = 'USD';
    
    //initiate the geoPlugin vars
    static $ip = null;
    static $city = null;
    static $region = null;
    static $areaCode = null;
    static $dmaCode = null;
    static $countryCode = null;
    static $countryName = null;
    static $continentCode = null;
    static $latitude = null;
    static $longitude = null;
    static $currencyCode = null;
    static $currencySymbol = null;
    static $currencyConverter = null;
    
    
    function __construct(){

            self::locate();
  
    }
    
    
    function geoPlugin() {

    }
    
    
    
    
    function locate($ip = null) {
        
        global $_SERVER;
        
        if ( is_null( $ip ) ) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $host = str_replace( '{IP}', $ip, self::$host );
        $host = str_replace( '{CURRENCY}', self::$currency, $host );
        
        $data = array();
        
        $response = self::fetch($host);
        
        $data = unserialize($response);
        
        //set the geoPlugin vars
        self::$ip = $ip;
        self::$city = $data['geoplugin_city'];
        self::$region = $data['geoplugin_region'];
        self::$areaCode = $data['geoplugin_areaCode'];
        self::$dmaCode = $data['geoplugin_dmaCode'];
        self::$countryCode = $data['geoplugin_countryCode'];
        self::$countryName = $data['geoplugin_countryName'];
        self::$continentCode = $data['geoplugin_continentCode'];
        self::$latitude = $data['geoplugin_latitude'];
        self::$longitude = $data['geoplugin_longitude'];
        self::$currencyCode = $data['geoplugin_currencyCode'];
        self::$currencySymbol = $data['geoplugin_currencySymbol'];
        self::$currencyConverter = $data['geoplugin_currencyConverter'];
        
    }
    
    function fetch($host) {

        if ( function_exists('curl_init') ) {
                        
            //use cURL to fetch data
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'geoPlugin PHP Class v1.0');
            $response = curl_exec($ch);
            curl_close ($ch);
            
        } else if ( ini_get('allow_url_fopen') ) {
            
            //fall back to fopen()
            $response = file_get_contents($host, 'r');
            
        } else {

            trigger_error ('geoPlugin class Error: Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
            return;
        
        }
        
        return $response;
    }
    
    function convert($amount, $float=2, $symbol=true) {
        
        //easily convert amounts to geolocated currency.
        if ( !is_numeric(self::currencyConverter) || self::currencyConverter == 0 ) {
            //trigger_error('geoPlugin class Notice: currencyConverter has no value.', E_USER_NOTICE);
            return $amount;
        }
        if ( !is_numeric($amount) ) {
            trigger_error ('geoPlugin class Warning: The amount passed to geoPlugin::convert is not numeric.', E_USER_WARNING);
            return $amount;
        }
        if ( $symbol === true ) {
            return self::$currencySymbol . round( ($amount * self::$currencyConverter), $float );
        } else {
            return round( ($amount * self::$currencyConverter), $float );
        }
    }
    
    function nearby($radius=10, $limit=null) {

        if ( !is_numeric(self::$latitude) || !is_numeric(self::$longitude) ) {
            trigger_error ('geoPlugin class Warning: Incorrect latitude or longitude values.', E_USER_NOTICE);
            return array( array() );
        }
        
        $host = "http://www.geoplugin.net/extras/nearby.gp?lat=" . self::$latitude . "&long=" . self::$longitude . "&radius={$radius}";
        
        if ( is_numeric($limit) )
            $host .= "&limit={$limit}";
            
        return unserialize( self::fetch($host) );

    }
 
 
}
}

global $IF_MyGEO;
@$IF_MyGEO = new IF_classGEO;