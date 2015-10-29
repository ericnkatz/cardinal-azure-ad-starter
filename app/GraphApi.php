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

        $keys = $this->loadKeysFromAzure();

        return JWT::decode($token->id_token, $keys, ['HS512']);
    }

    /**
     * http://stackoverflow.com/questions/32143743/verifying-jwt-from-azure-active-directory
     */
    public function loadKeysFromAzure() {

        $keys = [];

        // file_get_contents: https://login.windows.net/common/discovery/keys
        $microsoftPublicKeys = '{"keys":[{"kty":"RSA","use":"sig","kid":"kriMPdmBvx68skT8-mPAB3BseeA","x5t":"kriMPdmBvx68skT8-mPAB3BseeA","n":"kSCWg6q9iYxvJE2NIhSyOiKvqoWCO2GFipgH0sTSAs5FalHQosk9ZNTztX0ywS_AHsBeQPqYygfYVJL6_EgzVuwRk5txr9e3n1uml94fLyq_AXbwo9yAduf4dCHTP8CWR1dnDR-Qnz_4PYlWVEuuHHONOw_blbfdMjhY-C_BYM2E3pRxbohBb3x__CfueV7ddz2LYiH3wjz0QS_7kjPiNCsXcNyKQEOTkbHFi3mu0u13SQwNddhcynd_GTgWN8A-6SN1r4hzpjFKFLbZnBt77ACSiYx-IHK4Mp-NaVEi5wQtSsjQtI--XsokxRDqYLwus1I1SihgbV_STTg5enufuw","e":"AQAB","x5c":["MIIDPjCCAiqgAwIBAgIQsRiM0jheFZhKk49YD0SK1TAJBgUrDgMCHQUAMC0xKzApBgNVBAMTImFjY291bnRzLmFjY2Vzc2NvbnRyb2wud2luZG93cy5uZXQwHhcNMTQwMTAxMDcwMDAwWhcNMTYwMTAxMDcwMDAwWjAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkSCWg6q9iYxvJE2NIhSyOiKvqoWCO2GFipgH0sTSAs5FalHQosk9ZNTztX0ywS/AHsBeQPqYygfYVJL6/EgzVuwRk5txr9e3n1uml94fLyq/AXbwo9yAduf4dCHTP8CWR1dnDR+Qnz/4PYlWVEuuHHONOw/blbfdMjhY+C/BYM2E3pRxbohBb3x//CfueV7ddz2LYiH3wjz0QS/7kjPiNCsXcNyKQEOTkbHFi3mu0u13SQwNddhcynd/GTgWN8A+6SN1r4hzpjFKFLbZnBt77ACSiYx+IHK4Mp+NaVEi5wQtSsjQtI++XsokxRDqYLwus1I1SihgbV/STTg5enufuwIDAQABo2IwYDBeBgNVHQEEVzBVgBDLebM6bK3BjWGqIBrBNFeNoS8wLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldIIQsRiM0jheFZhKk49YD0SK1TAJBgUrDgMCHQUAA4IBAQCJ4JApryF77EKC4zF5bUaBLQHQ1PNtA1uMDbdNVGKCmSf8M65b8h0NwlIjGGGy/unK8P6jWFdm5IlZ0YPTOgzcRZguXDPj7ajyvlVEQ2K2ICvTYiRQqrOhEhZMSSZsTKXFVwNfW6ADDkN3bvVOVbtpty+nBY5UqnI7xbcoHLZ4wYD251uj5+lo13YLnsVrmQ16NCBYq2nQFNPuNJw6t3XUbwBHXpF46aLT1/eGf/7Xx6iy8yPJX4DyrpFTutDz882RWofGEO5t4Cw+zZg70dJ/hH/ODYRMorfXEW+8uKmXMKmX2wyxMKvfiPbTy5LmAU8Jvjs2tLg4rOBcXWLAIarZ"]},{"kty":"RSA","use":"sig","kid":"MnC_VZcATfM5pOYiJHMba9goEKY","x5t":"MnC_VZcATfM5pOYiJHMba9goEKY","n":"vIqz-4-ER_vNWLON9yv8hIYV737JQ6rCl6XfzOC628seYUPf0TaGk91CFxefhzh23V9Tkq-RtwN1Vs_z57hO82kkzL-cQHZX3bMJD-GEGOKXCEXURN7VMyZWMAuzQoW9vFb1k3cR1RW_EW_P-C8bb2dCGXhBYqPfHyimvz2WarXhntPSbM5XyS5v5yCw5T_Vuwqqsio3V8wooWGMpp61y12NhN8bNVDQAkDPNu2DT9DXB1g0CeFINp_KAS_qQ2Kq6TSvRHJqxRR68RezYtje9KAqwqx4jxlmVAQy0T3-T-IAbsk1wRtWDndhO6s1Os-dck5TzyZ_dNOhfXgelixLUQ","e":"AQAB","x5c":["MIIC4jCCAcqgAwIBAgIQQNXrmzhLN4VGlUXDYCRT3zANBgkqhkiG9w0BAQsFADAtMSswKQYDVQQDEyJhY2NvdW50cy5hY2Nlc3Njb250cm9sLndpbmRvd3MubmV0MB4XDTE0MTAyODAwMDAwMFoXDTE2MTAyNzAwMDAwMFowLTErMCkGA1UEAxMiYWNjb3VudHMuYWNjZXNzY29udHJvbC53aW5kb3dzLm5ldDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALyKs/uPhEf7zVizjfcr/ISGFe9+yUOqwpel38zgutvLHmFD39E2hpPdQhcXn4c4dt1fU5KvkbcDdVbP8+e4TvNpJMy/nEB2V92zCQ/hhBjilwhF1ETe1TMmVjALs0KFvbxW9ZN3EdUVvxFvz/gvG29nQhl4QWKj3x8opr89lmq14Z7T0mzOV8kub+cgsOU/1bsKqrIqN1fMKKFhjKaetctdjYTfGzVQ0AJAzzbtg0/Q1wdYNAnhSDafygEv6kNiquk0r0RyasUUevEXs2LY3vSgKsKseI8ZZlQEMtE9/k/iAG7JNcEbVg53YTurNTrPnXJOU88mf3TToX14HpYsS1ECAwEAATANBgkqhkiG9w0BAQsFAAOCAQEAfolx45w0i8CdAUjjeAaYdhG9+NDHxop0UvNOqlGqYJexqPLuvX8iyUaYxNGzZxFgGI3GpKfmQP2JQWQ1E5JtY/n8iNLOKRMwqkuxSCKJxZJq4Sl/m/Yv7TS1P5LNgAj8QLCypxsWrTAmq2HSpkeSk4JBtsYxX6uhbGM/K1sEktKybVTHu22/7TmRqWTmOUy9wQvMjJb2IXdMGLG3hVntN/WWcs5w8vbt1i8Kk6o19W2MjZ95JaECKjBDYRlhG1KmSBtrsKsCBQoBzwH/rXfksTO9JoUYLXiW0IppB7DhNH4PJ5hZI91R8rR0H3/bKkLSuDaKLWSqMhozdhXsIIKvJQ=="]}]}';
        $microsoft = json_decode($microsoftPublicKeys, true);

        foreach($microsoft['keys'] as $key) {
            $certificate = "-----BEGIN CERTIFICATE-----\r\n".chunk_split($key['x5c'][0],64)."-----END CERTIFICATE-----\r\n";
            $keys[$key['kid']] = $this->getPublicKeyFromX5C($certificate);
        }

        return $keys;
    }

    /**
     * http://stackoverflow.com/questions/32143743/verifying-jwt-from-azure-active-directory
     */
    public function getPublicKeyFromX5C($string_certText) {
        $object_cert = openssl_x509_read($string_certText);
        $object_pubkey = openssl_pkey_get_public($object_cert);
        $array_publicKey = openssl_pkey_get_details($object_pubkey);

        return $array_publicKey['key'];
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
