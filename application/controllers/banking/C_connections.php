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

    public function get_transactions($account_id)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('all') . ' ' . lang('transaction');
        $data['main'] = lang('all') . ' ' . lang('transaction');
        $data['account_id'] = $account_id;

        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_transaction_lists', $data);
        $this->load->view('templates/footer');
    }

    public function bank_entry_transaction()
    {
        $total_amount = 0;
        
        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $this->db->trans_start();
            
            //GET PREVIOISE INVOICE NO  
            @$prev_invoice_no = $this->M_entries->getMAXEntryInvoiceNo('JV');
            $number = (int) substr($prev_invoice_no, 2) + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
            $new_invoice_no = 'JV' . $number;
            //

            //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
            $user_id = $_SESSION['user_id'];
            $company_id = $_SESSION['company_id'];
            $sale_date = $this->input->post("date");
            $emp_id = ''; //$this->input->post("emp_id");
            $narration = 'Payee: '.($this->input->post("payee") == '' ? '' : $this->input->post("payee"));
            $deposit_to_acc_code = $this->input->post("account_id");
            $acc_code_2 = $this->input->post("account_id_2");
            $total_amount = $this->input->post("payment_amount");

            ////////
            $data = array(
                //'entry_id' => $entry_id,
                'employee_id' => $emp_id,
                'user_id' => $user_id,
                //'entry_no' => $entry_no,
                //'name' => $name,
                'account_code' => $deposit_to_acc_code, //account_id,
                'date' => $sale_date,
                //'amount' => $dr_amount,
                //'ref_account_id' => $ref_id,
                'debit' => $total_amount,
                'credit' => 0,
                'invoice_no' => $new_invoice_no,
                'narration' => $narration,
                'company_id' => $company_id,
            );
            $this->db->insert('acc_entry_items', $data);

            $data = array(
                //'entry_id' => $entry_id,
                'employee_id' => $emp_id,
                'user_id' => $user_id,
                //'entry_no' => $entry_no,
                //'name' => $name,
                'account_code' => $acc_code_2, //account_id,
                'date' => $sale_date,
                //'amount' => $dr_amount,
                //'ref_account_id' => $ref_id,
                'debit' => 0,
                'credit' => $total_amount,
                'invoice_no' => $new_invoice_no,
                'narration' => $narration,
                'company_id' => $company_id,
            );
            $this->db->insert('acc_entry_items', $data);

            //for logging
            $msg = 'invoice no ' . $new_invoice_no;
            $this->M_logs->add_log($msg, "Accept bank transaction", "created", "Banking");
            // end logging

            $this->db->trans_complete();
            echo '1';
        } //check product count

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
        $this->M_companies->update_access_token($_SESSION['company_id'], $access_token, $item_id);
        // ///
    }

    function get_accounts()
    {
        $result = $this->Plaid->get_accounts();
        return $result;
    }

    function get_transaction_lists_api()
    {
        $result = $this->Plaid->get_transaction_lists();
        return $result;
    }
}
