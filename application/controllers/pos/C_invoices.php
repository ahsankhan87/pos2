<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class C_invoices extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }

    public function index($saleType = '', $customer_id = '', $estimate_no = '')
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = 'Invoices';
        $data['main'] = 'Invoices';

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

    public function all()//credit all sales
    {
        $data = array('langs' => $this->session->userdata('lang'));
        $start_date = FY_START_DATE;  //date("Y-m-d", strtotime("last month"));
        $to_date = FY_END_DATE; //date("Y-m-d");
        $fiscal_dates = "(From: " . date('d-m-Y', strtotime($start_date)) . " To:" . date('d-m-Y', strtotime($to_date)) . ")";

        $data['title'] = lang('invoice') . ' ' . $fiscal_dates;
        $data['main'] = lang('invoice');
        $data['sale_type'] = "credit";

        $data['main_small'] = $fiscal_dates;

        $data['sales'] = $this->M_sales->get_sales(false, $start_date, $to_date,'credit');

        $this->load->view('templates/header', $data);
        $this->load->view('pos/sales/v_allsales', $data);
        $this->load->view('templates/footer');
    }

    public function editSales($invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));

        $data['title'] = lang('edit') . ' ' . lang('sales');
        $data['main'] = lang('edit') . ' ' . lang('sales');

        $data['saleType'] = 'credit'; //$saleType;//CASH, CREDIT, CASH RETURN AND CREDIT RETURN
        $data['invoice_no'] = $invoice_no;
        $data['edit'] = true;
        //$data['isEstimate'] = $isEstimate;

        //$data['itemDDL'] = $this->M_items->get_allItemsforJSON();
        // $data['customersDDL'] = $this->M_customers->getCustomerDropDown();
        // $data['supplier_cust'] = $this->M_suppliers->get_cust_supp();
        // $data['emp_DDL'] = $this->M_employees->getEmployeeDropDown();

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
                $narration = trim('');//($this->input->post("description") == '' ? '' : $this->input->post("description"));
                $register_mode = 'sale'; //$this->input->post("register_mode");
                $saleType = trim($this->input->post("sale_type"));
                $is_taxable =  1; //$this->input->post("is_taxable");
                $total_tax_amount =  ($is_taxable == 1 ? $this->input->post("total_tax") : 0);
                $due_date = $this->input->post("due_date");
                $business_address = trim($this->input->post("business_address"));
                $deposit_to_acc_code = $this->input->post("deposit_to_acc_code");
                $sub_total = $this->input->post("sub_total");
                $tax_acc_code = $this->input->post("tax_acc_code");
                $tax_rate = $this->input->post("tax_rate");
                $tax_id = $this->input->post('tax_id'); 

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
                    'tax_rate'=>$tax_rate,
                    'tax_id' => $tax_id,

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
                            'quantity_sold' => $qty, //if sales return then insert amount in negative
                            'item_cost_price' => $cost_price, //actually its avg cost comming from sale from
                            'item_unit_price' => $unit_price, //if sales return then insert amount in negative
                            'unit_id' => $unit_id,
                            'description' => $description,
                            'company_id' => $company_id,
                            //'discount_percent'=>($posted_values->discount_percent == null ? 0 : $posted_values->discount_percent),
                            //'discount_value' => $this->input->post('discount')[$key],
                            //'tax_id' => ($is_taxable == 1 ? $this->input->post('tax_id')[$key] : 0),
                            //'tax_rate' => ($is_taxable == 1 ? $this->input->post('tax_rate')[$key] : 0),
                            //'inventory_acc_code' => '', //$this->input->post('inventory_acc_code')[$key]
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
                         $this->M_logs->add_log($msg, "Invoice transaction", "created", "trans");
                         // end logging
                         
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
                        'account_code' => $deposit_to_acc_code, //account_id,
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
                        'debit' => 0,
                        'credit' => $total_tax_amount,
                        'invoice_no' => $new_invoice_no,
                        'narration' => $narration,
                        'company_id' => $company_id,
                    );
                    $this->db->insert('acc_entry_items', $data);
                    //////////

                $this->db->trans_complete();
                echo '1';
            } //check product count
            
        }
    }
   
    function receivePayment($customer_id,$invoice_no)
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        $data['title'] = 'Receive Payment';
        $data['main'] = 'Receive Payment';
        
        $data['sales'] = $this->M_sales->get_sales_by_invoice($invoice_no);
        $data['customer'] = $this->M_customers->get_customers($customer_id);
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/sales/v_receivePayment',$data);
        $this->load->view('templates/footer');
    }

    function makePayment()
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
            $customer_id = $this->input->post('customer_id',true);
            $company_id = $_SESSION['company_id'];
            $sale_date = $this->input->post("payment_date");
            $narration = ($this->input->post("comment") == '' ? '' : $this->input->post("comment"));
            $dr_acc_code = $this->input->post("deposit_to_acc_code");
            $cr_acc_code = $this->input->post("cr_acc_code");
            $amount = $this->input->post("amount");
            $paid_amount = $this->input->post("paid_amount");
            $invoice_no = $this->input->post("invoice_no");
            
            $data = array(
                'paid' => ($paid_amount+$amount),
                );
            $this->M_sales->updatePaidAmount($invoice_no,$data);
        
            ////////
            $data = array(
                //'entry_id' => $entry_id,
                // 'employee_id' => $emp_id,
                'user_id' => $user_id,
                //'entry_no' => $entry_no,
                //'name' => $name,
                'account_code' => $dr_acc_code, //account_id,
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

            ////////
            $data = array(
                //'entry_id' => $entry_id,
                // 'employee_id' => $emp_id,
                'user_id' => $user_id,
                //'entry_no' => $entry_no,
                //'name' => $name,
                'account_code' => $cr_acc_code, //account_id,
                'date' => $sale_date,
                //'amount' => $dr_amount,
                //'ref_account_id' => $ref_id,
                'debit' => 0,
                'credit' => $amount,
                'invoice_no' => $new_invoice_no,
                'narration' => $narration,
                'company_id' => $company_id,
            );
            $this->db->insert('acc_entry_items', $data);

            //for logging
            $msg = 'invoice no ' . $new_invoice_no;
            $this->M_logs->add_log($msg, "Payment Receipt transaction", "created", "trans");
            // end logging

            $this->db->trans_complete();

            $this->session->set_flashdata('message', 'Payment entry completed');
            redirect('pos/C_invoices/all', 'refresh');
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
            redirect('pos/C_invoices/all', 'refresh');
        }
    }

    function getSalesItemsJSON($invoice_no)
    {
        $data = $this->M_sales->get_sales_items_only($invoice_no);
        print_r(json_encode($data));
    }


    function getSalesJSON($invoice_no)
    {
        $data = $this->M_sales->get_sales_by_invoice($invoice_no);
        print_r(json_encode($data));
       
    }

    //Print Invoice in PDF
    function printReceipt($new_invoice_no)
    {
        $sales_items = $this->M_sales->get_sales_items($new_invoice_no);
        // var_dump($sales_items);

        $company_id = $_SESSION['company_id'];
        $Company = $this->M_companies->get_companies($company_id);
        $customer =  @$this->M_customers->get_customers(@$sales_items[0]['customer_id']);

        
        $this->load->library('Pdf_f');
        $pdf = new Pdf_f("P", 'mm', 'A4');

        $pdf->AddPage();
        //Display Company Info
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(50, 10, $Company[0]['name'], 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 7, $Company[0]['address'], 0, 1);
        //$pdf->Cell(50, 7, "Salem 636002.", 0, 1);
        $pdf->Cell(50, 7, "PH : ".$Company[0]['contact_no'], 0, 1);

        //Display INVOICE text
        $pdf->SetY(15);
        $pdf->SetX(-40);
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->Cell(50, 10, "INVOICE", 0, 1);

        //Display Horizontal line
        $pdf->Line(0, 42, 210, 42);

        //Billing Details // Body
        $pdf->SetY(49);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(50, 10, "Bill To: ", 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 7, $customer[0]["first_name"], 0, 1);
        $pdf->Cell(50, 7, $customer[0]["address"], 0, 1);
        //$pdf->Cell(50, 7, $customer[0]["city"], 0, 1);

        //Display Invoice no
        $pdf->SetY(49);
        $pdf->SetX(-60);
        $pdf->Cell(50, 7, "Invoice No : " . $new_invoice_no);

        //Display Invoice date
        $pdf->SetY(57);
        $pdf->SetX(-60);
        $pdf->Cell(50, 7, "Invoice Date : " . date('m-d-Y',strtotime($sales_items[0]["sale_date"])));

        //Display Table headings
        $pdf->SetY(85);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(80, 9, "DESCRIPTION", 1, 0);
        $pdf->Cell(40, 9, "PRICE", 1, 0, "C");
        $pdf->Cell(30, 9, "QTY", 1, 0, "C");
        $pdf->Cell(40, 9, "TOTAL", 1, 1, "C");
        $pdf->SetFont('Arial', '', 12);
        
        $discount = 0;
        $total_cost = 0;
        $total = 0;
        //Display table product rows
        foreach ($sales_items as $row) {
            $total_cost = ($row['item_unit_price'] * $row['quantity_sold']) - $row['discount_value'];
            $total += ($row['item_unit_price'] * $row['quantity_sold']);
            $discount += $row['discount_value'];
            $tax_amount = $total_cost * $row['tax_rate'] / 100;
            $account_name = $this->M_groups->get_accountName($row['account_code']);

            $pdf->Cell(80, 9, $account_name, "LR", 0);
            $pdf->Cell(40, 9, number_format($row["item_unit_price"],2), "R", 0, "R");
            $pdf->Cell(30, 9, number_format($row["quantity_sold"],2), "R", 0, "C");
            $pdf->Cell(40, 9, number_format(($row['item_unit_price'] * $row['quantity_sold']),2), "R", 1, "R");
        }
        //Display table empty rows
        for ($i = 0; $i < 12 - count($sales_items); $i++) {
            $pdf->Cell(80, 9, "", "LR", 0);
            $pdf->Cell(40, 9, "", "R", 0, "R");
            $pdf->Cell(30, 9, "", "R", 0, "C");
            $pdf->Cell(40, 9, "", "R", 1, "R");
        }
        //Display table total row
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(150, 9, "TOTAL", 1, 0, "R");
        $pdf->Cell(40, 9, number_format($total,2), 1, 1, "R");

        //Display amount in words
        $pdf->SetY(215);
        $pdf->SetX(10);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 9, "Amount in Words ", 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 9, number_format($total,2), 0, 1);
        ///////////////
        ///body

        //set footer position
        $pdf->SetY(-60);
        //$pdf->SetFont('helvetica', 'B', 12);
        //$pdf->Cell(0, 10, "for ABC COMPUTERS", 0, 1, "R");
        $pdf->Ln(15);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, "Authorized Signature", 0, 1, "R");
        $pdf->SetFont('helvetica', '', 10);

        //Display Footer Text
        $pdf->Cell(0, 10, "This is a computer generated invoice", 0, 1, "C");
        ///////////////

        $pdf->Output();
    }
}
