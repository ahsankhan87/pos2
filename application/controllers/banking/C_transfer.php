<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_transfer extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang("transfer");
        $data['main'] = lang("transfer");   

        $this->load->view('templates/header', $data);
        $this->load->view('banking/transfer/v_transfer', $data);
        $this->load->view('templates/footer');
    }


    public function transfer_transaction()
    {
        $amount = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            $this->db->trans_start();
            //GET PREVIOISE INVOICE NO  
            @$prev_invoice_no = $this->M_entries->getMAXEntryInvoiceNo('JV');
            $number = (int) substr($prev_invoice_no, 2) + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
            $new_invoice_no = 'JV' . $number;

            //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
            $user_id = $_SESSION['user_id'];
            $company_id = $_SESSION['company_id'];
            $sale_date = $this->input->post("sale_date");
            $narration = ($this->input->post("description") == '' ? '' : $this->input->post("description"));
            $transfer_from = $this->input->post("transfer_from");
            $transfer_to = $this->input->post("transfer_to");
            $amount = $this->input->post("amount");

            ////////
            $data = array(
                //'entry_id' => $entry_id,
                // 'employee_id' => $emp_id,
                'user_id' => $user_id,
                //'entry_no' => $entry_no,
                //'name' => $name,
                'account_code' => $transfer_to, //account_id,
                'date' => $sale_date,
                //'amount' => $dr_amount,
                //'ref_account_id' => $ref_id,
                'debit' => $amount,
                'credit' => 0,
                'invoice_no' => $new_invoice_no,
                'narration' => $narration,
                'company_id' => $company_id,
            );
            $this->db->insert('acc_entry_items', $data);
            $this->db->insert('acc_transfer', $data);

            ////////
            $data_1 = array(
                //'entry_id' => $entry_id,
                // 'employee_id' => $emp_id,
                'user_id' => $user_id,
                //'entry_no' => $entry_no,
                //'name' => $name,
                'account_code' => $transfer_from, //account_id,
                'date' => $sale_date,
                //'amount' => $dr_amount,
                //'ref_account_id' => $ref_id,
                'debit' => 0,
                'credit' => $amount,
                'invoice_no' => $new_invoice_no,
                'narration' => $narration,
                'company_id' => $company_id,
            );
            $this->db->insert('acc_entry_items', $data_1);
            $this->db->insert('acc_transfer', $data_1);


            //for logging
            $msg = 'invoice no ' . $new_invoice_no;
            $this->M_logs->add_log($msg, "Transfer transaction", "created", "trans");
            // end logging

            $this->db->trans_complete();
            echo '1';
        }
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '10240M');

        $data['title'] = lang('all').' '.lang('transfer');
        $data['main'] = lang('all').' '.lang('transfer');

        //$data['cities'] = $this->M_city->get_city();
        $data['transfer'] = $this->M_transfer->get_alltransfer($_SESSION['company_id'], FY_START_DATE, FY_END_DATE);


        $this->load->view('templates/header', $data);
        $this->load->view('banking/transfer/v_all', $data);
        $this->load->view('templates/footer');
    }

    public function receipt($invoice_no = NULL)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('invoice');
        $data['main'] = '';
        $data['invoice_no'] = $invoice_no;

        $data['receipt'] = $this->M_transfer->get_entry_by_invoiceNo($invoice_no, $_SESSION['company_id']);

        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);

        $this->load->view('templates/header', $data);
        $this->load->view('banking/transfer/v_receipt', $data);
        $this->load->view('templates/footer');
    }

    function delete($invoice_no)
    {
        
        $this->M_transfer->deleteEntry_invoice_no($invoice_no);
        
        
        $this->session->set_flashdata('message', 'Transfer Deleted');
        redirect('banking/C_transfer/all', 'refresh');
    }
}
