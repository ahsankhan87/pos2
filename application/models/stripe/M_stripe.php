<?php

class M_stripe extends CI_Model
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

    function save_stripe_setting()
    {
        $data = array(
            'stripe_key' => $this->input->post('stripe_key'),
            'stripe_secret_key' => $this->input->post('stripe_secret_key'),
        );

        return $query =  $this->db->update('companies', $data, array('id' => $_SESSION['company_id']));

    }

    function save_stripe_account_id($stripe_acct_id)
    {
        $data = array(
            'stripe_acct_id' => $stripe_acct_id,
        );

        return $query =  $this->db->update('companies', $data, array('id' => $_SESSION['company_id']));

    }

    public function get_stripe_acct_id()
    {
        $this->db->where('id',$_SESSION['company_id']);
        $query = $this->db->get('companies');
        
        if($row = $query->row())
        {
            return $row->stripe_acct_id;
        }
        
        return '';

    }

    function create_login_link($account_id)
    {
        $url = getenv('STRIPE_HOST');
        $api = "/v1/accounts/"; //"/einvoicing";
        $urn = $account_id."/login_links";
        $uri = $url . $api . $urn;

        $headers = [
            //"Accept: application/vnd.api+json",
            // "Authorization: Basic " . base64_encode($ponto_client_id . ":" . $ponto_client_secret),
            "Content-Type: application/json"
        ];

        $post_fields = [
            "YOUR_SECRET_KEY" => getenv('STRIPE_SECRET_KEY'),
            
        ];

        return $this->Http_verbs->post($uri, $headers, $post_fields);
    }
}
