<?php

/*
---

Depot-PHP:
A PHP toolkit for accessing Depot

version: 0.1.0

copyrights:
  - [Ryan Mitchell](@ryanhmitchell)

license:
  - [MIT License]

---
*/

class Depot {
        
    // credentials
    private $clientId;
    private $clientSecret;
    
    // oauth urls
    private $oauthAuthoriseURL = 'http://tc.depothq.com/oauth/authorize';
    private $oauthAccessTokenURL = 'http://tc.depothq.com/oauth/token';
    private $accessToken;
    
    // base url for api calls
    private $apiUrl = 'http://tc.depothq.com/api/v1';
    
    // debug mode
    private $debug = true;
    
    // what format do you want responses in
    private $format = 'json';
    
    // oauth or header mode
    private $mode = 'header';
    
    // default constructor
    function __construct($clientId, $clientSecret, $mode = '', $format = ''){
        
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        if ($mode != '') $this->mode = $mode;
        if ($format != '') $this->format = $format;

    }
    
    // pass off to oauth authorise url
    public function getAuthoriseURL($callback){
    
    	if ($this->mode != 'oauth') return;
        
        $authoriseURL  = $this->oauthAuthoriseURL;
        $authoriseURL .= '?response_type=code';
        $authoriseURL .= '&client_id='.$this->clientId;
        $authoriseURL .= '&redirect_uri='.urlencode($callback);
        
        return $authoriseURL;
        
    }
    
    // convert an oauth code to an access token
    public function getAccessToken($code, $callback){
    
    	if ($this->mode != 'oauth') return;
        
        // if we already have an access token
        if ($this->accessToken){
            return $this->accessToken;
        }
        
        // params to send to oauth receiver
        $params = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $callback
        );
        
        // call oauth
        $result = $this->call('', 'oauth', $params);
        
        // Get array of accessToken
        $accessToken = json_decode($result, true);
        
        // Save accessToken
        $this->accessToken = $accessToken['access_token'];
    
        // Return the response as an array
        return $accessToken;
    }
    
    public function setAccessToken($accessToken){
        $this->accessToken = $accessToken;
    }
    
    public function get($endpoint, $data=false){
        return $this->call($endpoint, 'get', $data);
    }
    
    public function post($endpoint, $data){
        return $this->call($endpoint, 'post', $data);
    }
    
    public function patch($endpoint, $data){
        return $this->call($endpoint, 'put', $data);
    }
    
    public function delete($endpoint, $data){
        return $this->call($endpoint, 'delete', $data);
    }    
    
    /**************************************************************************
    * Private functions
    **************************************************************************/
    
    private function call($endpoint, $type, $data=false){

        $ch = curl_init();
        
        // Set curl url to call
        if ($type == 'oauth'){
            $curlURL = $this->oauthAccessTokenURL;
        } else {
            $curlURL = $this->apiUrl.$endpoint.'.'.$this->format;
        }
        
        curl_setopt($ch, CURLOPT_URL, $curlURL);
        
        // Setup curl options
        $curl_options = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_USERAGENT      => 'Depot-PHP'
        );
        
        // type of request determines our headers
        switch($type){
        
            case 'post':
                $curl_options = $curl_options + array(
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer '.$this->accessToken,
					),
					CURLOPT_POST        => 1,
					CURLOPT_POSTFIELDS  => $data
                );
            break;
                
            case 'patch':
                $curl_options = $curl_options + array(
					CURLOPT_CUSTOMREQUEST => 'PATCH',
					CURLOPT_HTTPHEADER => array(
						'Authorization: Bearer '.$this->accessToken,
					),
					CURLOPT_POST        => 1,
					CURLOPT_POSTFIELDS  => $data
                );
            break;
                         
            case 'delete':
                $curl_options = $curl_options + array(
                	CURLOPT_CUSTOMREQUEST => 'DELETE',
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$this->accessToken,
                    ),
                    CURLOPT_POST        => 1,
                    CURLOPT_POSTFIELDS  => $data
                );
            break;
                                                
            case 'get':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array(
                        'Authorization: Bearer '.$this->accessToken
                    )
                );
            break;
                
            case 'oauth':
                $curl_options = $curl_options + array(
                    CURLOPT_HTTPHEADER => array('Accept: application/json'),
                    CURLOPT_POST       => 1,
                    CURLOPT_POSTFIELDS => $data
                );
            break;
            
        }
        
        // Set curl options
        curl_setopt_array($ch, $curl_options);
        
        // Send the request
        $result = curl_exec($ch);
        $error = curl_errno($ch);
        
        if($this->debug){
            var_dump($result);
            var_dump($error);
        }
        
        // Close the connection
        curl_close($ch);
        
        return json_decode($result, true);
    }
            
}

?>