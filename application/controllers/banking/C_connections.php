<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_connections extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
        $this->load->model('plaid/Plaid');
        $this->load->model('plaid/M_institution');
    }

    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('bank');
        $data['main'] = lang('bank');
        $data['plaidItems'] = $this->M_institution->retrieveItemsByPlaidItemID();


        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_plaid_accounts', $data);
        $this->load->view('templates/footer');
    }

    public function all_plaid_accounts()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('bank');
        $data['main'] = lang('bank');


        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_institutions', $data);
        $this->load->view('templates/footer');
    }

    function get_items_json()
    {
        $result = json_encode($this->M_institution->retrieveItemsByPlaidItemID());
        echo $result;
    }

    function retrieveAccountsByItemID_json($item_id)
    {
        $result = json_encode($this->M_institution->retrieveAccountsByItemID($item_id));
        echo $result;
    }
    function retrieveTransactionsByAccountID($account_id)
    {
        $result = json_encode($this->M_institution->retrieveTransactionsByAccountID($account_id));
        echo $result;
    }
    public function all($item_id)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'All accounts list';
        $data['main'] = 'All accounts list';
        $data['item_id'] = $item_id;


        $this->load->view('templates/header', $data);
        $this->load->view('banking/connections/v_allAccounts', $data);
        $this->load->view('templates/footer');
    }

    public function get_plaid_transactions($account_id, $plaid_item_id)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('all') . ' ' . lang('transaction');
        $data['main'] = lang('all') . ' ' . lang('transaction');
        $data['account_id'] = $account_id;
        $data['plaid_item_id'] = $plaid_item_id;

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
            $narration = 'Payee: ' . ($this->input->post("payee") == '' ? '' : $this->input->post("payee"));
            $deposit_to_acc_code = $this->input->post("account_id");
            $acc_code_2 = $this->input->post("account_id_2");
            $total_amount = $this->input->post("payment_amount");
            $plaid_trans_id = $this->input->post("plaid_trans_id");

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
                'plaid_trans_id' => $plaid_trans_id,
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
                'plaid_trans_id' => $plaid_trans_id,
            );
            $this->db->insert('acc_entry_items', $data);

            $data1 = array(
                'posted' => 1,
            );
            $this->db->update('pos_plaid_transactions', $data1, array('plaid_transaction_id' =>  $plaid_trans_id, 'company_id' => $_SESSION['company_id']));

            //for logging
            $msg = 'invoice no ' . $new_invoice_no;
            $this->M_logs->add_log($msg, "Accept bank transaction", "created", "Banking");
            // end logging

            $this->db->trans_complete();
            echo '1';
        } //check product count

    }

    ////////////////////////
    //API CALL FUNCTION
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

        //insert plaid items, accounts and trasactions data into database
        $this->insert_plaid_items($access_token);
        $this->insert_plaid_accounts($item_id, $access_token);
        $this->insert_plaid_transactions_month($access_token);
    }

    public function insert_plaid_items($plaid_access_token)
    {
        $plaidResponse = json_decode($this->Plaid->get_plaid_items($plaid_access_token), true);

        //when get plaid item detail then get institution name from plaid via institution id
        $institution_response = json_decode($this->Plaid->get_institution_by_id($plaidResponse['item']['institution_id']), true);

        // echo '<pre>';
        // echo var_dump($plaidResponse);
        // echo '</pre>';
        // return;


        if (!$this->M_institution->is_ItemByPlaidInstitutionId($plaidResponse['item']['institution_id'])) {
            $bankData = array(
                'user_id' => $_SESSION['user_id'],
                'company_id' => $_SESSION['company_id'],
                'plaid_access_token' => $plaid_access_token,
                'plaid_institution_id' => $plaidResponse['item']['institution_id'],
                'institution_name' => $institution_response['institution']["name"],
                'plaid_item_id' => $plaidResponse['item']['item_id'],
                // 'status' => $values['status']
            );

            $this->M_institution->insert_plaid_items($bankData);
        }
    }

    public function insert_plaid_accounts($item_id, $plaid_access_token)
    {
        $plaidResponse = json_decode($this->Plaid->get_accounts($plaid_access_token), true);
        // echo '<pre>';
        // echo var_dump($plaidResponse['accounts']);
        // echo '</pre>';
        //return;

        foreach ($plaidResponse['accounts'] as $values) {
            if (!$this->M_institution->is_account_exist($values['account_id'])) {
                $bankData = array(
                    'user_id' => $_SESSION['user_id'],
                    'company_id' => $_SESSION['company_id'],
                    'item_id' => $item_id,
                    'plaid_account_id' => $values['account_id'],
                    'name' => $values['name'],
                    'mask' => $values['mask'],
                    'official_name' => $values['official_name'],
                    'current_balance' => $values['balances']['current'],
                    'available_balance' => $values['balances']['available'],
                    'iso_currency_code' => $values["balances"]['iso_currency_code'],
                    'unofficial_currency_code' => $values["balances"]['unofficial_currency_code'],
                    'type' => $values['type'],
                    'subtype' => $values['subtype']
                );

                $this->M_institution->insert_plaid_accounts($bankData);
            }
        }
    }
    public function insert_plaid_transactions_month($plaid_access_token, $month = "-3 month", $refresh = false)
    {
        $start_date = date("Y-m-d", strtotime($month));
        $end_date = date("Y-m-d");

        $plaidResponse = json_decode($this->Plaid->get_transactions($start_date, $end_date, $plaid_access_token), true);

        // echo '<pre>';
        // echo $plaid_access_token;
        // echo var_dump($plaidResponse);
        // echo '</pre>';

        foreach ($plaidResponse['transactions'] as $values) {
            if (!$this->M_institution->is_plaid_transaction_exist($values['transaction_id'])) {
                $bankData = array(
                    // 'user_id' => $_SESSION['user_id'],
                    'company_id' => $_SESSION['company_id'],
                    'plaid_transaction_id' => $values['transaction_id'],
                    'account_id' => $values['account_id'],
                    'plaid_category_id' => $values['category_id'],
                    'category' => json_encode($values['category']),
                    // 'subcategory' => $values['subcategory'],
                    'name' => $values['name'],
                    'amount' => $values['amount'],
                    'iso_currency_code' => $values['iso_currency_code'],
                    'unofficial_currency_code' => $values['unofficial_currency_code'],
                    'type' => $values['transaction_type'],
                    'pending' => $values['pending'],
                    'account_owner' => $values['account_owner'],
                    'date' => $values['date'],

                );

                $this->M_institution->insert_plaid_transactions($bankData);
            }
        }
        echo json_encode($plaidResponse);
    }

    function plaid_transaction_refresh($plaid_item_id)
    {
        $access_token = $this->M_institution->retrieveItemsByPlaidItemID($plaid_item_id)[0]['plaid_access_token'];

        echo  $this->insert_plaid_transactions_month($access_token);
        //redirect('banking/C_connections/get_transaction_sync/' . $plaid_item_id);
    }

    function remove_plaid_item($plaid_item_id)
    {
        $access_token = $this->M_institution->retrieveItemsByPlaidItemID($plaid_item_id)[0]['plaid_access_token'];

        $result = $this->Plaid->get_plaid_item_remove($access_token);
        if (strlen($result) > 0) {
            $this->delete_plaid_item($plaid_item_id);
            redirect('banking/C_connections', 'refresh');
        }
        echo $result;
    }

    function get_institution_by_id($institution_id)
    {
        $result = $this->Plaid->get_institution_by_id($institution_id);
        echo $result;
    }

    function get_plaid_items($token)
    {
        $result = $this->Plaid->get_plaid_items($token);
        echo $result;
    }
    function retrieveAccountsByItemID($item_id)
    {
        //$plaidResponse = $this->Plaid->get_accounts();
        // echo $plaidResponse;

        echo json_encode($this->M_institution->retrieveAccountsByItemID($item_id));
    }

    function get_account_balance($account_id)
    {
        $bankData = array($account_id);
        $plaidResponse = json_decode($this->Plaid->get_account_balance($bankData), true);
        echo $plaidResponse['accounts'][0]['balances']["current"];
    }

    function transaction_sync()
    {
        $result = $this->Plaid->transaction_sync();
        echo $result;
    }

    function get_transactions($plaid_access_token)
    {
        $start_date = date("Y-m-d", strtotime('-3 month'));
        $end_date = date("Y-m-d");

        echo '<pre>';
        echo $this->Plaid->get_transactions($start_date, $end_date, $plaid_access_token);
        echo '</pre>';
    }

    function transaction_refresh()
    {
        $result = $this->Plaid->transaction_refresh();
        echo $result;
    }

    function is_transaction_exist()
    {
        $trans_id = $this->input->post('trans_id');
        $result = $this->Plaid->is_transaction_exist($trans_id);
        // echo '{"exist":"'.$result.'"}';
        echo $result;
    }
    //////////////////////
    function delete_plaid_item($plaid_item_id)
    {
        $this->M_institution->delete_plaid_items($plaid_item_id);
    }
}
