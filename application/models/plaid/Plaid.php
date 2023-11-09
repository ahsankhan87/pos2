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
            "public_token" => $public_token
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    function get_institutions()
    {
        $url = getenv('PLAID_HOST');
        $api = "/institutions/get";
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
            "count" =>  500,
            "offset" =>  0,
            "country_codes" =>  ["US"]
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
            "access_token" => $access_token
        ];


        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    function get_account_balance($account_id)
    {
        $url = getenv('PLAID_HOST');
        $api = "/accounts/balance/get";
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
            "access_token" => $access_token,
            "options" => [
                "account_ids" => $account_id
            ]
        ];


        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    function transaction_sync()
    {
        $url = getenv('PLAID_HOST');
        $api = "/transactions/sync";
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
            "access_token" => $access_token,
            "count" => 50,

        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }
    function get_transactions($start_date, $end_date)
    {
        $url = getenv('PLAID_HOST');
        $api = "/transactions/get";
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
            "access_token" => $access_token,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "options" => [
                "count" => 250,
                "offset" => 0,
                "include_personal_finance_category" => true
            ]
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    function transaction_refresh()
    {
        $url = getenv('PLAID_HOST');
        $api = "/transactions/refresh";
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
            "access_token" => $access_token,

        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }

    function is_transaction_exist($trans_id)
    {
        $this->db->where(array('plaid_trans_id' => $trans_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('acc_entry_items');

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    function is_account_exist($account_id)
    {
        $this->db->where(array('account_id' => $account_id, 'company_id' => $_SESSION['company_id']));
        $query = $this->db->get('pos_plaid_banks');

        if ($query->num_rows() > 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function insert_bank_details($data)
    {
        $this->db->insert('pos_plaid_banks', $data);
        return $this->db->insert_id();
    }

    public function get_user_banks($user_id = false)
    {
        if ($user_id != false) {
            $this->db->where('user_id', $user_id);
        }
        $this->db->where('company_id', $_SESSION['company_id']);
        return $this->db->get('pos_plaid_banks')->result();
    }
}
