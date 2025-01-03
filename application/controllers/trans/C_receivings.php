<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_receivings extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }

    public function index($purchaseType = '')
    {
        $data = array('langs' => $this->session->userdata('lang'));

        //$this->output->enable_profiler();

        $data['title'] = lang('purchases');
        $data['main'] = lang('purchases');

        $data['purchaseType'] = "cash";
        //when click on sale manu clear the cart first if exist
        //$this->destroyCart();

        //$data['itemDDL'] = $this->M_items->getItemDropDown();
        //$data['emp_DDL'] = $this->M_employees->getEmployeeDropDown();
        //$data['sizesDDL'] = $this->M_sizes->get_activeSizesDDL();
        $data['supplierDDL'] = $this->M_suppliers->getSupplierDropDown(); //search for legder account

        $this->load->view('templates/header', $data);
        $this->load->view('pos/receivings/v_receivings', $data);
        $this->load->view('templates/footer');
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $start_date = FY_START_DATE; //date("Y-m-d", strtotime("last year"));
        $to_date = FY_END_DATE; //date("Y-m-d");
        $fiscal_dates = "(From: " . date('m/d/Y', strtotime($start_date)) . " To:" . date('m/d/Y', strtotime($to_date)) . ")";

        $data['title'] = lang('purchases') . ' ' . $fiscal_dates;
        $data['main'] = lang('purchases');
        $data['main_small'] = $fiscal_dates;
        $data['purchaseType'] = "cash";

        $data['receivings'] = $this->M_receivings->get_receivings(false, $start_date, $to_date, 'cash');

        $this->load->view('templates/header', $data);
        $this->load->view('pos/receivings/v_allPurchases', $data);
        $this->load->view('templates/footer');
    }

    public function upload_purchase_file($invoice_no)
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $config['upload_path']  = './images/purchases/';
            $config['allowed_types'] = 'gif|jpg|png|pdf|doc|docx';
            $this->load->library('upload', $config);
            $this->upload->initialize($config);
            // $this->upload->do_upload('purchase_file');
            // $uploaddata = $this->upload->data();

            if (!$this->upload->do_upload('purchase_file')) {
                echo $this->upload->display_errors();
            } else {
                $upload_data = $this->upload->data();

                $data = array(
                    'file' => $upload_data['file_name'],
                );

                echo $this->db->update('pos_receivings', $data, array('invoice_no' => $invoice_no));
            }
        } else {

            $data = array('langs' => $this->session->userdata('lang'));

            $data['title'] = 'Purchase file';
            $data['main'] = 'Purchase file';
            $data['invoice_no'] = $invoice_no;

            $this->load->view('templates/header', $data);
            $this->load->view('pos/receivings/v_purchase_file', $data);
            $this->load->view('templates/footer');
        }
    }

    public function edit($invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('edit') . ' ' . lang('purchases');
        $data['main'] = lang('edit') . ' ' . lang('purchases');

        $data['purchaseType'] = "cash"; //$saleType;//CASH, CREDIT, CASH RETURN AND CREDIT RETURN
        $data['invoice_no'] = $invoice_no;
        $data['edit'] = true;
        //$data['isEstimate'] = $isEstimate;

        //$data['itemDDL'] = $this->M_items->get_allItemsforJSON();
        //$data['customersDDL'] = $this->M_customers->getCustomerDropDown();

        $this->load->view('templates/header', $data);
        $this->load->view('pos/receivings/v_editreceivings', $data);
        $this->load->view('templates/footer');
    }

    public function purchase_transaction($edit = null, $invoice_no = null)
    {
        //INITIALIZE
        $total_amount = 0;
        $discount = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if (count((array)$this->input->post('account_id')) > 0) {

                $this->db->trans_start();

                //IF EDIT THEN DELETE ALL INVOICES AND INSERT AGAIN
                if ($edit != null) {
                    $this->delete($invoice_no, false);
                    $new_invoice_no = $invoice_no;
                } else {
                    //GET PREVIOISE INVOICE NO  
                    @$prev_invoice_no = $this->M_receivings->getMAXPurchaseInvoiceNo();
                    //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                    //$new_invoice_no = 'POS'.date("Ymd").$number;
                    $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                    $new_invoice_no = 'R' . $number;
                }

                //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
                $user_id = $_SESSION['user_id'];
                $company_id = $_SESSION['company_id'];
                $sale_date = $this->input->post("sale_date");
                $supplier_id = $this->input->post("supplier_id");
                $supplier_invoice_no = $this->input->post("supplier_invoice_no");
                $emp_id = ''; //$this->input->post("emp_id");
                $currency_id = ($this->input->post("currency_id") == '' ? 0 : $this->input->post("currency_id"));
                $discount = ($this->input->post("total_discount") == '' ? 0 : $this->input->post("total_discount"));
                $narration = ''; //($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'receive'; //$this->input->post("register_mode");
                $purchaseType = 'cash';
                $is_taxable =  1; //$this->input->post("is_taxable");
                $total_tax_amount =  ($is_taxable == 1 ? $this->input->post("total_tax") : 0);
                $due_date = $this->input->post("due_date");
                $business_address = $this->input->post("business_address");
                $payment_acc_code = $this->input->post("payment_acc_code");
                $sub_total = $this->input->post("sub_total");
                $tax_acc_code = $this->input->post("tax_acc_code");
                $tax_rate = $this->input->post("tax_rate");
                $tax_id = $this->input->post('tax_id');

                if (isset($_FILES['document']) && $_FILES['document']['error'] == 0) {

                    // uploads image in the folder images
                    $temp = explode(".", $_FILES["document"]["name"]);
                    $newfilename = substr(md5(time()), 3, 10) . '.' . end($temp);
                    move_uploaded_file($_FILES['document']['tmp_name'], 'images/purchases/' . $newfilename);
                    $document = $newfilename;
                }

                //if tax amount is checked or 1 then tax will be dedected otherwise not deducted from total amount
                //total net amount 
                $net_total =  $this->input->post("net_total");

                //////

                $data = array(
                    'company_id' => $company_id,
                    'invoice_no' => $new_invoice_no,
                    'supplier_id' => $supplier_id,
                    'supplier_invoice_no' => $supplier_invoice_no,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    'payment_acc_code' => $payment_acc_code,
                    'receiving_date' => $sale_date,
                    'register_mode' => $register_mode,
                    'account' => $purchaseType,
                    'description' => $narration,
                    'discount_value' => $discount,
                    'currency_id' => $currency_id,
                    'total_amount' => ($register_mode == 'receive' ? $sub_total : -$sub_total), //return will be in minus amount
                    'total_tax' => ($register_mode == 'receive' ? $total_tax_amount : -$total_tax_amount), //return will be in minus amount
                    'due_date' => $due_date,
                    'business_address' => $business_address,
                    'tax_rate' => $tax_rate,
                    'tax_id' => $tax_id,
                    'document' => $document,
                );
                $this->db->insert('pos_receivings', $data);
                $receiving_id = $this->db->insert_id();
                ////////

                $data = array(
                    //'entry_id' => $entry_id,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    //'entry_no' => $entry_no,
                    //'name' => $name,
                    'account_code' => $payment_acc_code, //account_id,
                    'date' => $sale_date,
                    //'amount' => $dr_amount,
                    //'ref_account_id' => $ref_id,
                    'debit' => 0,
                    'credit' => $sub_total,
                    'invoice_no' => $new_invoice_no,
                    'narration' => $narration,
                    'company_id' => $company_id,
                );
                $this->db->insert('acc_entry_items', $data);

                foreach ($this->input->post('account_id') as $key => $value) {

                    if ($value != 0) {
                        $account_code  = htmlspecialchars(trim($value));
                        $qty = $this->input->post('qty')[$key];
                        $unit_price = $this->input->post('unit_price')[$key];
                        $cost_price = $this->input->post('cost_price')[$key];
                        $description = $this->input->post('description')[$key];
                        $total_amount = (float)($qty * $cost_price);

                        $data = array(
                            'receiving_id' => $receiving_id,
                            'invoice_no' => $new_invoice_no,
                            'account_code' => $account_code,
                            'item_id' => 0,
                            'description' => $narration,
                            'quantity_purchased' => ($register_mode == 'receive' ? $qty : -$qty), //if sales return then insert amount in negative
                            'item_cost_price' => ($register_mode == 'receive' ? $cost_price : -$cost_price), //actually its avg cost comming from sale from
                            'item_unit_price' => ($register_mode == 'receive' ? $unit_price : -$unit_price), //if sales return then insert amount in negative
                            //'unit_id' => $this->input->post('unit_id')[$key],
                            'description' => $description,
                            'company_id' => $company_id,
                            //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                            //'discount_value' => $this->input->post('discount')[$key],
                            //'tax_id' => ($is_taxable == 1 ? $this->input->post('tax_id')[$key] : 0),
                            //'tax_rate' => ($is_taxable == 1 ? $this->input->post('tax_rate')[$key] : 0),
                            //'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
                        );

                        $this->db->insert('pos_receivings_items', $data);


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
                            'debit' => $total_amount,
                            'credit' => 0,
                            'invoice_no' => $new_invoice_no,
                            'narration' => $narration,
                            'company_id' => $company_id,
                        );
                        $this->db->insert('acc_entry_items', $data);
                    }
                }

                ///////////////
                //TAX ACCOUNT ENTRY
                $data = array(
                    //'entry_id' => $entry_id,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    //'entry_no' => $entry_no,
                    //'name' => $name,
                    'account_code' => $payment_acc_code, //account_id,
                    'date' => $sale_date,
                    //'amount' => $dr_amount,
                    //'ref_account_id' => $ref_id,
                    'debit' => 0,
                    'credit' => $total_tax_amount,
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
                    'account_code' => $tax_acc_code, //account_id,
                    'date' => $sale_date,
                    //'amount' => $dr_amount,
                    //'ref_account_id' => $ref_id,
                    'debit' => $total_tax_amount,
                    'credit' => 0,
                    'invoice_no' => $new_invoice_no,
                    'narration' => $narration,
                    'company_id' => $company_id,
                );
                $this->db->insert('acc_entry_items', $data);
                //////////

                //for logging
                $msg = 'invoice no ' . $new_invoice_no;
                $this->M_logs->add_log($msg, "Purchase transaction", "created", "trans");
                // end logging

                $this->db->trans_complete();
                echo '1';
            }
        }
    }
    //purchase the projuct angularjs
    public function purchaseProducts()
    {
        //INITIALIZE
        $total_amount = 0;
        $discount = 0;
        $amount = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if (count((array)$this->input->post('product_id')) > 0) {
                $this->db->trans_start();
                //GET PREVIOISE INVOICE NO  
                @$prev_invoice_no = $this->M_receivings->getMAXPurchaseInvoiceNo();
                //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                //$new_invoice_no = 'POS'.date("Ymd").$number;
                $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                $new_invoice_no = 'R' . $number;

                //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
                $user_id = $_SESSION['user_id'];
                $company_id = $_SESSION['company_id'];
                $sale_date = $this->input->post("sale_date");
                $supplier_id = $this->input->post("supplier_id");
                $supplier_invoice_no = $this->input->post("supplier_invoice_no");
                $emp_id = ''; //$this->input->post("emp_id");
                $posting_type_code = $this->M_suppliers->getSupplierPostingTypes($supplier_id);
                $currency_id = ($this->input->post("currency_id") == '' ? 0 : $this->input->post("currency_id"));
                $discount = ($this->input->post("total_discount") == '' ? 0 : $this->input->post("total_discount"));
                $narration = ''; //($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'receive'; //$this->input->post("register_mode");
                $purchaseType = 'cash';
                $is_taxable =  1; //$this->input->post("is_taxable");
                $total_tax_amount =  ($is_taxable == 1 ? $this->input->post("total_tax") : 0);
                $due_date = $this->input->post("due_date");
                $business_address = $this->input->post("business_address");
                $bank_id = $this->input->post("bank_id");

                //if tax amount is checked or 1 then tax will be dedected otherwise not deducted from total amount
                //total net amount 
                $total_amount =  ($this->input->post("sub_total") - $discount) - $total_tax_amount;
                $total_return_amount =  ($this->input->post("sub_total") - $discount) - $total_tax_amount; //FOR RETURN PURSPOSE

                //////

                $data = array(
                    'company_id' => $company_id,
                    'invoice_no' => $new_invoice_no,
                    'supplier_id' => $supplier_id,
                    'supplier_invoice_no' => $supplier_invoice_no,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    'receiving_date' => $sale_date,
                    'register_mode' => $register_mode,
                    'account' => $purchaseType,
                    'description' => $narration,
                    'discount_value' => $discount,
                    'currency_id' => $currency_id,
                    'total_amount' => ($register_mode == 'receive' ? $total_amount : -$total_amount), //return will be in minus amount
                    'total_tax' => ($register_mode == 'receive' ? $total_tax_amount : -$total_tax_amount), //return will be in minus amount
                    'due_date' => $due_date,
                    'business_address' => $business_address,
                );
                $this->db->insert('pos_receivings', $data);
                $receiving_id = $this->db->insert_id();
                ////////

                foreach ($this->input->post('product_id') as $key => $value) {

                    if ($value != 0) {
                        $item_id  = htmlspecialchars(trim($value));
                        $qty = $this->input->post('qty')[$key];
                        $unit_price = $this->input->post('unit_price')[$key];
                        $cost_price = $this->input->post('cost_price')[$key];
                        $description = $this->input->post('description')[$key];

                        $data = array(
                            'receiving_id' => $receiving_id,
                            'invoice_no' => $new_invoice_no,
                            'item_id' => $item_id,
                            'description' => $narration,
                            'quantity_purchased' => ($register_mode == 'receive' ? $qty : -$qty), //if sales return then insert amount in negative
                            'item_cost_price' => ($register_mode == 'receive' ? $cost_price : -$cost_price), //actually its avg cost comming from sale from
                            'item_unit_price' => ($register_mode == 'receive' ? $unit_price : -$unit_price), //if sales return then insert amount in negative
                            'unit_id' => $this->input->post('unit_id')[$key],
                            'description' => $description,
                            'company_id' => $company_id,
                            //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                            'discount_value' => $this->input->post('discount')[$key],
                            'tax_id' => ($is_taxable == 1 ? $this->input->post('tax_id')[$key] : 0),
                            'tax_rate' => ($is_taxable == 1 ? $this->input->post('tax_rate')[$key] : 0),
                            //'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
                        );

                        $this->db->insert('pos_receivings_items', $data);

                        //for logging
                        $msg = 'invoice no ' . $new_invoice_no;
                        $this->M_logs->add_log($msg, "Purchase transaction", "created", "trans");
                        // end logging


                        //CHECK SERVICE IF SERVICE THEN DO NOT UPDATE QTY
                        if (trim($this->input->post('item_type')[$key]) != "service") {
                            if ($this->M_items->is_item_exist($item_id)) {
                                $total_stock =  $this->M_items->total_stock($item_id);

                                //if products is to be return then it will add from qty and the avg cost will be reverse to original cost
                                if ($register_mode == 'return') {
                                    $quantity = ($total_stock - $qty);
                                } else {
                                    $quantity = ($total_stock + $qty);
                                }

                                $option_data = array(
                                    'quantity' => $quantity,
                                    'unit_price' => $unit_price,
                                    'avg_cost' => $this->M_items->getAvgCost($item_id, $cost_price, $qty, $register_mode) //calculate avg cost

                                );
                                $this->db->update('pos_items_detail', $option_data, array('id' => $item_id));
                            }
                        }

                        //ADD ITEM DETAIL IN INVENTORY TABLE    
                        $data1 = array(
                            'trans_item' => $item_id,
                            'trans_comment' => 'Receive',
                            'trans_inventory' => $qty,
                            'company_id' => $company_id,
                            'trans_user' => $user_id,
                            'invoice_no' => $new_invoice_no,
                            'cost_price' => $cost_price, //actually its avg cost comming from sale from
                            'unit_price' => $unit_price,

                        );

                        $this->db->insert('pos_inventory', $data1);
                        //////////////

                        $amount += ($qty * $cost_price);
                    }
                } //end foreach

                //'{"invoice_no":"'.$new_invoice_no.'"}'; //redirect to receipt page using this $receiving_id

            } //check product count


            if (count($posting_type_code) !== 0) {


                //total net amount 
                //$total_amount =  round(($amount-$discount)-$data_posted->amount_due,2);

                /////////////////////////////////
                ////   ACCOUNT TRANSACTIONS  /////
                /////////////////////////////////       

                // inventory DEBIT AND
                // CASH CREDITED
                if ($purchaseType == 'cash' && $register_mode == 'receive') {
                    //Search for inventory and cash ledger account for account entry
                    //if invoice is cash then entry will be purchase debit and cash credit and vice versa

                    $dr_ledger_id = $posting_type_code[0]['inventory_acc_code'];
                    if ($bank_id != 0 && isset($bank_id)) {
                        $get_banks = $this->M_banking->get_activeBanking($bank_id);
                        $cr_ledger_id = $posting_type_code[0]['bank_acc_code'];
                    } else {
                        $cr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                    }


                    $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $amount, $amount, ucwords($narration), $new_invoice_no, $sale_date);


                    ///////////////
                    //TAX JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['salestax_acc_code'];
                        //$tax_cr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                        if ($bank_id != 0 && isset($bank_id)) {
                            $tax_cr_ledger_id = $posting_type_code[0]['bank_acc_code'];
                        } else {
                            $tax_cr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                        }

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);
                    }
                    ////////////////
                    ///Bank entry
                    if ($bank_id != 0 && isset($bank_id)) {
                        $data = array(
                            'bank_id' => $bank_id,
                            'account_code' => $get_banks[0]["bank_acc_code"],
                            'dueTo_acc_code' => $get_banks[0]["cash_acc_code"],
                            'date' => $sale_date,
                            'debit' => $amount,
                            'credit' => 0,
                            'invoice_no' => $new_invoice_no,
                            'entry_id' => $entry_id,
                            'narration' => $narration,
                            'company_id' => $_SESSION['company_id']
                        );
                        $this->db->insert('pos_bank_payments', $data);
                    }
                    ///
                }

                //inventory DEBITED AND 
                //ACOUNT PAYABLE SUPPLIER ID IS CREDITED
                elseif ($purchaseType == 'credit' && $register_mode == 'receive') {
                    //Search for inventory and cash ledger account for account entry
                    //if invoice is cash then entry will be purchase debit and cash credit and vice versa

                    $dr_ledger_id = $posting_type_code[0]['inventory_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['payable_acc_code'];

                    $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $amount, $amount, ucwords($narration), $new_invoice_no, $sale_date);

                    // $dr_ledger_id = $posting_type_code[0]['inventory_acc_code'];
                    // $cr_ledger_id = $posting_type_code[0]['payable_acc_code'];
                    // $this->M_entries->addEntries($dr_ledger_id,$cr_ledger_id,$total_amount,$total_amount,ucwords($narration),$new_invoice_no,$sale_date);

                    ///////////////
                    //TAX JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['salestax_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['payable_acc_code'];

                        $entry_id_tax = $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);
                        //M_suppliers SALES TAX PAYMENT ENTRY
                        $this->M_suppliers->addsupplierPaymentEntry($tax_cr_ledger_id, $tax_dr_ledger_id, 0, $total_tax_amount, $supplier_id, $narration, $new_invoice_no, $sale_date, 1, $entry_id_tax);
                    } //////////////// tax

                    ////////////

                    //for customer payment table
                    $this->M_suppliers->addsupplierPaymentEntry($cr_ledger_id, $dr_ledger_id, 0, $total_amount, $supplier_id, $narration, $new_invoice_no, $sale_date);
                    ///
                    ///Bank entry
                    if ($bank_id != 0 && isset($bank_id)) {
                        $get_banks = $this->M_banking->get_activeBanking($bank_id);
                        $data = array(
                            'bank_id' => $bank_id,
                            'account_code' => $get_banks[0]["bank_acc_code"],
                            'dueTo_acc_code' => $get_banks[0]["cash_acc_code"],
                            'date' => $sale_date,
                            'debit' => 0,
                            'credit' => $total_amount,
                            'invoice_no' => $new_invoice_no,
                            'entry_id' => $entry_id,
                            'narration' => $narration,
                            'company_id' => $_SESSION['company_id']
                        );
                        $this->db->insert('pos_bank_payments', $data);
                    }
                    ///
                }
                //PURCHASE RETURN CREDITED AND
                elseif ($purchaseType == 'cash' && $register_mode == 'return') {
                    //Search for purchases returns and discount ledger account for account entry
                    //if invoice is cash then entry will be purchase debit and cash credit and vice versa

                    $dr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['purchasereturn_acc_code'];
                    $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $new_invoice_no, $sale_date);

                    ///////////////
                    //TAX JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['salestax_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);
                    }
                    ////////////////

                }
                ////PURCHASE RETURN CREDITED AND
                elseif ($purchaseType == 'credit' && $register_mode == 'return') {
                    //Search for purchases and cash ledger account for account entry
                    //if invoice is cash then entry will be purchase debit and cash credit and vice versa
                    //SUPPLIER IS DEBITED
                    $dr_ledger_id = $posting_type_code[0]['payable_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['purchasereturn_acc_code'];
                    $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $new_invoice_no, $sale_date);

                    ///////////////
                    //TAX JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['payable_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['salestax_acc_code'];

                        $entry_id_tax =  $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);

                        //M_suppliers SALES TAX PAYMENT ENTRY
                        $this->M_suppliers->addsupplierPaymentEntry($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, 0, $supplier_id, $narration, $new_invoice_no, $sale_date, 1, $entry_id_tax);
                        // //M_suppliers SALES TAX PAYMENT ENTRY
                        // $this->M_suppliers->addsupplierPaymentEntry($tax_cr_ledger_id,$tax_dr_ledger_id,0,$total_tax_amount,$supplier_id,$narration,$new_invoice_no,$sale_date,1,$entry_id_tax);

                    }
                    ////////////////

                    /////////////////
                    //REDUCE THE TOTAL AMOUNT IN RECEINVING TO SHOW EXACT AMOUNT IN OUTSTANDING INVOICES
                    $credit_purchase = $this->M_receivings->get_creditPurchases($supplier_id);
                    foreach ($credit_purchase as $values) {
                        $prev_bal = $values['total_amount'];
                        $cur_amount = $total_return_amount;

                        if ($cur_amount > $prev_bal) {
                            $cur_amount = $prev_bal;
                        } else if ($cur_amount < $prev_bal) {
                            $cur_amount = $total_return_amount;
                        }

                        $data = array(
                            'total_amount' => ($prev_bal - $cur_amount),
                        );

                        $this->db->update('pos_receivings', $data, array('invoice_no' => $values['invoice_no']));

                        $cur_amount = ($total_return_amount - $prev_bal);

                        if ($cur_amount > 0) {
                            $total_return_amount = $cur_amount;
                        } else {
                            $total_return_amount = 0;
                        }
                    }
                    ///////////////

                    //for cusmoter payment table
                    $this->M_suppliers->addsupplierPaymentEntry($dr_ledger_id, $cr_ledger_id, $total_amount, 0, $supplier_id, $narration, $new_invoice_no, $sale_date);
                    ///

                }
                //IF DISCOUNT RECEIVED FROM SUPPLIER
                // PURCHASE DICOUNT CREDITED AND PURCHASES DEBITED
                if ($register_mode == 'receive') {
                    if ($discount != 0) {

                        $cr_ledger_discount_id = $posting_type_code[0]['purchasedis_acc_code'];

                        //journal entries 
                        // PURCHASE DICOUNT CREDITED AND PURCHASES DEBITED
                        $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_discount_id, $discount, $discount, $narration, $new_invoice_no, $sale_date);
                    }
                } elseif ($register_mode == 'return') {
                    if ($discount != 0) {

                        $dr_ledger_discount_id = $posting_type_code[0]['purchasedis_acc_code'];

                        //journal entries 
                        // PURCHASE DICOUNT DEBITED AND PURCHASES OR AC/PAYABLE CREDITED
                        $this->M_entries->addEntries($dr_ledger_discount_id, $cr_ledger_id, $discount, $discount, $narration, $new_invoice_no, $sale_date);
                    }
                }

                //////

                // echo '{"invoice_no":"' . $new_invoice_no . '"}'; //redirect to receipt page using this $new_invoice_no 


                ////////////////////////////
                //      ACCOUNTS CLOSED ..///

                // Posting type  end if 
            } //posting type if
            $this->db->trans_complete();
            echo '1';
        } //end post if
    }

    public function receipt($new_invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $data['receivings_items'] = $this->M_receivings->get_receiving_items($new_invoice_no);
        $receivings_items = $data['receivings_items'];

        $data['title'] = ($receivings_items[0]['register_mode'] == 'receive' ? 'Purchase' : 'Return') . ' Invoice # ' . $new_invoice_no;
        $data['main'] = ''; //($receivings_items[0]['register_mode'] == 'receive' ? '' : 'Return ').'Purchase Invoice';
        $data['invoice_no'] = $new_invoice_no;


        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);

        $this->load->view('templates/header', $data);
        $this->load->view('pos/receivings/v_receipt', $data);
        $this->load->view('templates/footer');
    }

    public function purchase()
    {
        $data = array('langs' => $this->session->userdata('lang'));

        //$this->output->enable_profiler();

        $data['title'] = lang('purchases');
        $data['main'] = lang('purchases');

        $data['supplierDL'] = $this->M_suppliers->getSupplierPostingTypes(2); //search for legder account


        $data['itemDDL'] = $this->M_items->getItemDropDown();
        //$data['colorsDDL'] = $this->M_colors->get_activeColorsDDL();
        $data['sizesDDL'] = $this->M_sizes->get_activeSizesDDL();
        $data['supplierDDL'] = $this->M_suppliers->getSupplierDropDown(); //search for legder account

        $this->load->view('templates/header', $data);
        $this->load->view('pos/receivings/v_purchases', $data);
        $this->load->view('templates/footer');
    }

    function get_purchases_JSON()
    {
        $start_date = FY_START_DATE; //date("Y-m-d", strtotime("last year"));
        $to_date = FY_END_DATE; //date("Y-m-d");

        print_r(json_encode($this->M_receivings->get_selected_receivings($start_date, $to_date)));
    }
    function get_receiving_by_invoice($invoice_no)
    {
        print_r(json_encode($this->M_receivings->get_receiving_by_invoice($invoice_no)));
    }
    function get_receiving_items_only($invoice_no)
    {
        print_r(json_encode($this->M_receivings->get_receiving_items_only($invoice_no)));
    }

    public function getSupplierCurrencyJSON($supplier_id)
    {
        $suppliersCurrency = $this->M_suppliers->get_supplierCurrency($supplier_id);
        echo json_encode($suppliersCurrency);
    }

    public function delete($invoice_no, $redirect = true)
    {
        //if entry deleted then all item qty will be reversed
        $this->db->trans_start();

        // $receiving_items = $this->M_receivings->get_receiving_items($invoice_no);

        // foreach ($receiving_items as $values) {
        //     $total_stock =  $this->M_items->total_stock($values['item_id'], -1, -1);
        //     $quantity = ($total_stock - $values['quantity_purchased']);

        //     $option_data = array(
        //         'quantity' => $quantity,
        //         //'unit_price' =>$values['item_unit_price'],
        //         'avg_cost' => $this->M_items->getAvgCost($values['item_id'], $values['item_cost_price'], $values['quantity_purchased'], 0, 0, 'return') //calculate avg cost

        //     );
        //     $this->db->update('pos_items_detail', $option_data, array('id' => $values['item_id']));


        //     //insert item info into inventory table
        //     $data1 = array(

        //         'trans_item' => $values['item_id'],
        //         'trans_comment' => 'KSRECV Deleted',
        //         'trans_inventory' => $values['quantity_purchased'],
        //         'company_id' => $_SESSION['company_id'],
        //         'trans_user' => $_SESSION['user_id'],
        //         'invoice_no' => $invoice_no
        //     );

        //     $this->db->insert('pos_inventory', $data1);
        // }


        $this->M_receivings->delete($invoice_no);
        $this->db->trans_complete();

        if ($redirect === true) {
            $this->session->set_flashdata('message', 'Entry Deleted');
            redirect('trans/C_receivings/all', 'refresh');
        }
    }

    //Print Invoice in PDF
    function printReceipt($new_invoice_no)
    {
        $sales_items = $this->M_receivings->get_receiving_items($new_invoice_no);
        // var_dump($sales_items);

        $company_id = $_SESSION['company_id'];
        $Company = $this->M_companies->get_companies($company_id);
        $supplier =  @$this->M_suppliers->get_suppliers(@$sales_items[0]['supplier_id']);

        // $this->load->library('Pdf_f');
        // $pdf = new Pdf_f("P", 'mm', 'A4');

        $this->load->library('tfpdf/TFPDF');
        $pdf = new TFPDF();

        $pdf->AddFont('DejaVu', '', 'DejaVuSansCondensed.ttf', true);
        $pdf->AddFont('DejaVuBold', 'B', 'DejaVuSansCondensed-Bold.ttf', true);
        $pdf->AddPage();

        //Display Company Info
        $pdf->SetFont('DejaVuBold', 'B', 14);
        $pdf->Cell(50, 10, $Company[0]['name'], 0, 1);
        $pdf->SetFont('DejaVu', '', 12);
        $pdf->Cell(50, 7, $Company[0]['address'], 0, 1);
        //$pdf->Cell(50, 7, "Salem 636002.", 0, 1);
        $pdf->Cell(50, 7, "PH : " . $Company[0]['contact_no'], 0, 1);

        if ($Company[0]['image'] != "") {
            $pdf->SetY(10);
            $pdf->SetX(90);
            $pdf->Image(base_url() . 'images/company/thumb/' . @$Company[0]['image']);
        }

        //Display INVOICE text
        $pdf->SetY(15);
        $pdf->SetX(-40);
        $pdf->SetFont('DejaVuBold', 'B', 18);
        $pdf->Cell(50, 10, strtoupper(lang("receipt")), 0, 1);

        //Display Horizontal line
        $pdf->Line(0, 42, 210, 42);

        //Billing Details // Body
        $pdf->SetY(49);
        $pdf->SetX(10);
        $pdf->SetFont('DejaVuBold', 'B', 12);
        $pdf->Cell(50, 10, lang('bill') . ' ' . lang('to') . " : ", 0, 1);
        $pdf->SetFont('DejaVu', '', 12);
        $pdf->Cell(50, 7, @$supplier[0]["name"], 0, 1);
        $pdf->Cell(50, 5, @$supplier[0]["address"], 0, 1);
        //$pdf->Cell(50, 5, $supplier[0]["city"], 0, 1);
        $pdf->Cell(50, 5, @$supplier[0]["contact_no"], 0, 1);

        //Display Invoice no
        $pdf->SetY(49);
        $pdf->SetX(-60);
        $pdf->Cell(50, 7, lang('invoice') . " No : " . $new_invoice_no);

        //Display Invoice date
        $pdf->SetY(57);
        $pdf->SetX(-60);
        $pdf->Cell(50, 7, lang('invoice') . ' ' . lang('date') . " : " . date('m/d/Y', strtotime($sales_items[0]["receiving_date"])));

        //Display Table headings
        $pdf->SetY(85);
        $pdf->SetX(10);
        $pdf->SetFont('DejaVuBold', 'B', 12);
        $pdf->Cell(105, 9, strtoupper(lang("description")), 1, 0);
        $pdf->Cell(30, 9, strtoupper(lang("price")), 1, 0, "C");
        $pdf->Cell(25, 9, strtoupper(lang("quantity")), 1, 0, "C");
        $pdf->Cell(30, 9, strtoupper(lang("total")), 1, 1, "C");
        $pdf->SetFont('DejaVu', '', 12);

        $discount = 0;
        $total_cost = 0;
        $total = 0;
        $total_tax = 0;
        //Display table product rows
        foreach ($sales_items as $row) {
            //$total += ($row['item_cost_price'] * $row['quantity_purchased']);
            //$discount += $row['discount_value'];
            // $account_name = $this->M_groups->get_accountName($row['account_code']);
            $cellWidth = 105;
            $cellHeight = 7;
            // check whether the text is overflowing
            if ($pdf->GetStringWidth($row["item_desc"]) < $cellWidth) {
                $line = 1; // if not then do nothing
            } else {
                $textLength = strlen($row["item_desc"]);
                $errMargin = 10;
                $startChar = 0;
                $maxChar = 1;
                $textArray = array();
                $tmpString =  "";

                while ($startChar < $textLength) {

                    while ($pdf->GetStringWidth($tmpString) < ($cellWidth - $errMargin) && ($startChar + $maxChar) < $textLength) {
                        $maxChar++;
                        $tmpString = substr($row["item_desc"], $startChar, $maxChar);
                    }

                    $startChar = $startChar + $maxChar;
                    array_push($textArray, $tmpString);

                    $maxChar = 0;
                    $tmpString = '';
                }
                $line = count($textArray);
            }
            $xPos = $pdf->GetX();
            $yPos = $pdf->GetY();

            $pdf->MultiCell($cellWidth, $cellHeight, $row["item_desc"], 1, 'L');
            $pdf->SetXY($xPos + $cellWidth, $yPos);
            // $pdf->Cell(105, 9, $row["item_desc"],  "LR",0 );
            $pdf->Cell(30, ($line * $cellHeight), number_format($row["item_cost_price"], 2), 1, 0, 'R');
            $pdf->Cell(25, ($line * $cellHeight), number_format($row["quantity_purchased"], 2), 1, 0, 'R');
            $pdf->Cell(30, ($line * $cellHeight), number_format(($row['item_cost_price'] * $row['quantity_purchased']), 2),  1, 1, 'R');
        }
        //Display table empty rows
        for ($i = 0; $i < 12 - count($sales_items); $i++) {
            $pdf->Cell(105, 9, "", "LR", 0);
            $pdf->Cell(30, 9, "", "R", 0, "R");
            $pdf->Cell(25, 9, "", "R", 0, "C");
            $pdf->Cell(30, 9, "", "R", 1, "R");
        }
        //Display table total row
        $total_tax = @$sales_items[0]["total_tax"];
        $pdf->SetFont('DejaVuBold', 'B', 12);
        $pdf->Cell(160, 9, strtoupper(lang("total") . ' ' . lang("tax")), 1, 0, "R");
        $pdf->Cell(30, 9, number_format($total_tax, 2), 1, 1, "R");

        //Display table total row
        $total = (@$sales_items[0]["total_amount"] + $total_tax);
        $pdf->SetFont('DejaVuBold', 'B', 12);
        $pdf->Cell(160, 9, strtoupper(lang("total")), 1, 0, "R");
        $pdf->Cell(30, 9, number_format($total, 2), 1, 1, "R");

        //Display amount in words
        // $pdf->SetY(215);
        // $pdf->SetX(10);
        // $pdf->SetFont('DejaVuBold','B', 12);
        // $pdf->Cell(0, 9, "Amount in Words ", 0, 1);
        // $pdf->SetFont('DejaVu','', 12);
        // $pdf->Cell(0, 9, number_format($total,2), 0, 1);
        ///////////////
        ///body

        //set footer position
        $pdf->SetY(-60);
        //$pdf->SetFont('helvetica', 'B', 12);
        //$pdf->Cell(0, 10, "for ABC COMPUTERS", 0, 1, "R");
        $pdf->Ln(15);
        $pdf->SetFont('DejaVu', '', 12);
        $pdf->Cell(0, 10, "Authorized Signature", 0, 1, "R");
        $pdf->SetFont('DejaVu', '', 10);

        //Display Footer Text
        $pdf->Cell(0, 10, "This is a computer generated invoice", 0, 1, "C");
        ///////////////

        $pdf->Output();
    }
}
