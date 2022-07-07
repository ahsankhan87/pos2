<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_sales extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }

    public function index($saleType = '', $customer_id = '', $estimate_no = '')
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('sales');
        $data['main'] = lang('sales');

        $data['customer_id'] = $customer_id;
        $data['saleType'] = $saleType;

        $data['estimate_no'] = $estimate_no; // Estimate invoice no.

        //$data['itemDDL'] = $this->M_items->get_activeItems();
        //$data['customersDDL'] = $this->M_customers->getCustomerDropDown();
        //$data['supplier_cust'] = $this->M_suppliers->get_cust_supp();
        //$data['emp_DDL'] = $this->M_employees->getEmployeeDropDown();
        //$data['salesPostingTypeDDL'] = $this->M_postingTypes->get_SalesPostingTypesDDL();
        //$data['taxes'] = $this->M_taxes->get_activetaxes();

        $this->load->view('templates/header', $data);
        $this->load->view('pos/sales/v_sales', $data);
        $this->load->view('templates/footer');
    }

    public function all()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $start_date = FY_START_DATE;  //date("Y-m-d", strtotime("last month"));
        $to_date = FY_END_DATE; //date("Y-m-d");
        $fiscal_dates = "(From: " . date('d-m-Y', strtotime($start_date)) . " To:" . date('d-m-Y', strtotime($to_date)) . ")";

        $data['title'] = lang('sales') . ' ' . $fiscal_dates;
        $data['main'] = lang('sales');
        $data['sale_type'] = "cash";

        $data['main_small'] = $fiscal_dates;

        $data['sales'] = $this->M_sales->get_sales(false, $start_date, $to_date,'cash');

        $this->load->view('templates/header', $data);
        $this->load->view('pos/sales/v_allsales', $data);
        $this->load->view('templates/footer');
    }
 
    public function editSales($invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('edit') . ' ' . lang('sales');
        $data['main'] = lang('edit') . ' ' . lang('sales');

        $data['saleType'] = ''; //$saleType;//CASH, CREDIT, CASH RETURN AND CREDIT RETURN
        $data['invoice_no'] = $invoice_no;
        $data['edit'] = true;
        //$data['isEstimate'] = $isEstimate;

        //$data['itemDDL'] = $this->M_items->get_allItemsforJSON();
        //$data['customersDDL'] = $this->M_customers->getCustomerDropDown();
        
        $this->load->view('templates/header', $data);
        $this->load->view('pos/sales/v_editsales', $data);
        $this->load->view('templates/footer');
    }

    public function sale_transaction($edit = null,$invoice_no=null)
    {
        $total_amount = 0;
        $discount = 0;
        $unit_price = 0;
        $cost_price = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if (count((array)$this->input->post('account_id')) > 0) {
                $this->db->trans_start();
        
                //IF EDIT THEN DELETE ALL INVOICES AND INSERT AGAIN
                if($edit != null)
                {
                    $this->delete($invoice_no,false);
                    $new_invoice_no = $invoice_no;
                }else{
                    //GET PREVIOISE INVOICE NO  
                    @$prev_invoice_no = $this->M_sales->getMAXSaleInvoiceNo();
                    //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                    //$new_invoice_no = 'POS'.date("Ymd").$number;
                    $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                    $new_invoice_no = 'S' . $number;

                }

                //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
                $user_id = $_SESSION['user_id'];
                $company_id = $_SESSION['company_id'];
                $sale_date = $this->input->post("sale_date");
                $customer_id = $this->input->post("customer_id");
                $emp_id = ''; //$this->input->post("emp_id");
                $unit_id = '';//$this->input->post("unit_id");
                //$posting_type_code = $this->M_customers->getCustomerPostingTypes($customer_id);
                $currency_id = ($this->input->post("currency_id") == '' ? 0 : $this->input->post("currency_id"));
                $discount = ($this->input->post("total_discount") == '' ? 0 : $this->input->post("total_discount"));
                $narration = '';//($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'sale'; //$this->input->post("register_mode");
                $saleType = 'cash';
                $is_taxable =  1; //$this->input->post("is_taxable");
                $total_tax_amount =  ($is_taxable == 1 ? $this->input->post("total_tax") : 0);
                $due_date = $this->input->post("due_date");
                $business_address = $this->input->post("business_address");
                $deposit_to_acc_code = $this->input->post("deposit_to_acc_code");
                $sub_total = $this->input->post("sub_total");
                
                $data = array(
                    'company_id' => $company_id,
                    'invoice_no' => $new_invoice_no,
                    'customer_id' => $customer_id,
                    'deposit_to_acc_code'=>$deposit_to_acc_code,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    'sale_date' => $sale_date,
                    'register_mode' => $register_mode,
                    'account' => $saleType,
                    'description' => $narration,
                    'discount_value' => $discount,
                    'currency_id' => $currency_id,
                    'total_amount' => ($register_mode == 'sale' ? $sub_total : -$sub_total), //return will be in minus amount
                    'total_tax' => ($register_mode == 'sale' ? $total_tax_amount : -$total_tax_amount), //return will be in minus amount
                    //'is_taxable' => $is_taxable,
                    'due_date'=>$due_date,
                    'business_address'=>$business_address,
                );
                $this->db->insert('pos_sales', $data);
                $sale_id = $this->db->insert_id();
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
                    'debit' => $sub_total,
                    'credit' => 0,
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
                        $total_amount = (double)($qty*$unit_price);
                    
                        $data = array(
                            'sale_id' => $sale_id,
                            'invoice_no' => $new_invoice_no,
                            'item_id' => 0,//$item_id,
                            'account_code'=>$account_code,
                            'description' => $narration,
                            'quantity_sold' => ($register_mode == 'sale' ? $qty : -$qty), //if sales return then insert amount in negative
                            'item_cost_price' => ($register_mode == 'sale' ? $cost_price : -$cost_price), //actually its avg cost comming from sale from
                            'item_unit_price' => ($register_mode == 'sale' ? $unit_price : -$unit_price), //if sales return then insert amount in negative
                            'unit_id' => $unit_id,
                            'description' => $description,
                            'company_id' => $company_id,
                            //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                            //'discount_value' => $this->input->post('discount')[$key],
                            'tax_id' => ($is_taxable == 1 ? $this->input->post('tax_id')[$key] : 0),
                            'tax_rate' => ($is_taxable == 1 ? $this->input->post('tax_rate')[$key] : 0),
                            'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
                        );

                        $this->db->insert('pos_sales_items', $data);

                       
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
                            'credit' => $total_amount,
                            'invoice_no' => $new_invoice_no,
                            'narration' => $narration,
                            'company_id' => $company_id,
                        );
                        $this->db->insert('acc_entry_items', $data);

                         //for logging
                         $msg = 'invoice no ' . $new_invoice_no;
                         $this->M_logs->add_log($msg, "Sale transaction", "created", "trans");
                         // end logging
                         
                    }
                }
                $this->db->trans_complete();
                echo '1';
            } //check product count
            
        }
    }
    //sale the projuct angularjs
    public function saleProducts()
    {
        $total_amount = 0;
        $discount = 0;
        $unit_price = 0;
        $cost_price = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if (count((array)$this->input->post('product_id')) > 0) {
                $this->db->trans_start();
                //GET PREVIOISE INVOICE NO  
                @$prev_invoice_no = $this->M_sales->getMAXSaleInvoiceNo();
                //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                //$new_invoice_no = 'POS'.date("Ymd").$number;
                $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                $new_invoice_no = 'S' . $number;

                //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
                $user_id = $_SESSION['user_id'];
                $company_id = $_SESSION['company_id'];
                $sale_date = $this->input->post("sale_date");
                $customer_id = $this->input->post("customer_id");
                $emp_id = ''; //$this->input->post("emp_id");
                $unit_id = '';//$this->input->post("unit_id");
                $posting_type_code = $this->M_customers->getCustomerPostingTypes($customer_id);
                $currency_id = ($this->input->post("currency_id") == '' ? 0 : $this->input->post("currency_id"));
                $discount = ($this->input->post("total_discount") == '' ? 0 : $this->input->post("total_discount"));
                $narration = '';//($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'sale'; //$this->input->post("register_mode");
                $saleType = 'cash';
                $is_taxable =  1; //$this->input->post("is_taxable");
                $total_tax_amount =  ($is_taxable == 1 ? $this->input->post("total_tax") : 0);
                $due_date = $this->input->post("due_date");
                $business_address = $this->input->post("business_address");
                $bank_id = $this->input->post("bank_id");

                //if tax amount is checked or 1 then tax will be dedected otherwise not deducted from total amount
                if ($is_taxable == 1) {
                    //total net amount 
                    $total_amount =  ($this->input->post("sub_total") - $discount) - $total_tax_amount;
                    $total_return_amount =  ($this->input->post("sub_total") - $discount) - $total_tax_amount; //FOR RETURN PURSPOSE
                } else {
                    $total_amount =  ($this->input->post("sub_total") - $discount);
                    $total_return_amount =  ($this->input->post("sub_total") - $discount); //FOR RETURN PURSPOSE
                }
                //////

                $data = array(
                    'company_id' => $company_id,
                    'invoice_no' => $new_invoice_no,
                    'customer_id' => $customer_id,
                    'employee_id' => $emp_id,
                    'user_id' => $user_id,
                    'sale_date' => $sale_date,
                    'register_mode' => $register_mode,
                    'account' => $saleType,
                    'description' => $narration,
                    'discount_value' => $discount,
                    'currency_id' => $currency_id,
                    'total_amount' => ($register_mode == 'sale' ? $total_amount : -$total_amount), //return will be in minus amount
                    'total_tax' => ($register_mode == 'sale' ? $total_tax_amount : -$total_tax_amount), //return will be in minus amount
                    'is_taxable' => $is_taxable,
                    'due_date'=>$due_date,
                    'business_address'=>$business_address,
                );
                $this->db->insert('pos_sales', $data);
                $sale_id = $this->db->insert_id();
                ////////

                foreach ($this->input->post('product_id') as $key => $value) {
                    
                    if ($value != 0) {
                        $item_id  = htmlspecialchars(trim($value));
                        $qty = $this->input->post('qty')[$key];
                        $unit_price = $this->input->post('unit_price')[$key];
                        $cost_price = $this->input->post('cost_price')[$key];
                        $description = $this->input->post('description')[$key];
                        
                        $data = array(
                            'sale_id' => $sale_id,
                            'invoice_no' => $new_invoice_no,
                            'item_id' => $item_id,
                            'description' => $narration,
                            'quantity_sold' => ($register_mode == 'sale' ? $qty : -$qty), //if sales return then insert amount in negative
                            'item_cost_price' => ($register_mode == 'sale' ? $cost_price : -$cost_price), //actually its avg cost comming from sale from
                            'item_unit_price' => ($register_mode == 'sale' ? $unit_price : -$unit_price), //if sales return then insert amount in negative
                            'unit_id' => $unit_id,
                            'description' => $description,
                            'company_id' => $company_id,
                            //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                            'discount_value' => $this->input->post('discount')[$key],
                            'tax_id' => ($is_taxable == 1 ? $this->input->post('tax_id')[$key] : 0),
                            'tax_rate' => ($is_taxable == 1 ? $this->input->post('tax_rate')[$key] : 0),
                            'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
                        );

                        $this->db->insert('pos_sales_items', $data);

                        //for logging
                        $msg = 'invoice no ' . $new_invoice_no;
                        $this->M_logs->add_log($msg, "Sale transaction", "created", "trans");
                        // end logging

                        //CHECK SERVICE IF SERVICE THEN DO NOT UPDATE QTY
                        if (trim($this->input->post('item_type')[$key]) != "service") {
                            if ($this->M_items->is_item_exist($item_id)) {
                                $total_stock =  $this->M_items->total_stock($item_id);

                                //if products is to be return then it will add from qty and the avg cost will be reverse to original cost
                                if ($register_mode == 'return') {
                                    $quantity = ($total_stock + $qty);
                                } else {
                                    $quantity = ($total_stock - $qty);
                                }

                                $option_data = array(
                                    'quantity' => $quantity
                                );
                                $this->db->update('pos_items_detail', $option_data, array('id' => $item_id));
                            }
                        }

                        //ADD ITEM DETAIL IN INVENTORY TABLE    
                        $data1 = array(
                            'trans_item' => $item_id,
                            'trans_comment' => 'Sales',
                            'trans_inventory' => -$qty,
                            'company_id' => $company_id,
                            'trans_user' => $user_id,
                            'invoice_no' => $new_invoice_no,
                            'cost_price' => $cost_price, //actually its avg cost comming from sale from
                            'unit_price' => $unit_price,

                        );

                        $this->db->insert('pos_inventory', $data1);
                        //////////////
                    }
                } //end foreach
                
                //'{"invoice_no":"'.$new_invoice_no.'"}'; //redirect to receipt page using this $receiving_id

            } //check product count


            if (count((array)$posting_type_code) > 0) {

                //////////////////////////////////
                ////   ACCOUNT TRANSACTIONS  /////
                /////////////////////////////////

                //  Cash Debit and Sales Credit
                if ($saleType == 'cash' && $register_mode == 'sale') {
                    //Search for sales and cash ledger account for account entry
                    //if invoice is cash then entry will be cash debit and sales credit and vice versa
                    if($bank_id != 0 && isset($bank_id))
                    {
                        $get_banks = $this->M_banking->get_activeBanking($bank_id);
                        $dr_ledger_id = $posting_type_code[0]['bank_acc_code'];
                    }else{
                        $dr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                    }
                    
                    $cr_ledger_id = $posting_type_code[0]['sales_acc_code'];

                    $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $new_invoice_no, $sale_date);

                    ///////////////
                    //TAX JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        
                        if($bank_id != 0 && isset($bank_id))
                        {
                            $tax_dr_ledger_id = $posting_type_code[0]['bank_acc_code'];
                        }else{
                            $tax_dr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                        }
                   
                        $tax_cr_ledger_id = $posting_type_code[0]['salestax_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);
                    }
                    ////////////////
                    
                    ///Bank entry
                    if($bank_id != 0 && isset($bank_id))
                    {
                        $data = array(
                            'bank_id' => $bank_id,
                            'account_code' => $get_banks[0]["bank_acc_code"],
                            'dueTo_acc_code' => $get_banks[0]["cash_acc_code"],
                            'date' => $sale_date,
                            'debit'=>0,
                            'credit'=>$total_amount,
                            'invoice_no' => $new_invoice_no,
                            'entry_id' => $entry_id,
                            'narration' => $narration,
                            'company_id'=> $_SESSION['company_id']
                            );
                        $this->db->insert('pos_bank_payments', $data); 
                    }
                    ///
                }

                //if Sales is on credit 
                //  AR - Customer Debit and Sales Credit
                elseif ($saleType == 'credit' && $register_mode == 'sale') {
                    //Search for purchases and cash ledger account for account entry
                    //if invoice is cash then entry will be purchase debit and cash credit and vice versa
                    $dr_ledger_id = $posting_type_code[0]['receivable_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['sales_acc_code'];



                    //JOURNAL ENTRY
                    $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $new_invoice_no, $sale_date);


                    //CUSTOMER PAYMENT ENTRY
                    $this->M_customers->addCustomerPaymentEntry($dr_ledger_id, $cr_ledger_id, $total_amount, 0, $customer_id, $narration, $new_invoice_no, $sale_date, 1, $entry_id);

                    if ($total_tax_amount > 0) {
                        ///////////////
                        //TAX JOURNAL ENTRY
                        $tax_dr_ledger_id = $posting_type_code[0]['receivable_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['salestax_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);

                        //CUSTOMER SALES TAX PAYMENT ENTRY
                        $this->M_customers->addCustomerPaymentEntry($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, 0, $customer_id, $narration, $new_invoice_no, $sale_date, 1, $entry_id);
                        //////////////// tax

                    }
                    ///
                    ///Bank entry
                    if($bank_id != 0 && isset($bank_id))
                    {
                        $get_banks = $this->M_banking->get_activeBanking($bank_id);
                        $data = array(
                            'bank_id' => $bank_id,
                            'account_code' => $get_banks[0]["bank_acc_code"],
                            'dueTo_acc_code' => $get_banks[0]["cash_acc_code"],
                            'date' => $sale_date,
                            'debit'=>0,
                            'credit'=>$total_amount,
                            'invoice_no' => $new_invoice_no,
                            'entry_id' => $entry_id,
                            'narration' => $narration,
                            'company_id'=> $_SESSION['company_id']
                            );
                        $this->db->insert('pos_bank_payments', $data); 
                    }
                    ///
                }
                //SALES RETURN DEBITED AND
                elseif ($saleType == 'cash' && $register_mode == 'return') {
                    //Search for sales return and cash ledger account for account entry
                    //if invoice is cash then entry will be sales return debit and cash credit and vice versa
                    $dr_ledger_id = $posting_type_code[0]['salesreturn_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['cash_acc_code'];

                    //JOURNAL ENTRY
                    $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $new_invoice_no, $sale_date);

                    ///////////////
                    //TAX REVERSE JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['salestax_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['cash_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);
                    }
                }
                ////SALES RETURN DEBITED AND
                elseif ($saleType == 'credit' && $register_mode == 'return') {
                    //Search for sales return and cash ledger account for account entry
                    //if invoice is cash then entry will be sales return debit and cash credit and vice versa

                    $dr_ledger_id = $posting_type_code[0]['salesreturn_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['receivable_acc_code'];


                    //JOURNAL ENTRY
                    $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $new_invoice_no, $sale_date);

                    //customer entry
                    $this->M_customers->addCustomerPaymentEntry($cr_ledger_id, $dr_ledger_id, 0, $total_amount, $customer_id, $narration, $new_invoice_no, $sale_date, 1, $entry_id);

                    ///////////////
                    //TAX REVERSE JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['salestax_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['cash_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $new_invoice_no, $sale_date);

                        //CUSTOMER SALES TAX PAYMENT ENTRY
                        $this->M_customers->addCustomerPaymentEntry($tax_cr_ledger_id, $tax_dr_ledger_id, 0, $total_tax_amount, $customer_id, $narration, $new_invoice_no, $sale_date, 1, $entry_id);
                        ////////////////
                    }


                    /////////////////
                    //REDUCE THE TOTAL AMOUNT IN SALES TO SHOW EXACT AMOUNT IN OUTSTANDING INVOICES
                    $creditSales = $this->M_sales->get_creditSales($customer_id);
                    foreach ($creditSales as $values) {
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

                        $this->db->update('pos_sales', $data, array('invoice_no' => $values['invoice_no']));

                        $cur_amount = ($total_return_amount - $prev_bal);

                        if ($cur_amount > 0) {
                            $total_return_amount = $cur_amount;
                        } else {
                            $total_return_amount = 0;
                        }
                    }
                    ///////////////



                }
                //IF DISCOUNT PAID
                // SALES DICOUNT DEBIT AND SALES CREDIT
                if ($register_mode == 'sale') {
                    if ($discount != 0) {

                        $dr_ledger_discount_id = $posting_type_code[0]['salesdis_acc_code'];
                        //journal entries 
                        // SALES DICOUNT DEBIT AND SALES CREDIT
                        $this->M_entries->addEntries($dr_ledger_discount_id, $cr_ledger_id, $discount, $discount, $narration, $new_invoice_no, $sale_date);
                    }
                } elseif ($register_mode == 'return') {
                    if ($discount != 0) {

                        $cr_ledger_discount_id = $posting_type_code[0]['salesdis_acc_code'];
                        //journal entries 
                        // SALES DICOUNT CREDIT AND SALES OR A/C RECEIVABLE DEBITED
                        $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_discount_id, $discount, $discount, $narration, $new_invoice_no, $sale_date);
                    }
                }

                
                /////////////////////////////
                //      ACCOUNTS CLOSED ..///
                /////////////////////////////
            } //posting type if
            $this->db->trans_complete();
            echo '1';
        } //end post if
    }

    //sale the projuct angularjs
    public function editSaleProducts()
    {
        $total_amount = 0;
        $total_cost_amount = 0;
        $discount = 0;
        $unit_price = 0;
        $cost_price = 0;

        // get posted data
        $data_posted = json_decode(file_get_contents("php://input", true));

        //        print_r($data_posted);
        //        echo die;

        if (count((array)$data_posted) > 0) {
            $this->db->trans_start();

            //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
            $invoice_no = $data_posted->invoice_no;
            $sale_date = date('Y-m-d', strtotime($data_posted->sale_date));
            $customer_id = $data_posted->customer_id;
            $emp_id = $data_posted->emp_id;
            $supplier_id = $data_posted->supplier_id;
            $posting_type_code = $this->M_customers->getCustomerPostingTypes($customer_id);
            $sale_supp_posting_type_code = $this->M_suppliers->getCustSuppPostingTypes($supplier_id);
            $exchange_rate = ($data_posted->exchange_rate == '' ? 0 : $data_posted->exchange_rate);
            $currency_id = ($data_posted->currency_id == '' ? 0 : $data_posted->currency_id);
            $discount = ($data_posted->discount == '' ? 0 : $data_posted->discount);
            $narration = ($data_posted->description == '' ? '' : $data_posted->description);
            $total_tax_amount =  $data_posted->total_tax;
            $is_taxable =  $data_posted->is_taxable;
            $total_tax_amount =  ($is_taxable == 1 ? $data_posted->total_tax : 0);
            //$total_tax_amount =  $data_posted->total_tax;

            //if multi currency is used then multiply $exchange_rate with amount

            //if tax amount is checked or 1 then tax will be dedected otherwise not deducted from total amount

            if ($is_taxable == 1) {
                $total_amount =  ($data_posted->total_amount - $discount) - $total_tax_amount;
                $total_return_amount =  ($data_posted->total_amount - $discount) - $total_tax_amount; //FOR RETURN PURSPOSE
            } else {
                $total_amount =  ($data_posted->total_amount - $discount);
                $total_return_amount =  ($data_posted->total_amount - $discount); //FOR RETURN PURSPOSE
            }
            //////
            //////

            if (count($posting_type_code) !== 0 || count($sale_supp_posting_type_code) !== 0)
            //if(count($sale_supp_posting_type_code) !== 0)
            {
                if ($supplier_id) {
                    $posting_type_code = $sale_supp_posting_type_code;
                }

                //DELETE ALS SALES AND ITEMS AND ACCOUNT ENTRY AGAINST INVOICE NO
                //AND THEN INSERT NEW ENTRIES.
                $this->delete($invoice_no, false);
                //////

                $data = array(
                    'company_id' => $_SESSION['company_id'],
                    'invoice_no' => $invoice_no,
                    'customer_id' => $customer_id,
                    'supplier_id' => $supplier_id,
                    'employee_id' => $emp_id,
                    'user_id' => $_SESSION['user_id'],
                    'sale_date' => $sale_date,
                    'register_mode' => $data_posted->register_mode,
                    'account' => $data_posted->saleType,
                    //'amount_due'=>$data_posted->amount_due,
                    'description' => $narration,
                    'discount_value' => $discount,
                    'currency_id' => $currency_id,
                    'exchange_rate' => $exchange_rate,
                    'total_amount' => $total_amount,
                    'total_tax' => $total_tax_amount,
                    'is_taxable' => $is_taxable,
                );

                $this->db->insert('pos_sales', $data);

                $sale_id = $this->db->insert_id();
                $inventory_acc_code = array();
                //extract JSON array items from posted data.
                foreach ($data_posted->items as $posted_values) :

                    $service = ($posted_values->service == null ? 0 : $posted_values->service);

                    $data = array(
                        'sale_id' => $sale_id,
                        'invoice_no' => $invoice_no,
                        'item_id' => $posted_values->item_id,
                        'description' => '',
                        'quantity_sold' => $posted_values->quantity,
                        'item_cost_price' => $posted_values->cost_price, //actually its avg cost comming from sale from
                        'item_unit_price' => $posted_values->unit_price,
                        'currency_id' => $currency_id,
                        'exchange_rate' => $exchange_rate,
                        'size_id' => $posted_values->size_id,
                        'company_id' => $_SESSION['company_id'],
                        //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                        'discount_value' => ($posted_values->discount_value == null ? 0 : $posted_values->discount_value),
                        'service' => $service,
                        'tax_id' => ($is_taxable == 1 ? $posted_values->tax_id : 0),
                        'tax_rate' => ($is_taxable == 1 ? $posted_values->tax_rate : 0),
                        'inventory_acc_code' => $posted_values->inventory_acc_code
                    );

                    $this->db->insert('pos_sales_items', $data);

                    //for logging
                    $msg = 'invoice no ' . $invoice_no;
                    $this->M_logs->add_log($msg, "sale transaction", "created", "trans");
                    // end logging

                    //CHECK SERVICE IF SERVICE THEN DO NOT UPDATE QTY
                    if ($service !== 1) {
                        if ($this->M_items->checkItemOptions($posted_values->item_id, 0, $posted_values->size_id)) {
                            $total_stock =  $this->M_items->total_stock($posted_values->item_id, 0, $posted_values->size_id);

                            //if products is to be return then it will add from qty and the avg cost will be reverse to original cost
                            if ($data_posted->register_mode == 'return') {
                                $quantity = ($total_stock + $posted_values->quantity);
                            } else {
                                $quantity = ($total_stock - $posted_values->quantity);
                            }

                            $option_data = array(
                                'quantity' => $quantity
                            );
                            $this->db->update('pos_items_detail', $option_data, array('size_id' => $posted_values->size_id, 'item_id' => $posted_values->item_id));
                        }
                    }


                    //ADD ITEM DETAIL IN INVENTORY TABLE    
                    $data1 = array(
                        'trans_item' => $posted_values->item_id,
                        'trans_comment' => 'KSPOS',
                        'trans_inventory' => -$posted_values->quantity,
                        'company_id' => $_SESSION['company_id'],
                        'trans_user' => $_SESSION['user_id'],
                        'invoice_no' => $invoice_no,
                        'cost_price' => $posted_values->cost_price, //actually its avg cost comming from sale from
                        'unit_price' => $posted_values->unit_price,

                    );

                    $this->db->insert('pos_inventory', $data1);
                    //////////////

                    $cost_price += ($posted_values->quantity * $posted_values->cost_price);
                    $unit_price += ($posted_values->quantity * $posted_values->unit_price);

                    //discount percent if percentage is used
                    //$discount += ($posted_values->quantity * $posted_values->unit_price)*$posted_values->discount/100;

                    //ADD INVENTORY AMOUNT WHICH IS SELECTED IN product and services
                    @$inventory_acc_code[$posted_values->inventory_acc_code] += $posted_values->cost_price;

                endforeach;

                $service = $service; //again assing service to new variable bcuz of loop end 

                //if multi currency is used then multiply $exchange_rate with amount
                if (@$_SESSION['multi_currency'] == 1) {
                    //Total Cost amount
                    $total_cost_amount =  round(($cost_price) / $exchange_rate, 2);
                } else {
                    //Total Cost amount
                    $total_cost_amount =  round(($cost_price), 2);
                }

                //////////////////////////////////
                ////   ACCOUNT TRANSACTIONS  /////
                /////////////////////////////////
                if ($data_posted->register_mode == 'sale') {
                    if ($service !== 1) {
                        foreach ($inventory_acc_code as $inventory_code => $amountt) {

                            $inventory_dr_ledger_id = $posting_type_code[0]['cos_acc_code'];
                            $inventory_cr_ledger_id = $inventory_code; // USE INVENTORY ACCOUNT CODE FROM ITEM TABEL NOT POSTING TYPE TABLE
                            //////////////
                            $this->M_entries->addEntries($inventory_dr_ledger_id, $inventory_cr_ledger_id, $amountt, $amountt, ucwords($narration), $invoice_no, $sale_date);
                        }
                    }
                }
                if ($data_posted->register_mode == 'return') {
                    if ($service !== 1) {
                        foreach ($inventory_acc_code as $inventory_code => $amountt) {

                            $inventory_cr_ledger_id = $posting_type_code[0]['cos_acc_code'];
                            $inventory_dr_ledger_id = $inventory_code; // USE INVENTORY ACCOUNT CODE FROM ITEM TABEL NOT POSTING TYPE TABLE
                            //////////////
                            $this->M_entries->addEntries($inventory_dr_ledger_id, $inventory_cr_ledger_id, $amountt, $amountt, ucwords($narration), $invoice_no, $sale_date);
                        }
                    }
                }

                //  Cash Debit and Sales Credit
                if ($data_posted->saleType == 'cash' && $data_posted->register_mode == 'sale') {
                    //Search for sales and cash ledger account for account entry
                    //if invoice is cash then entry will be cash debit and sales credit and vice versa
                    $dr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['sales_acc_code'];

                    $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $invoice_no, $sale_date);
                    ////////////////

                    ///////////////
                    //TAX JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['cash_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['salestax_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $invoice_no, $sale_date);
                    }
                    ////////////////


                }

                //if Sales is on credit 
                //  AR - Customer Debit and Sales Credit
                elseif ($data_posted->saleType == 'credit' && $data_posted->register_mode == 'sale') {
                    //Search for purchases and cash ledger account for account entry
                    //if invoice is cash then entry will be purchase debit and cash credit and vice versa
                    $dr_ledger_id = $posting_type_code[0]['receivable_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['sales_acc_code'];


                    //for cusmoter payment table
                    if ($supplier_id) {
                        //JOURNAL ENTRY
                        $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $invoice_no, $sale_date);

                        //SUPPLIER PAYMENT ENTRY
                        $this->M_suppliers->addsupplierPaymentEntry($dr_ledger_id, $cr_ledger_id, $total_amount, 0, $supplier_id, $narration, $invoice_no, $sale_date, $exchange_rate, $entry_id);

                        /////////////////
                        //REDUCE THE TOTAL AMOUNT IN RECEINVING TO SHOW EXACT AMOUNT IN OUTSTANDING INVOICES
                        $credit_purchase = $this->M_receivings->get_creditPurchases($supplier_id);
                        foreach ($credit_purchase as $values) {
                            $prev_bal = $values['paid'];
                            $cur_amount = $total_return_amount; //current amount

                            if ($cur_amount > $prev_bal) {
                                $cur_amount = $total_return_amount;
                            } else if ($cur_amount < $prev_bal) {
                                $cur_amount = $prev_bal;
                            }

                            $data = array(
                                'paid' => ($cur_amount + $total_return_amount),
                            );

                            //$this->db->update('pos_receivings',$data,array('invoice_no'=>$values['invoice_no']));
                            $this->M_receivings->updatePaidAmount($values['invoice_no'], $data);

                            $cur_amount = ($total_return_amount + $prev_bal);

                            if ($cur_amount > 0) {
                                $total_return_amount = $cur_amount;
                            } else {
                                $total_return_amount = 0;
                            }
                        }
                        ///////////////
                    } else {

                        //JOURNAL ENTRY
                        $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $invoice_no, $sale_date);

                        //CUSTOMER PAYMENT ENTRY
                        $this->M_customers->addCustomerPaymentEntry($dr_ledger_id, $cr_ledger_id, $total_amount, 0, $customer_id, $narration, $invoice_no, $sale_date, $exchange_rate, $entry_id);

                        ///////////////
                        //TAX JOURNAL ENTRY
                        if ($total_tax_amount > 0) {
                            $tax_dr_ledger_id = $posting_type_code[0]['receivable_acc_code'];
                            $tax_cr_ledger_id = $posting_type_code[0]['salestax_acc_code'];

                            $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $invoice_no, $sale_date);

                            //CUSTOMER SALES TAX PAYMENT ENTRY
                            $this->M_customers->addCustomerPaymentEntry($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, 0, $customer_id, $narration, $invoice_no, $sale_date, $exchange_rate, $entry_id);
                            //////////////// tax
                        }
                    }

                    ///
                }
                //SALES RETURN DEBITED AND
                elseif ($data_posted->saleType == 'cash' && $data_posted->register_mode == 'return') {
                    //Search for sales return and cash ledger account for account entry
                    //if invoice is cash then entry will be sales return debit and cash credit and vice versa
                    $dr_ledger_id = $posting_type_code[0]['salesreturn_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['cash_acc_code'];

                    //JOURNAL ENTRY
                    $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $invoice_no, $sale_date);

                    ///////////////
                    //TAX REVERSE JOURNAL ENTRY
                    if ($total_tax_amount > 0) {
                        $tax_dr_ledger_id = $posting_type_code[0]['salestax_acc_code'];
                        $tax_cr_ledger_id = $posting_type_code[0]['cash_acc_code'];

                        $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $invoice_no, $sale_date);
                    }
                    ////////////////


                }
                ////SALES RETURN DEBITED AND
                elseif ($data_posted->saleType == 'credit' && $data_posted->register_mode == 'return') {
                    //Search for sales return and cash ledger account for account entry
                    //if invoice is cash then entry will be sales return debit and cash credit and vice versa

                    $dr_ledger_id = $posting_type_code[0]['salesreturn_acc_code'];
                    $cr_ledger_id = $posting_type_code[0]['receivable_acc_code'];


                    //for cusmoter payment table
                    if ($supplier_id) {
                        //JOURNAL ENTRY
                        $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $invoice_no, $sale_date);

                        $this->M_suppliers->addsupplierPaymentEntry($cr_ledger_id, $dr_ledger_id, 0, $total_amount, $supplier_id, $narration, $invoice_no, $sale_date, $exchange_rate, $entry_id);

                        /////////////////
                        //REDUCE THE PAID AMOUNT IN RECEINVING TO SHOW EXACT AMOUNT IN OUTSTANDING INVOICES
                        $credit_purchase = $this->M_receivings->get_creditPurchases($supplier_id);
                        foreach ($credit_purchase as $values) {
                            $prev_bal = $values['paid'];
                            $cur_amount = $total_return_amount;

                            if ($cur_amount > $prev_bal) {
                                $cur_amount = $prev_bal;
                            } else if ($cur_amount < $prev_bal) {
                                $cur_amount = $total_return_amount;
                            }

                            $data = array(
                                'paid' => ($prev_bal - $cur_amount),
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
                    } //supplier end
                    else {
                        //JOURNAL ENTRY
                        $entry_id = $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_id, $total_amount, $total_amount, ucwords($narration), $invoice_no, $sale_date);

                        //customer entry
                        $this->M_customers->addCustomerPaymentEntry($cr_ledger_id, $dr_ledger_id, 0, $total_amount, $customer_id, $narration, $invoice_no, $sale_date, $exchange_rate, $entry_id);

                        ///////////////
                        //TAX REVERSE JOURNAL ENTRY
                        if ($total_tax_amount > 0) {
                            $tax_dr_ledger_id = $posting_type_code[0]['salestax_acc_code'];
                            $tax_cr_ledger_id = $posting_type_code[0]['cash_acc_code'];

                            $this->M_entries->addEntries($tax_dr_ledger_id, $tax_cr_ledger_id, $total_tax_amount, $total_tax_amount, ucwords($narration), $invoice_no, $sale_date);

                            //CUSTOMER SALES TAX PAYMENT ENTRY
                            $this->M_customers->addCustomerPaymentEntry($tax_cr_ledger_id, $tax_dr_ledger_id, 0, $total_tax_amount, $customer_id, $narration, $invoice_no, $sale_date, $exchange_rate, $entry_id);
                        }
                        ////////////////
                        //tax end

                        /////////////////
                        //REDUCE THE TOTAL AMOUNT IN SALES TO SHOW EXACT AMOUNT IN OUTSTANDING INVOICES
                        $creditSales = $this->M_sales->get_creditSales($customer_id);
                        foreach ($creditSales as $values) {
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

                            $this->db->update('pos_sales', $data, array('invoice_no' => $values['invoice_no']));

                            $cur_amount = ($total_return_amount - $prev_bal);

                            if ($cur_amount > 0) {
                                $total_return_amount = $cur_amount;
                            } else {
                                $total_return_amount = 0;
                            }
                        }
                        ///////////////
                    } //customer end


                }
                //IF DISCOUNT PAID
                // SALES DICOUNT DEBIT AND SALES CREDIT
                if ($data_posted->register_mode == 'sale') {
                    if ($discount != 0) {

                        $dr_ledger_discount_id = $posting_type_code[0]['salesdis_acc_code'];
                        //journal entries 
                        // SALES DICOUNT DEBIT AND SALES CREDIT
                        $this->M_entries->addEntries($dr_ledger_discount_id, $cr_ledger_id, $discount, $discount, $narration, $invoice_no, $sale_date);
                    }
                } elseif ($data_posted->register_mode == 'return') {
                    if ($discount != 0) {

                        $cr_ledger_discount_id = $posting_type_code[0]['salesdis_acc_code'];
                        //journal entries 
                        // SALES DICOUNT CREDIT AND SALES OR A/C RECEIVABLE DEBITED
                        $this->M_entries->addEntries($dr_ledger_id, $cr_ledger_discount_id, $discount, $discount, $narration, $invoice_no, $sale_date);
                    }
                }

                echo '{"invoice_no":"' . $invoice_no . '"}'; //redirect to receipt page using this $receiving_id

                $this->db->trans_complete();

                /////////////////////////////
                //      ACCOUNTS CLOSED ..///
                /////////////////////////////

            } // Posting type  end if 
            else {
                echo '{"invoice_no":"no-posting-type"}';
            }
        } //$data_posted if close
        else {
            echo 'No Data';
        }
    }
    public function receipt($new_invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $data['sales_items'] = $this->M_sales->get_sales_items($new_invoice_no);
        $sales_items = $data['sales_items'];

        //////////////////////////////
        // QR Code
        $this->load->library('ciqrcode');
        ///////////////////////

        $data['title'] = ($sales_items[0]['register_mode'] == 'sale' ? 'Sales' : 'Return') . ' Invoice #' . $new_invoice_no;
        $data['main'] = ''; //($sales_items[0]['register_mode'] == 'sale' ? 'Sales' : 'Return').' Invoice #'.$new_invoice_no;
        $data['invoice_no'] = $new_invoice_no;

        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);

        $this->load->view('templates/header', $data);
        //$this->load->view('pos/sales/v_receipt_small', $data);
        $this->load->view('pos/sales/v_receipt',$data);
        $this->load->view('templates/footer');
    }


    function get_sales_JSON()
    {
        $start_date = FY_START_DATE;  //date("Y-m-d", strtotime("last year"));
        $to_date = FY_END_DATE; //date("Y-m-d");

        print_r(json_encode($this->M_sales->get_selected_sales($start_date, $to_date)));
    }

    public function getCustomerCurrencyJSON($customer_id)
    {
        $customersCurrency = $this->M_customers->get_customerCurrency($customer_id);
        echo json_encode($customersCurrency);
    }

    public function delete($invoice_no, $redirect = true)
    {
        $this->db->trans_start();

        $this->M_sales->delete($invoice_no);
        $this->db->trans_complete();

        if ($redirect === true) {
            $this->session->set_flashdata('message', 'Entry Deleted');
            redirect('pos/C_sales/all', 'refresh');
        }
    }

    function getSalesItemsJSON($invoice_no)
    {
        $data = $this->M_sales->get_sales_items_only($invoice_no);

        $outp = "";
        foreach ($data as $rs) {
            //$tm =  json_decode($rs["teams_id"]);
            //print_r($tm);

            if ($outp != "") {
                $outp .= ",";
            }

            //$outp .= '{"item_id":"'  . $rs["item_id"] . '",';
            $outp .= '{"account_code":"'  . $rs["account_code"] . '",';
            //$outp .= '"unit_id":"'   . $rs["unit_id"] . '",';
            $outp .= '"item_cost_price":"'   . $rs["item_cost_price"] . '",';
            $outp .= '"item_unit_price":"'   . $rs["item_unit_price"] . '",';
            $outp .= '"quantity_sold":"'   . $rs["quantity_sold"] . '",';
            $outp .= '"discount_percent":"'   . $rs["discount_percent"] . '",';
            $outp .= '"discount_value":"'   . $rs["discount_value"] . '",';
            $outp .= '"tax_id":"'   . $rs["tax_id"] . '",';
            $outp .= '"tax_rate":"'   . $rs["tax_rate"] . '",';
            $outp .= '"tax_name":"",';
            $outp .= '"description":"'   . $rs["description"] . '",';
            //$outp .= '"service":"'   . $rs["service"] . '",';

            //$item_name = $this->M_items->get_ItemName($rs["item_id"]);
            //$item_name = $this->M_groups->get_accountName($rs["account_code"]);
            //$outp .= '"name":"'   . @$item_name . '",';

            $outp .= '"invoice_no":"' . $rs["invoice_no"]     . '"}';
        }

        $outp = '[' . $outp . ']';
        echo $outp;
    }


    function getSalesJSON($invoice_no)
    {
        $data = $this->M_sales->get_sales_by_invoice($invoice_no);

        $outp = "";
        foreach ($data as $rs) {
            //$tm =  json_decode($rs["teams_id"]);
            //print_r($tm);

            if ($outp != "") {
                $outp .= ",";
            }

            $outp .= '{"sale_time":"'  . $rs["sale_time"] . '",';
            $outp .= '"sale_date":"'   . $rs["sale_date"] . '",';
            $outp .= '"customer_id":"'   . $rs["customer_id"] . '",';
            $outp .= '"employee_id":"'   . $rs["employee_id"] . '",';
            $outp .= '"user_id":"'   . $rs["user_id"] . '",';
            $outp .= '"register_mode":"'   . $rs["register_mode"] . '",';
            $outp .= '"account":"'   . $rs["account"] . '",';
            $outp .= '"description":"'   . $rs["description"] . '",';
            $outp .= '"discount_value":"'   . $rs["discount_value"] . '",';
            $outp .= '"total_amount":"'   . $rs["total_amount"] . '",';
            $outp .= '"total_tax":"'   . $rs["total_tax"] . '",';
            $outp .= '"business_address":"'   . $rs["business_address"] . '",';
            $outp .= '"deposit_to_acc_code":"'   . $rs["deposit_to_acc_code"] . '",';
            $outp .= '"due_date":"'   . $rs["due_date"] . '",';

            $outp .= '"exchange_rate":"'   . $rs["exchange_rate"] . '",';
            $outp .= '"currency_id":"'   . $rs["currency_id"] . '",';

            $outp .= '"invoice_no":"' . $rs["invoice_no"]     . '"}';
        }

        $outp = '[' . $outp . ']';
        echo $outp;
    }
}
