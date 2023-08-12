<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_connections extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
        $this->load->model('plaid/Plaid');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('bank');
        $data['main'] = lang('bank');

        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_connections', $data);
        $this->load->view('templates/footer');
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('all') . ' ' . lang('banks');
        $data['main'] = lang('all') . ' ' . lang('banks');

        $data['connections'] = array(); // $this->M_connections->get_connections(false, $start_date, $to_date,null);
        
        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_all', $data);
        $this->load->view('templates/footer');
    }

    public function get_transactions()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('all') . ' ' . lang('transaction');
        $data['main'] = lang('all') . ' ' . lang('transaction');

        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_transaction_lists', $data);
        $this->load->view('templates/footer');
    }

    function create_link_token()
    {
        $result = $this->Plaid->create_link_token();
        echo $result;
    }

    function exchange_public_token()
    {
        $public_token = $this->input->post('public_token');
        $result = $this->Plaid->public_token_exchange($public_token);
        
        echo $result;
        
    }

    function store_access_token()
    {
        $access_token = $this->input->post('access_token');
        $item_id = $this->input->post('item_id');
        //update access_token in db
        $this->M_companies->update_access_token($_SESSION['company_id'],$access_token,$item_id);
        // ///
    }
     
    function get_accounts()
    {
        $result = $this->Plaid->get_accounts();
        echo $result;
    }
     
    function get_transaction_lists_api()
    {
        $result = $this->Plaid->get_transaction_lists();
        echo $result;
    }
}
