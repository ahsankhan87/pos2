<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_estimate extends MY_Controller{
    
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');        
    }
    
    public function index($saleType='',$customer_id='')
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        $data['title'] = lang('estimates');
        $data['main'] = lang('estimates');
        
        $data['customer_id'] = $customer_id;
        $data['saleType'] = $saleType;
        //when click on sale manu clear the cart first if exist
        //$this->cart->destroy();
        
        //$data['itemDDL'] = $this->M_items->get_allItemsforJSON();
        $data['customersDDL'] = $this->M_customers->getCustomerDropDown();
        $data['supplier_cust'] = $this->M_suppliers->get_cust_supp();
        $data['emp_DDL'] = $this->M_employees->getEmployeeDropDown();
        $data['taxes'] = $this->M_taxes->get_activetaxes();
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/estimate/v_estimate',$data);
        $this->load->view('templates/footer');
    }
    
    public function editestimate($invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        $data['title'] = 'Edit Estimate';
        $data['main'] = 'Edit Estimate';
        
        $data['saleType'] = '';//$saleType;//CASH, CREDIT, CASH RETURN AND CREDIT RETURN
        $data['invoice_no'] = $invoice_no;
        $data['edit'] = true;
        
        //$data['itemDDL'] = $this->M_items->get_allItemsforJSON();
        $data['customersDDL'] = $this->M_customers->getCustomerDropDown();
        $data['supplier_cust'] = $this->M_suppliers->get_cust_supp();
        $data['emp_DDL'] = $this->M_employees->getEmployeeDropDown();
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/estimate/v_editestimateProduct',$data);
        $this->load->view('templates/footer');
    }
    public function sale_transaction()
    {
        $total_amount = 0;
        $discount = 0;
        $unit_price = 0;
        $cost_price = 0;

        if ($this->input->server('REQUEST_METHOD') === 'POST') {

            if (count((array)$this->input->post('account_id')) > 0) {
                $this->db->trans_start();
                //GET PREVIOISE INVOICE NO  
                @$prev_invoice_no = $this->M_estimate->getMAXSaleInvoiceNo();
                //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                //$new_invoice_no = 'POS'.date("Ymd").$number;
                $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                $new_invoice_no = 'E' . $number;

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
                $total_amount = $this->input->post("net_total");

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
                    'total_amount' => ($register_mode == 'sale' ? $total_amount : -$total_amount), //return will be in minus amount
                    'total_tax' => ($register_mode == 'sale' ? $total_tax_amount : -$total_tax_amount), //return will be in minus amount
                    //'is_taxable' => $is_taxable,
                    //'due_date'=>$due_date,
                    // 'business_address'=>$business_address,
                );
                $this->db->insert('pos_estimate', $data);
                $sale_id = $this->db->insert_id();
               
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
                            //'item_id' => 0,//$item_id,
                            'account_code'=>$account_code,
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

                        $this->db->insert('pos_estimate_items', $data);

                       
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
    //sale the projuct 
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
                @$prev_invoice_no = $this->M_estimate->getMAXSaleInvoiceNo();
                //$number = (int) substr($prev_invoice_no,11)+1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                //$new_invoice_no = 'POS'.date("Ymd").$number;
                $number = (int) $prev_invoice_no + 1; // EXTRACT THE LAST NO AND INCREMENT BY 1
                $new_invoice_no = 'E' . $number;

                //GET ALL ACCOUNT CODE WHICH IS TO BE POSTED AMOUNT
                $user_id = $_SESSION['user_id'];
                $company_id = $_SESSION['company_id'];
                $sale_date = $this->input->post("sale_date");
                $customer_id = $this->input->post("customer_id");
                $emp_id = ''; //$this->input->post("emp_id");
                $unit_id = '';//$this->input->post("unit_id");
                // $posting_type_code = $this->M_customers->getCustomerPostingTypes($customer_id);
                $currency_id = ($this->input->post("currency_id") == '' ? 0 : $this->input->post("currency_id"));
                $discount = ($this->input->post("total_discount") == '' ? 0 : $this->input->post("total_discount"));
                $narration = ''; //($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'sale'; //$this->input->post("register_mode");
                $saleType = 'cash';
                $is_taxable =  1; //$this->input->post("is_taxable");
                $total_tax_amount =  ($is_taxable == 1 ? $this->input->post("total_tax") : 0);

                //total net amount 
                $total_amount =  ($this->input->post("sub_total") - $discount) - $total_tax_amount;
                    
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
                );
                $this->db->insert('pos_estimate', $data);
                $sale_id = $this->db->insert_id();
                ////////
                
                foreach ($this->input->post('product_id') as $key => $value) {

                    if ($value != 0) {
                        $item_id  = htmlspecialchars(trim($value));
                        $qty = $this->input->post('qty')[$key];
                        $unit_price = $this->input->post('unit_price')[$key];
                        $cost_price = $this->input->post('cost_price')[$key];

                        $data = array(
                            'sale_id' => $sale_id,
                            'invoice_no' => $new_invoice_no,
                            'item_id' => $item_id,
                            'description' => $narration,
                            'quantity_sold' => $qty, //($register_mode == 'sale' ? $qty : -$qty), //if sales return then insert amount in negative
                            'item_cost_price' => $cost_price, //($register_mode == 'sale' ? $cost_price : -$cost_price), //actually its avg cost comming from sale from
                            'item_unit_price' => $unit_price, //($register_mode == 'sale' ? $unit_price : -$unit_price), //if sales return then insert amount in negative
                            'unit_id' => $unit_id,
                            'company_id' => $company_id,
                            //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                            'discount_value' => $this->input->post('discount')[$key],
                            'tax_id' => ($is_taxable == 1 ? $this->input->post('tax_id')[$key] : 0),
                            'tax_rate' => ($is_taxable == 1 ? $this->input->post('tax_rate')[$key] : 0),
                            'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
                        );

                        $this->db->insert('pos_estimate_items', $data);

                        //for logging
                        $msg = 'invoice no ' . $new_invoice_no;
                        $this->M_logs->add_log($msg, "Estimate transaction", "created", "trans");
                        // end logging

                    }
                } //end foreach
                $this->db->trans_complete();  
                echo '1';  //'{"invoice_no":"'.$new_invoice_no.'"}'; //redirect to receipt page using this $receiving_id

            } //check product count
        }
        
    }
    
    
    public function receipt($new_invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $data['estimate_items'] = $this->M_estimate->get_estimate_items($new_invoice_no);
        $estimate_items = $data['estimate_items'];
        
        //////////////////////////////
        // QR Code
        $this->load->library('ciqrcode');
        ///////////////////////

        $data['title'] = strtoupper($estimate_items[0]['register_mode']).' #'.$new_invoice_no;
        $data['main'] = '';//($estimate_items[0]['register_mode'] == 'sale' ? 'estimate' : 'Return').' Invoice #'.$new_invoice_no;
        $data['invoice_no'] = $new_invoice_no;
        
        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);
            
        $this->load->view('templates/header',$data);
        $this->load->view('pos/estimate/v_receipt',$data);
        //$this->load->view('pos/estimate/v_receipt',$data);
        $this->load->view('templates/footer');
    }
    
    public function allestimate()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        $data['title'] = lang('estimates');
        $data['main'] = lang('estimates');
        
        $start_date = FY_START_DATE;  //date("Y-m-d", strtotime("last month"));
        $to_date = FY_END_DATE; //date("Y-m-d");
        
        $data['main_small'] = "(From:".date('d-m-Y',strtotime($start_date)) ." To:" .date('d-m-Y',strtotime($to_date)).")";
        
        $data['estimate'] = $this->M_estimate->get_estimate(false,$start_date,$to_date);
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/estimate/v_allestimate',$data);
        $this->load->view('templates/footer');
    }
    
    function get_estimate_JSON()
    {
        $start_date = FY_START_DATE;  //date("Y-m-d", strtotime("last year"));
        $to_date = FY_END_DATE; //date("Y-m-d");
        
        print_r(json_encode($this->M_estimate->get_selected_estimate($start_date,$to_date)));
    }
    
    public function getCustomerCurrencyJSON($customer_id)
    {
        $customersCurrency = $this->M_customers->get_customerCurrency($customer_id);
        echo json_encode($customersCurrency);
    }
    
    public function delete($invoice_no,$redirect = true)
    {
        
        $this->db->trans_start();
          
        $this->M_estimate->delete($invoice_no);
        $this->db->trans_complete();   
        
        if($redirect === true)
        {
            $this->session->set_flashdata('message','Entry Deleted');
            redirect('pos/C_estimate/allestimate','refresh');
        }
        
    }
    
    function getEstimateItemsJSON($invoice_no)
    {
        $data = $this->M_estimate->get_estimate_items($invoice_no);
        
        $outp = "";
        foreach($data as $rs)
        {
            //$tm =  json_decode($rs["teams_id"]);
            //print_r($tm);
            
            if ($outp != "") {$outp .= ",";}
            
            $outp .= '{"item_id":"'  . $rs["item_id"] . '",';
            $outp .= '"size_id":"'   . $rs["size_id"]. '",';
            $outp .= '"unit_id":"'   . $rs["unit_id"]. '",';
            $outp .= '"item_cost_price":"'   . $rs["item_cost_price"]. '",';
            $outp .= '"item_unit_price":"'   . $rs["item_unit_price"]. '",';
            $outp .= '"quantity_sold":"'   . $rs["quantity_sold"]. '",';
            $outp .= '"discount_percent":"'   . $rs["discount_percent"]. '",';
            $outp .= '"discount_value":"'   . $rs["discount_value"]. '",';
            $outp .= '"sale_date":"'   . $rs["sale_date"]. '",';
            $outp .= '"register_mode":"'   . $rs["register_mode"]. '",';
            $outp .= '"discount_value":"'   . $rs["discount_value"]. '",';
            $outp .= '"customer_id":"'   . $rs["customer_id"]. '",';
            $outp .= '"employee_id":"'   . $rs["employee_id"]. '",';
            $outp .= '"description":"'   . $rs["description"]. '",';
            $outp .= '"account":"'   . $rs["account"]. '",';
            $outp .= '"tax_id":"'   . $rs["tax_id"]. '",';
            $outp .= '"tax_rate":"'   . $rs["tax_rate"]. '",';
            $outp .= '"tax_name":"",';
            $outp .= '"inventory_acc_code":"",';
            
            $outp .= '"exchange_rate":"'   . $rs["exchange_rate"]. '",';
            $outp .= '"currency_id":"'   . $rs["currency_id"]. '",';
            $outp .= '"service":"'   . $rs["service"]. '",';
            
            $item_name = $this->M_items->get_ItemName($rs["item_id"]);
            $outp .= '"name":"'   . @$item_name . '",';
            
            $size_name = $this->M_sizes->get_sizeName($rs["size_id"]);
            $outp .= '"size":"'   . @$size_name . '",';
            
            $outp .= '"invoice_no":"'. $rs["invoice_no"]     . '"}'; 
        }
            
        $outp ='['.$outp.']';
        echo $outp;
    }
}