<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_bankDeposit extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = "Bank Deposit";
        $data['main'] = "Bank Deposit";
        
        $this->load->view('templates/header', $data);
        $this->load->view('banking/bank_deposit/v_bank_deposit', $data);
        $this->load->view('templates/footer');
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $start_date = FY_START_DATE;  //date("Y-m-d", strtotime("last month"));
        $to_date = FY_END_DATE; //date("Y-m-d");
        $fiscal_dates = "(From: " . date('d-m-Y', strtotime($start_date)) . " To:" . date('d-m-Y', strtotime($to_date)) . ")";

        $data['title'] = "All Bank Deposit";
        $data['main'] = "All Bank Deposit";

        $data['main_small'] = "";// $fiscal_dates;

        $data['bank_deposit'] = $this->M_bank_deposit->get_bank_deposit(false, $start_date, $to_date,null);

        $this->load->view('templates/header', $data);
        $this->load->view('banking/bank_deposit/v_allbank_deposit', $data);
        $this->load->view('templates/footer');
    }

    public function bank_deposit_transaction()
    {
        $total_amount = 0;
        $discount = 0;
        $unit_price = 0;
        $cost_price = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if (count((array)$this->input->post('account_id')) > 0) {
                $this->db->trans_start();
                //GET PREVIOISE INVOICE NO  
                @$prev_invoice_no = $this->M_bank_deposit->getMAXBankDepositInvoiceNo();
                //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                //$new_invoice_no = 'POS'.date("Ymd").$number;
                $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                $new_invoice_no = 'B' . $number;

                //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
                $user_id = $_SESSION['user_id'];
                $company_id = $_SESSION['company_id'];
                $sale_date = $this->input->post("sale_date");
                $emp_id = ''; //$this->input->post("emp_id");
                $narration = '';//($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'sale'; //$this->input->post("register_mode");
                $deposit_to_acc_code = $this->input->post("deposit_to_acc_code");
                $total_amount = $this->input->post("net_total");

                $data = array(
                    'company_id' => $company_id,
                    'invoice_no' => $new_invoice_no,
                    'deposit_to_acc_code'=>$deposit_to_acc_code,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    'sale_date' => $sale_date,
                    'description' => $narration,
                    'total_amount' => ($register_mode == 'sale' ? $total_amount : -$total_amount), //return will be in minus amount
                    
                );
                $this->db->insert('acc_bank_deposit_header', $data);
                $bank_deposit_id = $this->db->insert_id();
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

                foreach ($this->input->post('account_id') as $key => $value) {
                    
                    if ($value != 0) {
                        $account_code  = htmlspecialchars(trim($value));
                        $customer_id = $this->input->post("customer_id")[$key];
                        $unit_price = $this->input->post('unit_price')[$key];
                        $description = $this->input->post('description')[$key];
                        //$total_amount += (double)($unit_price);
                    
                        $data = array(
                            'bank_deposit_id' => $bank_deposit_id,
                            'invoice_no' => $new_invoice_no,
                            'account_code'=>$account_code,
                            'customer_id'=>$customer_id,
                            'amount' => $unit_price, //if sales return then insert amount in negative
                            'description' => $description,
                            'company_id' => $company_id,
                            'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
                        );

                        $this->db->insert('acc_bank_deposit_detail', $data);

                       
                        $data = array(
                            //'entry_id' => $entry_id,
                            'employee_id' => $emp_id,
                            'user_id' => $user_id,
                            //'entry_no' => $entry_no,
                            //'name' => $name,
                            'account_code' => $account_code, //account_id,
                            'date' => $sale_date,
                            //'amount' => $dr_amount,
                            //'ref_account_id' => $ref_id,
                            'debit' => 0,
                            'credit' => $unit_price,
                            'invoice_no' => $new_invoice_no,
                            'narration' => $description,
                            'company_id' => $company_id,
                        );
                        $this->db->insert('acc_entry_items', $data);

                        //customer entry
                        $this->M_customers->addCustomerPaymentEntry($account_code, $deposit_to_acc_code, 0, $unit_price, $customer_id, $description, $new_invoice_no, $sale_date, 1, 0);

                         //for logging
                         $msg = 'invoice no ' . $new_invoice_no;
                         $this->M_logs->add_log($msg, "Bank Deposit transaction", "created", "trans");
                         // end logging
                         
                    }
                }
                $this->db->trans_complete();
                echo '1';
            } //check product count
            
        }
    }
    public function allbank_deposit()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '10240M');

        $data['title'] = "Bank Deposit";
        $data['main'] = "Bank Deposit";

        //$data['cities'] = $this->M_city->get_city();
        $data['bank_deposit'] = $this->M_bank_deposit->get_allbank_deposit($_SESSION['company_id'], FY_START_DATE, FY_END_DATE);


        $this->load->view('templates/header', $data);
        $this->load->view('banking/bank_deposit/v_bank_deposit', $data);
        $this->load->view('templates/footer');
    }


    public function receipt($invoice_no = NULL)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'Invoice';
        $data['main'] = '';
        $data['invoice_no'] = $invoice_no;

        $data['receipt'] = $this->M_bank_deposit->get_entry_by_invoiceNo($invoice_no, $_SESSION['company_id']);

        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);

        $this->load->view('templates/header', $data);
        $this->load->view('banking/bank_deposit/v_receipt', $data);
        $this->load->view('templates/footer');
    }

    function delete($invoice_no)
    {
        $this->M_bank_deposit->delete($invoice_no);
        
        $this->session->set_flashdata('message', 'Bank Deposit Deleted');
        redirect('banking/C_bankDeposit/all', 'refresh');
    }

}
