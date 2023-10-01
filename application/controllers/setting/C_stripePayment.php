<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
// require_once dirname(__FILE__) . '../../libraries/tfpdf/tfpdf.php';
class C_stripePayment extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
        require_once(APPPATH . 'libraries/stripe-php-12.5.0/init.php');
        $this->load->model('stripe/Stripe');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'stripePayment';
        $data['main'] = 'stripePayment';

        //GET ALL THE ACCOUTS FROM STRIPE
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        $result = $stripe->accounts->all();

        $data['list_all'] = $result;

        $this->load->view('templates/header', $data);
        $this->load->view('setting/stripe/v_stripe', $data);
        $this->load->view('templates/footer');
    }

    public function list_all_account()
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        $data = $stripe->accounts->all(['limit' => 3]);
        return json_encode($data);
    }

    public function get_account_by_id($account_id)
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        $data =   $stripe->accounts->retrieve(
            $account_id,
            []
        );
        echo json_encode($data);
    }

    public function create_account()
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        $result =  $stripe->accounts->create(['type' => 'express']);
        
        //$data = array('success' => true, 'data'=> $stripe);
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        
        $account_link = $stripe->accountLinks->create([
            'account' => $result->id,
            'refresh_url' => 'https://example.com/reauth',
            'return_url' => site_url('setting/C_stripePayment/'),
            'type' => 'account_onboarding',
        ]);

        redirect($account_link->url, 'refresh');
        
    }

    public function update_link()
    {
        $account_id = $this->input->post('account_id',true);
        //$data = array('success' => true, 'data'=> $stripe);
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        
        $account_link = $stripe->accountLinks->create([
            'account' => $account_id ,
            'refresh_url' => 'https://example.com/reauth',
            'return_url' => site_url('setting/C_stripePayment/'),
            'type' => 'account_onboarding',
        ]);

        redirect($account_link->url, 'refresh');
        
    }
    public function delete_account($account_id)
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));
        $result = $stripe->accounts->delete(
            $account_id,
            []
        );
        
        // echo $result;
        if($result->deleted)
        {
            $this->session->set_flashdata('message', 'Account ID: '.$result->id.' has been deleted.');
        }else{
            $this->session->set_flashdata('error', 'Record not deleted.');
        }
        
        redirect('setting/C_stripePayment/', 'refresh');
    }

    function account_link($account_id = "acct_1NwQY6IETd01p1Sr")
    {
        // Set your secret key. Remember to switch to your live secret key in production.
        // See your keys here: https://dashboard.stripe.com/apikeys
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET_KEY'));

        $data = $stripe->accountLinks->create([
            'account' => $account_id,
            'refresh_url' => 'https://example.com/reauth',
            'return_url' => 'https://example.com/return',
            'type' => 'account_onboarding',
        ]);

        //$data = array('success' => true, 'data'=> $stripe);

        echo $data;
    }

    public function handlePayment()
    {

        \Stripe\Stripe::setApiKey(getenv('STRIPE_SECRET_KEY'));

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
