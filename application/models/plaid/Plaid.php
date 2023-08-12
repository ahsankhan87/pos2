<?php

class Plaid extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('plaid/Http_verbs');
        $this->load->model('plaid/Uri_path');
        // $this->load->model('Plaid/Schema');

        //var_dump($this->Http_verbs->get('ponto-connect'));
        //var_dump($this->Schema->api_schema('ponto-connect'));
        // $ponto_connect_schema = $this->Schema->get_ponto_connect();

    }


    /*
 * Authorization Resources
 * Token
 * */
    function create_link_token()
    {
        $url = getenv('PLAID_HOST');
        $api = "/link/token/create"; //"/einvoicing";
        $urn = ''; //"/oauth2/token";
        $uri = $url . $api . $urn;

        $headers = [
            //"Accept: application/vnd.api+json",
            // "Authorization: Basic " . base64_encode($ponto_client_id . ":" . $ponto_client_secret),
            "Content-Type: application/json"
        ];

        $post_fields = [
            "client_id" => getenv('PLAID_CLIENT_ID'),
            "secret" => getenv('PLAID_SECRET'),
            "client_name" => "Plaid Test App",
            "user" => ["client_user_id" => "user-id"],
            "products" => ["identity"],
            "country_codes" => ["US"],
            "language" => "en",
            "webhook" => "https://webhook.example.com",
            "redirect_uri" => getenv('PLAID_REDIRECT_URI'),
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    //Get access token with exchange of public token
    function public_token_exchange($public_token)
    {
        $url = getenv('PLAID_HOST');
        $api = "/item/public_token/exchange"; 
        $urn = ''; //"/oauth2/token";
        $uri = $url . $api . $urn;

        $headers = [
            //"Accept: application/vnd.api+json",
            // "Authorization: Basic " . base64_encode($ponto_client_id . ":" . $ponto_client_secret),
            "Content-Type: application/json"
        ];

        $post_fields = [
            "client_id" => getenv('PLAID_CLIENT_ID'),
            "secret" => getenv('PLAID_SECRET'),
            "public_token"=> $public_token
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    function get_accounts()
    {
        $url = getenv('PLAID_HOST');
        $api = "/accounts/get"; 
        $urn = ''; //"/oauth2/token";
        $uri = $url . $api . $urn;

        $access_token = $this->M_companies->get_access_token($_SESSION['company_id']);
        
        $headers = [
            //"Accept: application/vnd.api+json",
            // "Authorization: Basic " . base64_encode($ponto_client_id . ":" . $ponto_client_secret),
            "Content-Type: application/json"
        ];

        $post_fields = [
            "client_id" => getenv('PLAID_CLIENT_ID'),
            "secret" => getenv('PLAID_SECRET'),
            "access_token"=> $access_token
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    
    function get_transaction_lists()
    {
        $url = getenv('PLAID_HOST');
        $api = "/transactions/get"; 
        $urn = ''; //"/oauth2/token";
        $uri = $url . $api . $urn;
        
        $access_token = $this->M_companies->get_access_token($_SESSION['company_id']);
        $start_date = date("Y-m-d",strtotime("-1 month"));
        $end_date = date("Y-m-d");

        $headers = [
            //"Accept: application/vnd.api+json",
            // "Authorization: Basic " . base64_encode($ponto_client_id . ":" . $ponto_client_secret),
            "Content-Type: application/json"
        ];

        $post_fields = [
            "client_id" => getenv('PLAID_CLIENT_ID'),
            "secret" => getenv('PLAID_SECRET'),
            "access_token" => $access_token,
            "start_date"=> $start_date,
            "end_date" => $end_date,
            "options"=> [
                "count" => 250,
                "offset" => 0,
                "include_personal_finance_category" => true
            ]
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }
}
