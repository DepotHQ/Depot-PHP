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
    private $oauthAuthoriseURL = 'https://{sub}.depothq.com/oauth/authorize';
    private $oauthAccessTokenURL = 'https://{sub}.depothq.com/oauth/token';
    private $accessToken;
    
    // base url for api calls
    private $apiUrl = 'https://{sub}.depothq.com/api/v1/';
    
    // debug mode
    private $debug = false;
    
    // what format do you want responses in
    private $format = 'json';
    
    // oauth or header mode
    private $mode = 'header';
    
    // default constructor
    function __construct($subdomain, $clientId, $clientSecret, $mode = '', $format = ''){
    
    	$subdomain = array_shift(explode('.', $subdomain));
    
    	$this->oauthAuthoriseURL = str_replace('{sub}', $subdomain, $this->oauthAuthoriseURL);
    	$this->oauthAccessTokenURL = str_replace('{sub}', $subdomain, $this->oauthAccessTokenURL);
    	$this->apiUrl = str_replace('{sub}', $subdomain, $this->apiUrl);
        
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

        // Save accessToken
        $this->accessToken = $result->access_token;
    
        // Return the response as an array
        return $result;
    }
    
    public function setAccessToken($accessToken){
        $this->accessToken = $accessToken;
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
    
    public function upload($endpoint, $path){
        return $this->call($endpoint.'/upload', 'post', array('_tbxFile1' => '@'.$path), false);
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
            CURLOPT_USERAGENT      => 'Depot-PHP',
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false
        );
        
        // Set curl url to call
        if ($type == 'oauth'){
            $curlURL = $this->oauthAccessTokenURL;
        } else {
            $curlURL = $this->apiUrl.$endpoint.'.'.$this->format.'?';
            if ($this->mode == 'oauth'){
            	//$curlURL .= '&oauth_token='.$this->accessToken;
	            $curl_options += array(
	            	CURLOPT_HTTPHEADER => array(
	            		'Authorization: Bearer '.$this->accessToken
	            	)
	            );            	
            } else {
	            $curl_options += array(
	            	CURLOPT_HTTPHEADER => array(
	            		'X-DEPOT-TOKEN: '.$this->clientId,
	            		'X-DEPOT-SECRET: '.$this->clientSecret
	            	)
	            );
            }
        }
                                                        
        // type of request determines our headers
        switch($type){
        
            case 'post':
                $curl_options = $curl_options + array(
					CURLOPT_POST        => 1,
					CURLOPT_POSTFIELDS  => $data
                );
            break;
                
            case 'patch':
                $curl_options = $curl_options + array(
					CURLOPT_CUSTOMREQUEST => 'PATCH',
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
                
            case 'oauth':
                $curl_options = $curl_options + array(
                    CURLOPT_POST       => 1,
                    CURLOPT_POSTFIELDS => $data
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
        
        return ($type == 'oauth' || $this->format == 'json') ? json_decode($this->result) : $this->result;
    }
            
}

?>