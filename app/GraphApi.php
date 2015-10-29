<?php

namespace App;

use Cache;
use GuzzleHttp\Client as http;
use \Firebase\JWT\JWT;

class GraphApi
{
    public $client;

    public $secret;

    public $tenant;

    public $resource;

    public $token;

    public $http;


    protected $endpoints = ['users'];


    function __construct(http $http) {

        $this->client = env('AZURE_AD_ID');
        $this->secret = env('AZURE_AD_SECRET'); 
        $this->tenant = env('AZURE_AD_TENANT'); 
        $this->resource = env('AZURE_AD_RESOURCE'); 
        $this->http = $http;

        $this->token = $this->getToken();


    }

    public function authorize($code, $session_state) {

        $token = $this->fetchToken([
            'code' => $code,
            'grant_type' => 'authorization_code'
        ]);

        $jwt = explode('.', $token->id_token);

        $user = json_decode(base64_decode($jwt[1]))->upn;

        return $user;
       
    }

    public function getToken() {
        $token = Cache::remember('token', 59, function() {
            $token = $this->fetchToken();
            return $token->access_token;
        });

        return $token;
    }

    public function fetchToken($params = []) {
        $oauth_request = 'https://login.windows.net/' . $this->tenant . '/oauth2/token';

        try {

            $token_request = $this->http->post($oauth_request, [
                'form_params' => array_merge([
                    'client_id' => $this->client,
                    'client_secret' => $this->secret,
                    'grant_type' => 'client_credentials',
                    'resource' => $this->resource
                ], $params)
            ]);

            $token_response = $token_request->getBody();

            $token = json_decode($token_response);

            return $token;

        } catch (Exception $e) {
            //  print_r($e->getResponse());

        }
    }
    
    public function validateEndpoint($endpoint) {
      
        foreach($this->endpoints as $allowed) {
            if( strpos($endpoint, $allowed) >= 0 ){
                return strpos($endpoint, $allowed);
            }
        }

        return false;
    }

    public function getEndpoint($endpoint, $query = []) {
        
        if( $this->validateEndpoint($endpoint) !== false) {

            try {

                $graph_uri = $this->resource . '/' . $this->tenant . '/';

                $users_request = $this->http->get($graph_uri . $endpoint, [
                    'headers' => ['Authorization' => $this->token],
                    'query' => array_merge(['api-version' => '1.6'], $query)
                ]);
                
                return json_decode( $users_request->getBody() );

            } catch (Exception $e) {
                //  print_r($e->getResponse());
            }

        }

        $invalidObj = (object) null;
        $invalidObj->error = 'Invalid Endpoint';
        return $invalidObj;

    }

    public function getEndpointWithItem($endpoint, $item) {
        return $this->getEndpoint($endpoint . '/' . $item);
    }

    public function getEndpointWithPagination($endpoint, $pagination) {
        
        return $this->getEndpoint($endpoint, [
            '$skiptoken' => "X'$pagination'"
        ]);
    }
}
