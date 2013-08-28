<?php

/*
---

Highrise-PHP:
A PHP toolkit for accessing Highrise

version: 0.1.0

copyrights:
  - [Ryan Mitchell](@ryanhmitchell)

license:
  - [MIT License]

---
*/

class Highrise {
        
    // credentials
    private $token;
    
    // base url for api calls
    private $apiUrl = 'https://{sub}.highrisehq.com/';
    
    // debug mode
    private $debug = false;
    
    // default constructor
    function __construct($subdomain, $token){
    
    	// subdomain
		$subdomain = array_shift(explode('.', $subdomain));
    	$this->apiUrl = str_replace('{sub}', $subdomain, $this->apiUrl);
        
        // token
        $this->token = $token;

    }
    
    public function get($endpoint, $data=array()){
        return $this->call($endpoint, 'get', $data);
    }
    
    public function post($endpoint, $data){
        return $this->call($endpoint, 'post', $data);
    }
    
    public function patch($endpoint, $data){
        return $this->call($endpoint, 'patch', $data);
    }
    
    public function delete($endpoint, $data){
        return $this->call($endpoint, 'delete', $data);
    }    
    
    /**************************************************************************
    * Private functions
    **************************************************************************/
    
    private function call($endpoint, $type, $data=array()){

        $ch = curl_init();
        
        // Setup curl options
        $curl_options = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'Highrise-PHP',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER	   => array(
            	'Accept: application/xml', 
            	'Content-Type: application/xml'
            ),
            CURLOPT_USERPWD        => $this->token.':x'
        );
        
        // Set curl url to call
        $curlURL = $this->apiUrl.$endpoint.'.xml?';
                                                        
        // type of request determines our headers
        switch($type){
        
            case 'post':
                $curl_options = $curl_options + array(
					CURLOPT_POST        => 1,
					CURLOPT_POSTFIELDS  => $data
                );
            break;
                
            case 'put':
                $curl_options = $curl_options + array(
					CURLOPT_CUSTOMREQUEST => 'PUT',
					CURLOPT_POSTFIELDS  => $data
                );
            break;
                         
            case 'delete':
                $curl_options = $curl_options + array(
                	CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_POSTFIELDS  => $data
                );
            break;
                                                
            case 'get':
            	$curlURL .= '&'.http_build_query($data);
                $curl_options = $curl_options + array(
                );
            break;
            
        }
        
        // add url
        $curl_options = $curl_options + array(
			CURLOPT_URL => $curlURL
        );
                                        
        // Set curl options
        curl_setopt_array($ch, $curl_options);
        
        // Send the request
        $this->result = curl_exec($ch);
        
        // curl info
        $this->info = curl_getinfo($ch);
        
        if ($this->debug){
            var_dump($this->result);
            var_dump($this->info);
        }
        
        // Close the connection
        curl_close($ch);
        
        // load string to xml, convert to json and back again to give us std. objects
        return json_decode(json_encode(simplexml_load_string($this->result)));
    }
            
}

?>