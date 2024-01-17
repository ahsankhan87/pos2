<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_banking extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }

    function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('listof') . ' ' . lang('banks');
        $data['main'] = lang('listof') . ' ' . lang('banks');

        //$data['cities'] = $this->M_city->get_city();
        $data['banking'] = $this->M_banking->get_activeBanking();

        $this->load->view('templates/header', $data);
        $this->load->view('banking/banks/v_banking', $data);
        $this->load->view('templates/footer');
    }

    function get_banks_JSON($acc_code)
    {
        print_r(json_encode($this->M_banking->get_activeBankByAccCode($acc_code)));
    }

    function get_active_banks_JSON($id = false)
    {
        print_r(json_encode($this->M_banking->get_activeBanking($id)));
    }

    function bankDetail($bank_id)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'Bank Detail';
        $data['main'] = 'Bank Detail';

        $data['bank'] = $this->M_banking->get_banking($bank_id);
        $data['bank_entries'] = $this->M_banking->get_bank_Entries($bank_id, FY_START_DATE, FY_END_DATE);

        $this->load->view('templates/header', $data);
        $this->load->view('banking/banks/v_bankDetail', $data);
        $this->load->view('templates/footer');
    }
    function create()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            //form Validation
            // $this->form_validation->set_rules('cash_acc_code', 'Cash Account Code', 'required');
            $this->form_validation->set_rules('bank_acc_code', 'Bank Account Code', 'required');
            $this->form_validation->set_rules('bank_name', 'Bank Name', 'required');
            //$this->form_validation->set_rules('acc_holder_name', 'Acc Holder Name', 'required');
            //$this->form_validation->set_rules('bank_account_no', 'bank_account_no', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><a class="close" data-dismiss="alert">�</a><strong>', '</strong></div>');

            $this->db->trans_start();

            //after form Validation run
            if ($this->form_validation->run()) {
                $data = array(
                    'company_id' => $_SESSION['company_id'],
                    'bank_acc_code' => $this->input->post('bank_acc_code', true),
                    // 'cash_acc_code' => $this->input->post('cash_acc_code', true),
                    'bank_account_no' => $this->input->post('bank_account_no', true),
                    'bank_name' => $this->input->post('bank_name', true),
                    // 'acc_holder_name' => $this->input->post('acc_holder_name', true),
                    // 'bank_branch' => $this->input->post('bank_branch', true),
                    'status' => 1,
                    // 'op_balance_dr' => $this->input->post('op_balance_dr', true),
                    // 'op_balance_cr' => $this->input->post('op_balance_cr', true),
                    // "exchange_rate" => $this->input->post('exchange_rate', true),
                    // 'currency_id' => $this->input->post('currency_id', true),

                );

                if ($this->db->insert('pos_banking', $data)) {

                    //$bank_id = $this->db->insert_id();
                    $this->session->set_flashdata('message', 'Bank Created');
                } else {
                    $data['flash_message'] = false;
                }

                $this->db->trans_complete();

                redirect('banking/C_banking/index', 'refresh');
            }
        }
        $data['title'] = 'Add New Bank';
        $data['main'] = 'Add New Bank';

        //$data['salesPostingTypeDDL'] = $this->M_postingTypes->get_SalesPostingTypesDDL();
        $data['accountDDL'] = $this->M_groups->getGrpDetailDropDown($_SESSION['company_id'], $data['langs']); //search for legder account
        //$data['currencyDropDown'] = $this->M_currencies->getcurrencyDropDown();


        $this->load->view('templates/header', $data);
        $this->load->view('banking/banks/create', $data);
        $this->load->view('templates/footer');
    }
    //edit category
    public function edit($id = NULL)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            //form Validation
            // $this->form_validation->set_rules('cash_acc_code', 'cash_acc_code', 'required');
            $this->form_validation->set_rules('bank_acc_code', 'bank_acc_code', 'required');
            $this->form_validation->set_rules('bank_name', 'bank_name', 'required');
            //$this->form_validation->set_rules('acc_holder_name', 'acc_holder_name', 'required');
            //$this->form_validation->set_rules('bank_account_no', 'bank_account_no', 'required');
            $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><a class="close" data-dismiss="alert">�</a><strong>', '</strong></div>');

            $this->db->trans_start();

            //after form Validation run
            if ($this->form_validation->run()) {
                $data = array(
                    'company_id' => $_SESSION['company_id'],
                    'bank_acc_code' => $this->input->post('bank_acc_code', true),
                    // 'cash_acc_code' => $this->input->post('cash_acc_code', true),
                    'bank_account_no' => $this->input->post('bank_account_no', true),
                    'bank_name' => $this->input->post('bank_name', true),
                    // 'acc_holder_name' => $this->input->post('acc_holder_name', true),
                    // 'bank_branch' => $this->input->post('bank_branch', true),
                    // 'op_balance_dr' => $this->input->post('op_balance_dr', true),
                    // 'op_balance_cr' => $this->input->post('op_balance_cr', true),
                    // "exchange_rate" => $this->input->post('exchange_rate', true),
                    // 'currency_id' => $this->input->post('currency_id', true),

                );
                //$this->db->update('pos_banking', $data, array('id'=>$_POST['id']));

                if ($this->db->update('pos_banking', $data, array('id' => $_POST['id']))) {

                    $this->session->set_flashdata('message', 'bank Updated');
                } else {
                    $data['flash_message'] = false;
                }
                $this->db->trans_complete();
                //$this->M_banking->updatebank();
                //$this->session->set_flashdata('message','bank Updated');
                redirect('banking/C_banking/index', 'refresh');
            }
        }

        $data['title'] = 'Update Bank';
        $data['main'] = 'Update Bank';


        $data['bank'] = $this->M_banking->get_banking($id);
        //$data['salesPostingTypeDDL'] = $this->M_postingTypes->get_SalesPostingTypesDDL();
        $data['accountDDL'] = $this->M_groups->getGrpDetailDropDown($_SESSION['company_id'], $data['langs']); //search for legder account
        //$data['currencyDropDown'] = $this->M_currencies->getcurrencyDropDown();


        $this->load->view('templates/header', $data);
        $this->load->view('banking/banks/edit', $data);
        $this->load->view('templates/footer');
    }

    function bank_transactions($bank_id, $bank_acc_code = '')
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'Bank Detail';
        $data['main'] = 'Bank Detail';
        $data['bank_id'] = $bank_id;
        $data['bank_acc_code'] = $bank_acc_code;

        // $data['bank'] = $this->M_banking->get_banking($bank_id);
        // $data['bank_entries']= $this->M_banking->get_bank_Entries($bank_id,FY_START_DATE,FY_END_DATE);

        $this->load->view('templates/header', $data);
        $this->load->view('banking/banks/v_bankTransactions', $data);
        $this->load->view('templates/footer');
    }
    function delete($id)
    {
        $this->db->trans_start();

        $this->M_banking->deletebank($id);

        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'bank Deleted');
        redirect('banking/C_banking/index', 'refresh');
    }

    function inactivate($id, $op_balance_dr, $op_balance_cr, $bank_acc_code) // it will inactive the page
    {
        $this->db->trans_start();
        $this->M_banking->inactivate($id, $op_balance_dr, $op_balance_cr, $bank_acc_code);
        $this->db->trans_complete();

        $this->session->set_flashdata('message', 'Bank in-activated');
        redirect('banking/C_banking/index', 'refresh');
    }

    function activate($id) // it will active 
    {
        $this->M_banking->activate($id);
        $this->session->set_flashdata('message', 'bank Activated');
        redirect('banking/C_banking/index', 'refresh');
    }

    public function import_bank_transactions()
    {
        $config = array();
        $config['upload_path'] = './images/bank_transactions';
        $config['allowed_types'] = 'xlsx|xls|csv';

        $this->upload->initialize($config);
        // var_dump($_FILES);

        if (!$this->upload->do_upload('import_file')) {

            //$this->session->set_flashdata('error',$this->upload->display_errors().' error');
            $data = array('error' => $this->upload->display_errors());
            // $this->load->view('passengers/v_import', $data);
            echo $data['error'];
            //redirect('Passengers/passengerImport','refresh');
        } else {
            $upload_data = $this->upload->data();
            @chmod($upload_data['full_path'], 0777);

            $this->load->library('Excel');
            $this->load->library('IOFactory');
            $objPHPExcel = IOFactory::load($upload_data['full_path']);

            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            $worksheet = $objPHPExcel->getSheet(0);
            $lastRow = $worksheet->getHighestRow();

            $uploads = false;
            $data_excel = array();
            for ($row = 2; $row <= $lastRow; $row++) {

                //REPLACE DATE VALUE FROM EXCEL AND
                //CONVERT INTO MYSQL DATE FORMAT
                $date = str_replace("/", "-", $worksheet->getCell('A' . $row)->getValue());
                $trans_date = date("Y-m-d", strtotime($date));

                $data = array(

                    // $data_excel[$row - 1]['user_id'] = $this->session->userdata('user_id'),
                    $data_excel[$row - 1]['date'] = $trans_date,
                    $data_excel[$row - 1]["description"] = trim($worksheet->getCell('B' . $row)->getValue()),
                    $data_excel[$row - 1]['amount'] = trim($worksheet->getCell('C' . $row)->getValue()),
                    $data_excel[$row - 1]['active'] = '1',
                );

                $uploads = true;
            }
            @unlink($upload_data['full_path']); //DELETE FILE
            echo json_encode($data_excel);
        }
    }


    public function save_bank_transaction()
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
            $bank_id = $this->input->post("bank_id");
            $company_id = $_SESSION['company_id'];
            $sale_date = $this->input->post("date");
            $emp_id = ''; //$this->input->post("emp_id");
            $narration = $this->input->post("description");
            $deposit_to_acc_code = $this->input->post("account_id");
            $acc_code_2 = $this->input->post("account_id_2");
            $total_amount = $this->input->post("amount");

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
            $entry_id = $this->db->insert('acc_entry_items', $data);

            $this->M_banking->addBankPaymentEntry(
                $deposit_to_acc_code,
                $acc_code_2,
                $total_amount,
                0,
                $bank_id,
                $narration,
                $new_invoice_no,
                $sale_date,
                $entry_id
            );
            //for logging
            $msg = 'invoice no ' . $new_invoice_no;
            $this->M_logs->add_log($msg, "Accept bank transaction", "created", "Banking");
            // end logging

            $this->db->trans_complete();
            echo '1';
        } //check product count

    }
}
