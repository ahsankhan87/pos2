<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once dirname(__FILE__) . '../../libraries/tfpdf/tfpdf.php';
class C_stripePayment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
        require_once(APPPATH . 'libraries/stripe-php-12.5.0/init.php');
        $this->load->model('stripe/M_stripe');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'Stripe Payment Account';
        $data['main'] = 'Stripe Payment Account';

        try {

            //GET ALL THE ACCOUTS FROM STRIPE
            // $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
            // $result = $stripe->accounts->all();
            // $stripe_acct_id = $this->M_stripe->get_stripe_acct_id();

            $result = array();
            $stripe_acct_id = "";

            if ($_SESSION['stripe_secret_key'] != "") {
                $stripe_acct_id = $this->M_stripe->get_stripe_acct_id();

                if ($stripe_acct_id != "") {
                    $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
                    $result =   $stripe->accounts->retrieve(
                        $stripe_acct_id,
                        []
                    );
                }
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any API errors that occur during the retrieval
            echo 'Error: ' . $e->getMessage();
        }

        $data['stripe_acct_id'] = $stripe_acct_id;
        $data['account'] = $result;

        $this->load->view('templates/header', $data);
        $this->load->view('setting/stripe/v_stripe', $data);
        $this->load->view('templates/footer');
    }

    public function list_all_account()
    {
        $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
        $data = $stripe->accounts->all(['limit' => 3]);
        return json_encode($data);
    }

    public function get_account_by_id($account_id)
    {
        $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
        $data =   $stripe->accounts->retrieve(
            $account_id,
            []
        );
        echo json_encode($data);
    }

    public function create_account()
    {
        try {
            // Set your secret key. Remember to switch to your live secret key in production.
            // See your keys here: https://dashboard.stripe.com/apikeys
            $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
            $result =  $stripe->accounts->create(['type' => 'express']);

            //$data = array('success' => true, 'data'=> $stripe);
            $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);

            //update account id in database
            $this->M_stripe->save_stripe_account_id($result->id);
            //

            $account_link = $stripe->accountLinks->create([
                'account' => $result->id,
                'refresh_url' => site_url('setting/C_stripePayment/create_account'),
                'return_url' => site_url('setting/C_stripePayment'),
                'type' => 'account_onboarding',
            ]);


            redirect($account_link->url, 'refresh');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any API errors that occur during the retrieval
            echo 'Error: ' . $e->getMessage();
        }
    }
    public function return($account_id)
    {
        $this->M_stripe->save_stripe_account_id($account_id);
        redirect('setting/C_stripePayment', 'refresh');
    }

    public function update_link()
    {
        try {
            $account_id = $this->input->post('account_id', true);
            //$data = array('success' => true, 'data'=> $stripe);
            $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);

            $account_link = $stripe->accountLinks->create([
                'account' => $account_id,
                'refresh_url' => site_url('setting/C_stripePayment/update_link'),
                'return_url' => site_url('setting/C_stripePayment/'),
                'type' => 'account_onboarding',
            ]);

            redirect($account_link->url, 'refresh');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any API errors that occur during the retrieval
            echo 'Error: ' . $e->getMessage();
        }
    }
    public function delete_account($account_id)
    {
        try {
            $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
            $result = $stripe->accounts->delete(
                $account_id,
                []
            );
            $stripe_acct_id = "";
            $this->M_stripe->save_stripe_account_id($stripe_acct_id);

            // echo $result;
            if ($result->deleted) {
                $this->session->set_flashdata('message', 'Account ID: ' . $result->id . ' has been deleted.');
            } else {
                $this->session->set_flashdata('error', 'Record not deleted.');
            }

            redirect('setting/C_stripePayment/', 'refresh');
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any API errors that occur during the retrieval
            echo 'Error: ' . $e->getMessage();
        }
    }

    function account_link($account_id)
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);

        $data = $stripe->accountLinks->create([
            'account' => $account_id,
            'refresh_url' => 'https://example.com/reauth',
            'return_url' => 'https://example.com/return',
            'type' => 'account_onboarding',
        ]);

        //$data = array('success' => true, 'data'=> $stripe);

        echo $data;
    }

    function save_stripe_setting()
    {
        echo $this->M_stripe->save_stripe_setting();
    }

    
    function create_login_link($account_id)
    {
        try {
            $stripe = new \Stripe\StripeClient($_SESSION['stripe_secret_key']);
            $result = $stripe->accounts->createLoginLink(
                $account_id,
                []
            );

            redirect($result->url, 'refresh');
            //echo $result['url'];
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle any API errors that occur during the retrieval
            echo 'Error: ' . $e->getMessage();
        }
    }


    public function handlePayment()
    {

        \Stripe\Stripe::setApiKey($_SESSION['stripe_secret_key']);

        \Stripe\Charge::create([
            "amount" => 1000,
            "currency" => "usd",
            "source" => $this->input->post('stripeToken'),
            "description" => "Dummy stripe payment."
        ]);

        $this->session->set_flashdata('success', 'Payment has been successful.');

        redirect('setting/C_stripePayment/', 'refresh');
    }
}
