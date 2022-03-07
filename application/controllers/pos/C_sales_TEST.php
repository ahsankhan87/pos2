<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_sales extends MY_Controller{
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function index()
    {
        $data['title'] = 'Sales';
        $data['main'] = 'Sales';
        
        //when click on sale manu clear the cart first if exist
        //$this->cart->destroy();
        
        $data['itemDDL'] = $this->M_items->getItemDropDown();
        //$data['customersDDL'] = $this->M_customers->getCustomerDropDown();
        $data['customersDDL'] = $this->M_groups->get_ledgersByGroupName('AR');//search for legder account
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/sales/v_salesProduct',$data);
        $this->load->view('templates/footer');
    }
    
    //sale the projuct angularjs
    public function saleProducts()
    {
        $total_amount =0;
        $total_cost_amount=0;
        $discount =0;
        $amount=0;
        $cost_price=0;
        
        // get posted data
        $data_posted = json_decode(file_get_contents("php://input",true)); 
       // print_r($data_posted);
       if(count($data_posted) > 0)
        {
            
       $data = array(
            'company_id'=> $_SESSION['company_id'],
            'customer_id' => $data_posted->customer_ledger_id,
            'employee_id'=>0,
            'sale_date' =>date("Y-m-d"),
            'register_mode'=>$data_posted->register_mode,
            'account'=>$data_posted->saleType,
            'amount_due'=>$data_posted->amount_due,
            'description'=>($data_posted->description == '' ? ' ' : $data_posted->description)
            );
            
        $this->db->insert('pos_sales', $data);
        
        $sale_id = $this->db->insert_id();
        
        //extract JSON array items from posted data.
        foreach($data_posted->items as $posted_values):
        $data = array(
            'sale_id' => $sale_id,
            'item_id'=>$posted_values->item_id,
            'description'=>'',
            'quantity_sold'=>$posted_values->quantity,
            'item_cost_price'=>$posted_values->cost_price,//actually its avg cost comming from sale from
            'item_unit_price'=>$posted_values->unit_price,
            'discount_percent'=>$posted_values->discount,
            'color_id'=>$posted_values->color_id,
            'size_id'=>$posted_values->size_id
            );
            
        $this->db->insert('pos_sales_items', $data);
        
        if($this->M_items->checkItemOptions($posted_values->item_id,$posted_values->color_id,$posted_values->size_id))
        {
            $total_stock =  $this->M_items->total_stock($posted_values->item_id,$posted_values->color_id,$posted_values->size_id);
                    
                    //if products is to be return then it will add from qty and the avg cost will be reverse to original cost
                    if($data_posted->register_mode == 'return')
                     {
                        $quantity = ($total_stock + $posted_values->quantity);
                     }else { 
                        $quantity=($total_stock - $posted_values->quantity); 
                        }
             
            $option_data = array(
            'quantity'=>$quantity
            );
          $this->db->update('pos_items_detail',$option_data,array('size_id'=>$posted_values->size_id,'item_id'=>$posted_values->item_id));
     
        }
        
         //ADD ITEM DETAIL IN INVENTORY TABLE    
          $data1= array(
            'trans_item'=>$posted_values->item_id,
            'trans_comment'=>'KSPOS',
            'trans_inventory' => -$posted_values->quantity,
            'company_id'=> $_SESSION['company_id']
            );
            
          $this->db->insert('pos_inventory', $data1);
          //////////////
         
         $cost_price += ($posted_values->quantity * $posted_values->cost_price);
         $amount += ($posted_values->quantity * $posted_values->unit_price);
         $discount += ($posted_values->quantity * $posted_values->unit_price)*$posted_values->discount/100;
              
        endforeach;
        
        //total net amount 
        $total_amount =  (($amount-$discount)-$data_posted->amount_due);
        
        //Total Cost amount
        $total_cost_amount =  (($cost_price-$discount)-$data_posted->amount_due);
        
        //////////////////////////////////
        ////   ACCOUNT TRANSACTIONS  /////
        /////////////////////////////////
        //  Cash Debit and Sales Credit
        if($data_posted->saleType == 'cash' && $data_posted->register_mode == 'sale')
            {
                //Search for sales and cash ledger account for account entry
                //if invoice is cash then entry will be cash debit and sales credit and vice versa
                $data['cash'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for cash ledger
                foreach($data['cash'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'cash')
                    {
                        $dr_ledger_id = $ledger_value['id'];
                    }
                   
                endforeach;
                
                //THIS IS FOR SALES i.e Sales is Credit.
               $data['sales'] = $this->M_groups->get_ledgersByGroupName('revenue');//search for sales ledger
               foreach($data['sales'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'sales')
                    {
                        $cr_ledger_id = $ledger_value['id'];
                    }
                   
                endforeach;
                
                ////////////////
                //INVENTORY WILL BE DEDUCTED(CREDITED) AND COST OF SALE WILL BE DEBITED
                $data['inventory'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for inventory and cash
                foreach($data['inventory'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'inventory')
                    {
                        $inventory_cr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                
                $data['cos'] = $this->M_groups->get_ledgersByGroupName('cos');//search for inventory and cash
                foreach($data['cos'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'purchases')//cos = cost of sales
                    {
                        $inventory_dr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                /////////////////
                
                //////////////
                // AMOUNT DUE//
                if($data_posted->amount_due > 0)
                {
                    $this->M_entries->addEntries($data_posted->customer_ledger_id,$cr_ledger_id,$data_posted->amount_due,$data_posted->amount_due,'Due Amount on Sales',$sale_id);
                }
                ////////////
                //END AMOUNT DUE
                    
            }
            
            //if Sales is on credit 
            //  AR - Customer Debit and Sales Credit
         elseif($data_posted->saleType == 'credit' && $data_posted->register_mode == 'sale')
         {
                //Search for purchases and cash ledger account for account entry
                //if invoice is cash then entry will be purchase debit and cash credit and vice versa
                $data['sales'] = $this->M_groups->get_ledgersByGroupName('revenue');//search for sales ledger
               foreach($data['sales'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'sales')
                    {
                        $cr_ledger_id = $ledger_value['id'];
                    }    
                   
                endforeach;
                
                $dr_ledger_id = $data_posted->customer_ledger_id;
                
                ////////////////
                //INVENTORY WILL BE DEDUCTED(CREDITED) AND COST OF SALE WILL BE DEBITED
                $data['inventory'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for inventory and cash
                foreach($data['inventory'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'inventory')
                    {
                        $inventory_cr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                
                $data['cos'] = $this->M_groups->get_ledgersByGroupName('cos');//search for inventory and cash
                foreach($data['cos'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'purchases')//cos = cost of sales
                    {
                        $inventory_dr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                /////////////////              
                
         }
         //SALES RETURN DEBITED AND
         elseif($data_posted->saleType == 'cash' && $data_posted->register_mode == 'return')
         {
            //Search for sales return and cash ledger account for account entry
            //if invoice is cash then entry will be sales return debit and cash credit and vice versa
            $data['sales_return'] = $this->M_groups->get_ledgersByGroupName('revenue');//search for purchases returne
            foreach($data['sales_return'] as $ledger_value):
                if(strtolower($ledger_value['name']) == 'sales-returns')
                {
                    $dr_ledger_id = $ledger_value['id'];
                }    
                
            endforeach;
            
          //CASH CREDITED
           $data['cash'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for cash
                foreach($data['cash'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'cash')
                    {
                        $cr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                
                ////////////////
                //INVENTORY WILL BE DEDUCTED(CREDITED) AND COST OF SALE WILL BE DEBITED
                $data['inventory'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for inventory and cash
                foreach($data['inventory'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'inventory')
                    {
                        $inventory_dr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                
                $data['cos'] = $this->M_groups->get_ledgersByGroupName('cos');//search for inventory and cash
                foreach($data['cos'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'purchases')//cos = cost of sales
                    {
                        $inventory_cr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                /////////////////          
         }
         ////SALES RETURN DEBITED AND
         elseif($data_posted->saleType == 'credit' && $data_posted->register_mode == 'return')
         {
            //Search for sales return and cash ledger account for account entry
            //if invoice is cash then entry will be sales return debit and cash credit and vice versa
            $data['sales_return'] = $this->M_groups->get_ledgersByGroupName('revenue');//search for sales returne
            foreach($data['sales_return'] as $ledger_value):
                if(strtolower($ledger_value['name']) == 'sales-returns')
                {
                    $dr_ledger_id = $ledger_value['id'];
                }    
                
            endforeach;
            
            //CUSTOMER IS CREDITED
             $cr_ledger_id = $data_posted->customer_ledger_id;
             
                ////////////////
                //INVENTORY WILL BE DEDUCTED(CREDITED) AND COST OF SALE WILL BE DEBITED
                $data['inventory'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for inventory and cash
                foreach($data['inventory'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'inventory')
                    {
                        $inventory_dr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                
                $data['cos'] = $this->M_groups->get_ledgersByGroupName('cos');//search for inventory and cash
                foreach($data['cos'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'purchases')//cos = cost of sales
                    {
                        $inventory_cr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                /////////////////          
         }
         
            
                    //IF DISCOUNT PAID
                    // SALES DICOUNT DEBIT AND SALES CREDIT
                    if($data_posted->register_mode == 'sale')
                    {
                        if($discount != 0)
                        {
                        $data['sales_discount'] = $this->M_groups->get_ledgersByGroupName('revenue');//search for sales ledger
                        foreach($data['sales_discount'] as $ledger_value):
                            if(strtolower($ledger_value['name']) == 'sales-discount')
                            {
                                $dr_ledger_discount_id = $ledger_value['id'];
                            } 
                        
                        endforeach;
                        //journal entries 
                        // SALES DICOUNT DEBIT AND SALES CREDIT
                        $this->M_entries->addEntries($dr_ledger_discount_id,$cr_ledger_id,$discount,$discount,'Discount on Sales',$sale_id);
                        }
                    }
                    elseif($data_posted->register_mode == 'return')
                    {
                        if($discount != 0)
                        {
                        $data['sales_discount'] = $this->M_groups->get_ledgersByGroupName('revenue');//search for sales ledger
                        foreach($data['sales_discount'] as $ledger_value):
                            if(strtolower($ledger_value['name']) == 'sales-discount')
                            {
                                $cr_ledger_discount_id = $ledger_value['id'];
                            } 
                        
                        endforeach;
                        //journal entries 
                        // SALES DICOUNT CREDIT AND SALES OR A/C RECEIVABLE DEBITED
                        $this->M_entries->addEntries($dr_ledger_id,$cr_ledger_discount_id,$discount,$discount,'Discount on Sales',$sale_id);
                        }
                    }
                    
                    
                //////
               $dr_amount = $total_amount;
               $cr_amount = $total_amount;
               $narration = $data_posted->register_mode . ' '. $data_posted->saleType . ' '.$data_posted->description;
               
               $dr_ledger = $dr_ledger_id;
               $cr_ledger = $cr_ledger_id;
               
               //journal entries 
               $this->M_entries->addEntries($dr_ledger,$cr_ledger,$dr_amount,$cr_amount,ucwords($narration),$sale_id);
               
               //////INVENTORY ENTRIES
               $inventory_dr_amount = $total_cost_amount;
               $inventory_cr_amount = $total_cost_amount;
               $narration = $data_posted->register_mode . ' '. $data_posted->saleType . ' '.$data_posted->description;
               
               $inventory_dr_ledger_id = $inventory_dr_ledger_id;
               $inventory_cr_ledger_id = $inventory_cr_ledger_id;
               
               //journal entries 
               $this->M_entries->addEntries($inventory_dr_ledger_id,$inventory_cr_ledger_id,$inventory_dr_amount,$inventory_cr_amount,ucwords($narration),$sale_id);
               /////
               echo '{"sale_id":"'.$sale_id.'"}'; //redirect to receipt page using this $receiving_id
         
         /////////////////////////////
         //      ACCOUNTS CLOSED ..///
         /////////////////////////////
         
        }// end if 
        else{
            echo 'No Data';
        }
    }
    
    
    public function receipt($sales_id)
    {
        $data['title'] = 'Sales Invoice';
        $data['main'] = 'Invoice';
        $data['invoice_no'] = $sales_id;
        
        $data['sales_items'] = $this->M_sales->get_sales_items($sales_id);
       
        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);
            
        $this->load->view('templates/header',$data);
        $this->load->view('pos/sales/v_receipt',$data);
        $this->load->view('templates/footer');
    }
    
    
    
    
    public function sales()
    {
        if($this->input->post('rowid'))
        {
            $this->M_sales->sales();
        
        
        $this->session->set_flashdata('success_msg','The Item has sucessfully Sold!');
        $this->destroyCart();
        redirect('pos/C_sales/index','refresh');
        
        }
        else
        {
            $this->session->set_flashdata('error','There is no Item!');
       
            redirect('pos/C_sales/index','refresh');
        }
    }
    
   
    
    //build dropdown list options for sales form
    public function buildItemOptionsDDL()
    {
        $item_id=$this->input->post('item_id',true);
        
        $item_options = $this->M_items->get_optionsDDL($item_id);
        
        
        $output = null;
        //$output .= '<option value="0" selected="selected">Please Select</option>';
        foreach($item_options as $values)
        {
            if($values['color_id'] == 0 AND $values['size_id'] == 0)//if color and size doen not have then show only qty.
            {
                $output .= '<option value="'.$values['id'].'">'.$values['quantity'] .'</option>';
            
            }
            else // else show all
            {
                $color = $this->M_colors->get_color($values['color_id']);
                $size = $this->M_sizes->get_size($values['size_id']);
                $output .= '<option value="'.$values['id'].'">'.$color[0]['name'].', '.$size[0]['name'] . ', '.$values['quantity'] .'</option>';
            
            }
            
        }
        
        echo $output;
    }
    
    public function buildItemSalePrice()
    {
        $item_id=$this->input->post('item_id',true);
        
        $items = $this->M_items->get_items($item_id);
        
        echo $items[0]['unit_price'];
        
    }
    
    
    //add items to the cart.
    public function addCart(){
        
        $item_id = $this->input->post('item_id',true);
        $item_option_id = $this->input->post('item_options',true);
        $discount_percent = $this->input->post('discount_percent',true);
        $sale_price = $this->input->post('sale_price',true);
        
        if($item_id && $item_option_id)
        {
           $total_stock =  $this->M_items->total_stock($item_id);
            
            $data['options'] = $this->M_items->get_itemOptions($item_option_id);
            
            $items  = $this->M_items->get_activeItems($item_id);
            foreach($items as $item):
            $data = array(
                        'id' => $item['item_id'],
    		          	'qty' => 1,
    		          	'price' => $sale_price,
                        'cost_price'=> $item['cost_price'],
                        'item_qty'=> $total_stock, // its for item table (add and minus) purpose
    		           	'name' => $item['name'],
                        'discount_percent'=>$discount_percent,
                        'options' => $data['options']
    		              );    
            endforeach;
            $this->cart->insert($data);
            $this->session->set_flashdata('success_msg','The Item has sucessfully added.!');
        }
        else
        {
            $this->session->set_flashdata('error','Please select item and quantity OR purchase product first then sale it');
        }
        redirect('pos/C_sales/index','refresh');
    }
    
    // Updated the shopping cart
    function update_cart(){
         
          // Retrieve the posted information
        $item = $this->input->post('rowid');
        $qty = $this->input->post('qty');
        $price = $this->input->post('price');
        
     
        // Cycle true all items and update them
        for($i=0;$i < count($item);$i++)
        {
            // Create an array with the products rowid's and quantities.
            $data = array(
               'rowid' => $item[$i],
               'qty'   => $qty[$i],
               'price' => $price[$i]
            );
             
           $this->cart->update($data);
        }
        
        // Update the cart with the new information
         
        $this->session->set_flashdata('success_msg','The Item has sucessfully updated!');
        redirect('pos/C_sales/index','refresh');
     
    }

//destroy the cart
    public function destroyCart(){
        
        $this->cart->destroy();
        redirect('pos/C_sales/index','refresh');
    }
    
    //remove one item from cart
    public function removeCart($rowId){
        $data = array(
                'rowid' => $rowId,
                'qty'   => 0
            );
        $this->cart->update($data);
        redirect('pos/C_sales/index','refresh');
    }
    
    
    public function checkout(){
        
        $data['title'] = 'Sales Check out';
        $data['main'] = 'Sales Checkout';
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/sales/checkout',$data);
        $this->load->view('templates/footer');
    }
    
}