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

        $data['title'] = lang('bank') . ' ' . lang('deposit');
        $data['main'] = lang('bank') . ' ' . lang('deposit');

        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_connections', $data);
        $this->load->view('templates/footer');
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('all') . ' ' . lang('bank') . ' ' . lang('deposit');
        $data['main'] = lang('all') . ' ' . lang('bank') . ' ' . lang('deposit');

        $data['connections'] = array(); // $this->M_connections->get_connections(false, $start_date, $to_date,null);
        
        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_all', $data);
        $this->load->view('templates/footer');
    }

    function create_link_token()
    {
        $result = $this->Plaid->create_link_token();
        echo $result;
    }

    function exchange_public_token($public_token)
    {
        // $public_token = $this->input->post('public_token');
        $result = $this->Plaid->public_token_exchange($public_token);
        //echo $result->access_token;
        // echo '</br>ahsan ';
        // echo $result['access_token'];
        // $access_token = ($result ? $result->access_token : '');
        
        // //update refresh token in db
        // $this->M_companies->update_access_token($_SESSION['company_id'],$access_token);
        // ///

        //echo $result;
        
    }
}
