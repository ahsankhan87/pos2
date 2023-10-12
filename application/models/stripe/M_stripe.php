<?php

class M_stripe extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('plaid/Http_verbs');
        $this->load->model('plaid/Uri_path');
        require_once(APPPATH . 'libraries/stripe-php-12.5.0/init.php');
        
    }
    function create_payment_link($account_id,$product_name,$unit_price,$quantity,$application_fee,$currency="usd")
    {
        try {
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys
            $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

            $result = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => $currency,
                            'product_data' => [
                                'name' => $product_name,
                            ],
                            'unit_amount' => $unit_price, // Amount in cents (e.g., 2000 means $20.00)
                        ],
                        'quantity' => $quantity,
                    ],
                ],
                'payment_intent_data' => [
                    'application_fee_amount' => $application_fee,
                    'transfer_data' => ['destination' => $account_id],
                ],
                'success_url' => site_url('Success'),
                'cancel_url' => site_url('Success/cancel'),
            ]);

            // redirect($result->url, 'refresh');
            return $result->url;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any API errors that occur during the retrieval
            return 'Error: ' . $e->getMessage();
        }
    }

    function save_stripe_setting()
    {
        $data = array(
            'stripe_key' => $this->input->post('stripe_key'),
            'stripe_secret_key' => $this->input->post('stripe_secret_key'),
        );

        return $this->db->update('companies', $data, array('id' => $_SESSION['company_id']));

    }

    function save_stripe_account_id($stripe_acct_id)
    {
        $data = array(
            'stripe_acct_id' => $stripe_acct_id,
        );

        return $this->db->update('companies', $data, array('id' => $_SESSION['company_id']));

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

    public function get_stripe_keys()
    {
        $data = array();
        $this->db->where('id',$_SESSION['company_id']);
        $query = $this->db->get('companies');
        
        if($row = $query->row())
        {
            $data['stripe_key'] =  $row->stripe_key;
            $data['stripe_secret_key'] =  $row->stripe_secret_key;
            
        }
        
        return $data;

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
