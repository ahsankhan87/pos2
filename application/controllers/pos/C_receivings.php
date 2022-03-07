<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_receivings extends MY_Controller{
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function index()
    {
        //$this->output->enable_profiler();
        
        $data['title'] = 'Purchases';
        $data['main'] = 'Purchases';
        
        //when click on sale manu clear the cart first if exist
       // $this->destroyCart();
        
        $data['itemDDL'] = $this->M_items->getItemDropDown();
        $data['colorsDDL'] = $this->M_colors->get_activeColorsDDL();
        $data['sizesDDL'] = $this->M_sizes->get_activeSizesDDL();
        $data['supplierDDL'] = $this->M_groups->get_ledgersByGroupName('AP');//search for legder account
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/receivings/v_purchases',$data);
        $this->load->view('templates/footer');
    }
    
    //purchase the projuct angularjs
    public function purchaseProducts()
    {
        //INITIALIZE
        $total_amount =0;
        $discount =0;
        $amount=0;
        
        // get posted data from angularjs purchases 
        $data_posted = json_decode(file_get_contents("php://input",true)); 
        //print_r($data_posted);
        
        if(count($data_posted) > 0)
        { 
         $data = array(
            'company_id'=> $_SESSION['company_id'],
            'supplier_id' => $data_posted->supplier_ledger_id,
            'supplier_invoice_no' => $data_posted->supplier_invoice_no,
            'employee_id'=>0,
            'receiving_date' => date('Y-m-d'),
            'register_mode'=>$data_posted->register_mode,
            'account'=>$data_posted->purchaseType,
            'amount_due'=>$data_posted->amount_due,
            'description'=>($data_posted->description == '' ? ' ' : $data_posted->description)
            );
            
        $this->db->insert('pos_receivings', $data);
        
        $receiving_id = $this->db->insert_id();
        
        //extract JSON array items from posted data.
        foreach($data_posted->items as $posted_values):
        
        //$posted_exp_date = strftime('%Y-%m-%d', strtotime($posted_values->expiry_date));//get only date remove time
            
            //insert in receivings items
            $data = array(
                'receiving_id'=>$receiving_id,
                'item_id'=>$posted_values->item_id,
                'quantity_purchased'=>$posted_values->quantity,
                'item_cost_price'=>$posted_values->cost_price,
                'item_unit_price'=>$posted_values->unit_price,
                'discount_percent'=>$posted_values->discount,
                'color_id'=>$posted_values->color_id,
                'size_id'=>$posted_values->size_id
                );
                
            $this->db->insert('pos_receivings_items', $data);
            // receiving itmes
            
            
            //insert items details
            //if items already exist according to expiry then update qty.
             
            if($this->M_items->checkItemOptions($posted_values->item_id,$posted_values->color_id,$posted_values->size_id))
            {
                $total_stock =  $this->M_items->total_stock($posted_values->item_id,$posted_values->color_id,$posted_values->size_id);
                        
                        //if products is to be return then it will subtract from qty and the avg cost will be reverse to original cost
                        if($data_posted->register_mode == 'return')
                         {
                            $quantity = ($total_stock - $posted_values->quantity);
                         }else { 
                            $quantity=($total_stock + $posted_values->quantity); 
                            }
                 
                $option_data = array(
                'quantity'=>$quantity,
                'unit_price' =>$posted_values->unit_price,
                'avg_cost'=>$this->M_items->getAvgCost($posted_values->item_id,$posted_values->cost_price,$posted_values->quantity,$posted_values->color_id,$posted_values->size_id,$data_posted->register_mode)//calculate avg cost
                 
                );
              $this->db->update('pos_items_detail',$option_data,array('color_id'=>$posted_values->color_id,'size_id'=>$posted_values->size_id,'item_id'=>$posted_values->item_id));
         
            }
            else //else insert new item details
            {
                $option_data = array(
                'item_id'=>$posted_values->item_id,
                'quantity'=>$posted_values->quantity,
                'color_id' =>$posted_values->color_id,
                'size_id' =>$posted_values->size_id,
                'cost_price' =>$posted_values->cost_price, //actually this price is NEW cost price
                'unit_price' =>$posted_values->unit_price,
                'avg_cost'=>$this->M_items->getAvgCost($posted_values->item_id,$posted_values->cost_price,$posted_values->quantity,$posted_values->color_id,$posted_values->size_id,$data_posted->register_mode)//calculate avg cost
                );
              $this->db->insert('pos_items_detail', $option_data);
            }
             
            //item details
            
            //insert item info into inventory table
            $data1= array(
                
                'trans_item'=>$posted_values->item_id,
                'trans_comment'=>'KSRECV',
                'trans_inventory' =>$posted_values->quantity,
                'company_id'=> $_SESSION['company_id']
                
                );
                
            $this->db->insert('pos_inventory', $data1);
            //insert item info into inventory table  
            
            $amount += ($posted_values->quantity * $posted_values->cost_price);
         $discount += ($posted_values->quantity * $posted_values->cost_price)*$posted_values->discount/100;
              
        endforeach;
        
        //total net amount 
        $total_amount =  (($amount-$discount)-$data_posted->amount_due);
        
        /////////////////////////////////
        ////   ACCOUNT TRANSACTIONS  /////
        /////////////////////////////////
        // inventory DEBIT AND
        // CASH CREDITED
        if($data_posted->purchaseType == 'cash' && $data_posted->register_mode == 'receive')
            {
                //Search for inventory and cash ledger account for account entry
                //if invoice is cash then entry will be purchase debit and cash credit and vice versa
                $data['inventory'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for inventory and cash
                foreach($data['inventory'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'inventory')
                    {
                        $dr_ledger_id = $ledger_value['id'];
                    }    
                    else if(strtolower($ledger_value['name']) == 'cash')
                    {
                        $cr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
                
                
                //////////////
                // AMOUNT DUE//
                if($data_posted->amount_due > 0)
                {
                    $this->M_entries->addEntries($dr_ledger_id,$data_posted->supplier_ledger_id,$data_posted->amount_due,$data_posted->amount_due,'Due Amount on puchases',$receiving_id);
                }
                ////////////
                //END AMOUNT DUE
            }
            
        //inventory DEBITED AND 
        //ACOUNT PAYABLE SUPPLIER ID IS CREDITED
         elseif($data_posted->purchaseType == 'credit' && $data_posted->register_mode == 'receive')
         {
            //Search for inventory and cash ledger account for account entry
                //if invoice is cash then entry will be purchase debit and cash credit and vice versa
                $data['inventory'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for inventory and cash
                foreach($data['inventory'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'inventory')
                    {
                        $dr_ledger_id = $ledger_value['id'];
                    }    
                    
                endforeach;
                
                $cr_ledger_id = $data_posted->supplier_ledger_id;
         }
         //PURCHASE RETURN CREDITED AND
         elseif($data_posted->purchaseType == 'cash' && $data_posted->register_mode == 'return')
         {
            //Search for purchases returns and discount ledger account for account entry
            //if invoice is cash then entry will be purchase debit and cash credit and vice versa
            $data['purchase_return'] = $this->M_groups->get_ledgersByGroupName('cos');//search for purchases returne
            foreach($data['purchase_return'] as $ledger_value):
                if(strtolower($ledger_value['name']) == 'purchase-returns')
                {
                    $cr_ledger_id = $ledger_value['id'];
                }    
                
            endforeach;
          //CASH DEBITED
           $data['cash'] = $this->M_groups->get_ledgersByGroupName('current_assets');//search for purchases and cash
                foreach($data['cash'] as $ledger_value):
                    if(strtolower($ledger_value['name']) == 'cash')
                    {
                        $dr_ledger_id = $ledger_value['id'];
                    }
                endforeach;
         }
         ////PURCHASE RETURN CREDITED AND
         elseif($data_posted->purchaseType == 'credit' && $data_posted->register_mode == 'return')
         {
            //Search for purchases and cash ledger account for account entry
            //if invoice is cash then entry will be purchase debit and cash credit and vice versa
            $data['purchase_return'] = $this->M_groups->get_ledgersByGroupName('cos');//search for purchases returne
            foreach($data['purchase_return'] as $ledger_value):
                if(strtolower($ledger_value['name']) == 'purchase-returns')
                {
                    $cr_ledger_id = $ledger_value['id'];
                }    
                
            endforeach;
            
            //SUPPLIER IS DEBITED
             $dr_ledger_id = $data_posted->supplier_ledger_id;
         }
         
         
                    //IF DISCOUNT RECEIVED FROM SUPPLIER
                    // PURCHASE DICOUNT CREDITED AND PURCHASES DEBITED
                    if($data_posted->register_mode == 'receive')
                    {
                        if($discount != 0)
                        {
                            $data['purchase_discount'] = $this->M_groups->get_ledgersByGroupName('cos');//search for sales ledger
                            foreach($data['purchase_discount'] as $ledger_value):
                                if(strtolower($ledger_value['name']) == 'purchase-discount')
                                {
                                    $cr_ledger_discount_id = $ledger_value['id'];
                                } 
                            
                            endforeach;
                            //journal entries 
                            // PURCHASE DICOUNT CREDITED AND PURCHASES DEBITED
                            $this->M_entries->addEntries($dr_ledger_id,$cr_ledger_discount_id,$discount,$discount,'Discount on puchases',$receiving_id);
                        }
                    }
                    elseif($data_posted->register_mode == 'return')
                    {
                        if($discount != 0)
                        {
                            $data['purchase_discount'] = $this->M_groups->get_ledgersByGroupName('cos');//search for sales ledger
                            foreach($data['purchase_discount'] as $ledger_value):
                                if(strtolower($ledger_value['name']) == 'purchase-discount')
                                {
                                    $dr_ledger_discount_id = $ledger_value['id'];
                                } 
                            
                            endforeach;
                            //journal entries 
                            // PURCHASE DICOUNT DEBITED AND PURCHASES OR AC/PAYABLE CREDITED
                            $this->M_entries->addEntries($dr_ledger_discount_id,$cr_ledger_id,$discount,$discount,'Discount on puchases',$receiving_id);
                        }
                    }
                    
                //////
               $dr_amount = $total_amount;
               $cr_amount = $total_amount;
               $narration = $data_posted->register_mode . ' '. $data_posted->purchaseType . ' ' . $data_posted->description;
               
               $dr_ledger = $dr_ledger_id;
               $cr_ledger = $cr_ledger_id;
                
                
               $this->M_entries->addEntries($dr_ledger,$cr_ledger,$dr_amount,$cr_amount,ucwords($narration),$receiving_id);
                
               
               echo '{"receiving_id":"'.$receiving_id.'"}';//redirect to receipt page using this $receiving_id 
                 
         ////////////////////////////
         //      ACCOUNTS CLOSED ..///
         
        }//end if
        else{
            echo 'No Data';
        }
    }
    
    public function receipt($receiving_id)
    {
        $data['title'] = 'Purchase Invoice';
        $data['main'] = 'Invoice';
        $data['invoice_no'] = $receiving_id;
        
        $data['receivings_items'] = $this->M_receivings->get_receiving_items($receiving_id);
       
        $company_id = $_SESSION['company_id'];
        $data['Company'] = $this->M_companies->get_companies($company_id);
            
        $this->load->view('templates/header',$data);
        $this->load->view('pos/receivings/v_receipt',$data);
        $this->load->view('templates/footer');
    }
    
    public function purchase()
    {
        //$this->output->enable_profiler();
        
        $data['title'] = 'Purchases';
        $data['main'] = 'Purchases';
        
        //$data['supplierDDL'] = $this->M_groups->get_ledgersByGroupName('AP');//search for legder account
        
        
        $data['itemDDL'] = $this->M_items->getItemDropDown();
        $data['colorsDDL'] = $this->M_colors->get_activeColorsDDL();
        $data['sizesDDL'] = $this->M_sizes->get_activeSizesDDL();
        $data['supplierDDL'] = $this->M_groups->get_ledgersByGroupName('AP');//search for legder account
        
      
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/receivings/v_purchases',$data);
        $this->load->view('templates/footer');
    }
    
    public function allPurchases()
    {
        $data['title'] = 'Purchases';
        $data['main'] = 'Purchases';
        
        $data['receivings'] = $this->M_receivings->get_receivings();
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/receivings/v_allpurchases',$data);
        $this->load->view('templates/footer');
    }
    
    
    
    
    
    
    
    public function receivings()
    {
        if($this->input->post('rowid'))
        {
            $this->M_receivings->receivings();
        
        
        $this->session->set_flashdata('success_msg','The Items has sucessfully Received!');
        $this->destroyCart();
       // redirect('pos/C_receivings/index','refresh');
        
        }
        else
        {
            $this->session->set_flashdata('error','There is no item!');
        
            redirect('pos/C_receivings/index','refresh');
        }
        
    }
    //add items to the cart.
    public function addCart(){
        
        $item_id = $this->input->post('item_id',true);
        $quantity = $this->input->post('quantity',true);//new_qty
        $discount_percent = $this->input->post('discount_percent',true);
        $size = $this->input->post('sizes',true);
        $color =  $this->input->post('colors',true);
        
        if($item_id && $quantity)
        {
            $total_stock =  $this->M_items->total_stock($item_id);
            
            $items  = $this->M_items->get_activeItems($item_id);
            foreach($items as $item):
                    $data = array(
                        'id' => $item['item_id'],
    		          	'qty' => $quantity, //new_qty
    		          	'price' => $item['avg_cost'],
                        'unit_price'=> $item['unit_price'],
                        'item_qty'=> $total_stock, // its for pos_items table (add and minus qty) purpose
    		           	'name' => $item['name'],
                        'discount_percent'=>$discount_percent,
                        'options' => array('size'=>$size,'color'=>$color)
    		              );    
            endforeach;
            $this->cart->insert($data);
            $this->session->set_flashdata('success_msg','The Item has sucessfully added.!');
        }
        else
        {
            $this->session->set_flashdata('error','Please select item and quantity');
        }
        redirect('pos/C_receivings/index','refresh');
        
    }
    
    // Updated the shopping cart
    function update_cart(){
         
        // Retrieve the posted information
        $item = $this->input->post('rowid');
        $qty = $this->input->post('qty');
        $cost_price = $this->input->post('cost_price');
        
     
        // Cycle true all items and update them
        for($i=0;$i < count($item);$i++)
        {
            // Create an array with the products rowid's and quantities.
            $data = array(
               'rowid' => $item[$i],
               'qty'   => $qty[$i],
               'price' => $cost_price[$i]
              // 'unit_price'=>$unit_price[$i]
            );
             
            
           $this->cart->update($data);
        }
        
        //Update the cart with the new information
         
        $this->session->set_flashdata('success_msg','The cart is updated!');
        redirect('pos/C_receivings/index','refresh');
     
    }

//destroy the cart
    public function destroyCart(){
        
        $this->cart->destroy();
        redirect('pos/C_receivings/index','refresh');
    }
    
    //remove one item from cart
    public function removeCart($rowId){
        $data = array(
                'rowid' => $rowId,
                'qty'   => 0
            );
        $this->cart->update($data);
        redirect('pos/C_receivings/index','refresh');
    }
    
    
    public function checkout(){
        
        $data['title'] = 'Receivings Check out';
        $data['main'] = 'Receivings Checkout';
        
        $this->load->view('templates/header',$data);
        $this->load->view('pos/receivings/checkout',$data);
        $this->load->view('templates/footer');
    }
    
}