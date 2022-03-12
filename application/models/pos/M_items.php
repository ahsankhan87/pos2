<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_items extends CI_Model{
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    //get all products and also only one product and active and inactive too.
    public function get_items($id = FALSE, $limit = 1000000, $offset = 0)
    {
        if($id === FALSE)
        {
            if($limit)
            {
                $this->db->limit($limit);
                $this->db->offset($offset);
            }
            
            //$this->db->order_by('id','asc');
            $option = array('deleted'=>0,'company_id'=> $_SESSION['company_id']);
            $query = $this->db->get_where('pos_items_detail',$option);
            $data = $query->result_array();
            return $data;
        }
        
        //$this->db->order_by('id','asc');
        $options = array('id'=> $id,'company_id'=> $_SESSION['company_id']);
        
        $query = $this->db->get_where('pos_items_detail',$options);
        $data = $query->result_array();
        return $data;
    }
    
    //get all products and also only one product and active and inactive too.
    public function get_low_stock_items()
    {
        
        $this->db->select('A.id AS item_id,A.service,B.category_id, B.name,B.inventory_acc_code,B.barcode,B.size_id,B.unit_id,
        B.quantity,B.cost_price, B.avg_cost,B.unit_price,B.re_stock_level');
        
        $this->db->where('B.quantity <= B.re_stock_level');
        
        $query = $this->db->get_where('pos_items_detail AS B', array('B.deleted'=>0,'B.company_id'=> $_SESSION['company_id']));
        $data = $query->result_array();
        return $data;
    }
    
    function getItemDropDown($item_type=null)//item type = service or purchased
    {
        $data = array();;
        $data[0] = "--Please Select--";
        //$query = $this->db->get_where('pos_items',array('company_id'=> $_SESSION['company_id'],'deleted'=>0));
        if($item_type != null)
        {
            $this->db->where('A.item_type',$item_type);
        }
       
        $query = $this->db->get_where('pos_items_detail AS A', array('A.deleted'=>0,
        'A.company_id'=> $_SESSION['company_id']));
        
        if ($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row)
            {
                // $category = $this->M_category->get_CatNameByItem($row['id']);//get the category of single item
                // $data[$row['id']] = $row['name'] . ($category == '' ? '' : ' - '.$category);//and if category is not empty it will add it with item i.e used in purchases and sales product DDL
                $data[$row['id']] = $row['name'];//and if category is not empty it will add it with item i.e used in purchases and sales product DDL
            }
        }
        $query->free_result();
        return $data;
    }
        
    //get active property if id is provided then one record will be loaded.
    //this property will show on front page.
    public function get_activeItems($item_id = FALSE)
    {
        if($item_id === FALSE)
        {
            $query = $this->db->get_where('pos_items_detail', array('deleted'=>0,'company_id'=> $_SESSION['company_id']));
            $data = $query->result_array();
            return $data;
        }
        
        $this->db->order_by('id','desc');
        $options = array('id'=> $item_id, 'deleted'=>0,'company_id'=> $_SESSION['company_id']);
        $query = $this->db->get_where('pos_items_detail',$options);
        $data = $query->result_array();
        return $data;
    }
    
    public function get_allItemsforJSON()
    {
        //$this->db->order_by('B.item_id','desc');
        $this->db->select('A.id AS item_id,A.category_id,A.name,A.barcode,A.inventory_acc_code,A.picture,
        A.item_type,A.unit_id,A.quantity,A.avg_cost,A.cost_price,A.unit_price,A.re_stock_level,
        A.inventory_acc_code,A.wip_acc_code,A.tax_id,
        U.name as unit_name');
        // $this->db->join('pos_items AS B','A.item_id = B.item_id','left');
        // $this->db->join('pos_taxes AS T','T.id = A.tax_id','left');
        // $this->db->join('pos_sizes as C','A.size_id = C.id','left');
        $this->db->join('pos_units as U','A.unit_id = U.id','left');
        // $this->db->join('pos_locations as L','A.location_code = L.code','left');
        
        $query = $this->db->get_where('pos_items_detail AS A', array('A.deleted'=>0,'A.company_id'=> $_SESSION['company_id']));
        $data = $query->result_array();
        return $data;
    
    }
    
    //get item and item detail for purchase
    public function get_allItems()
    {
        //$this->db->order_by('A.item_id','desc');
        $this->db->select('B.id AS item_id,B.category_id, B.name,B.inventory_acc_code,B.barcode,B.size_id,B.unit_id,
        B.quantity,B.cost_price, B.avg_cost,B.unit_price,B.re_stock_level,
        B.tax_id,B.inventory_acc_code,B.wip_acc_code');
        
        $query = $this->db->get_where('pos_items_detail AS B', array('B.deleted'=>0,'B.company_id'=> $_SESSION['company_id']));
        $data = $query->result_array();
        return $data;
    
    }
    
    public function get_ItemName($item_id)
    {
        $options = array('id'=> $item_id, 'deleted'=>0,'company_id'=> $_SESSION['company_id']);
        $query = $this->db->get_where('pos_items_detail',$options);
        
        if($item_name = $query->row())
        {
            return $item_name->name;
        }
    }
    
    public function get_itemDetail($item_id,$size_id = null)
    {
        if($size_id !== null)
        {
            $this->db->where('id.size_id',$size_id);
        }
        $this->db->where(array('id.item_id'=>$item_id));
        $this->db->join('pos_items_detail id','i.item_id=id.item_id','LEFT');
        $query = $this->db->get('pos_items i');
        $data = $query->result_array();
        return $data;  
    }
    
    public function getSelected_items($item_id)
    {
        $this->db->select("A.unit_price,A.cost_price,A.item_type,A.tax_id,T.name AS tax,T.rate AS tax_rate");

        $this->db->join('pos_taxes AS T','T.id = A.tax_id','left');
        $query = $this->db->get_where('pos_items_detail A',array('A.id'=>$item_id));
        
        $data = $query->result_array();
        return $data;
    }
    
    //get product by search 
    public function search($search){
        
        $this->db->like('name',$search);
        $options = array('deleted'=>0,'company_id'=> $_SESSION['company_id']);
        $query = $this->db->get_where('pos_items_detail',$options);
        $data = $query->result_array();
        return $data;
    }
    
    function addItems()
        {
            $this->db->trans_start(); 
            
            $desc = $this->input->post('description',true);
            $name = $this->input->post('name',true);
            $category_id = $this->input->post('category_id',true);
            $unit_id = ($this->input->post('unit_id',true) == '' ? 0 : $this->input->post('unit_id',true));
            $tax_id = $this->input->post('tax_id',true);
            $item_type = $this->input->post('item_type',true);
            $location_code = $this->input->post('location_code',true);
            $reorder_level=$this->input->post('reorder_level',true);
            $inventory_acc_code = $this->input->post('inventory_acc_code',true);
            $wip_acc_code = ($this->input->post('wip_acc_code',true) == '' ? 0 : $this->input->post('wip_acc_code',true));
            $initial_qty_single = ($this->input->post('initial_qty_single',true) == '' ? 0 : $this->input->post('initial_qty_single',true));
            $cost_price = ($this->input->post('cost_price',true) == '' ? 0 : $this->input->post('cost_price',true));
            $unit_price = ($this->input->post('unit_price',true) == '' ? 0 : $this->input->post('unit_price',true));
            $date = date("Y-m-d H:m:i");
            $barcode = ($this->input->post('barcode',true));
            $item_code= ($this->input->post('item_code',true));
           
            $option_data = array(
                'name'=>$name,
                'barcode'=>$barcode,
                'category_id'=>$category_id,
                'item_code'=>$item_code,
                'item_type'=>$item_type,
                'quantity'=>$initial_qty_single,
                'avg_cost'=>$cost_price,
                'cost_price' =>$cost_price, //actually this price is NEW cost price
                'unit_price' =>$unit_price,
                'tax_id' => $tax_id,
                're_stock_level' => $reorder_level,
                'unit_id' =>$unit_id,
                'location_code' =>$location_code,
                "picture" =>$_FILES['upload_pic']['name'],
                'inventory_acc_code'=>$inventory_acc_code,
                'wip_acc_code'=>$wip_acc_code,
                'description'=>$desc,
                'company_id'=>$_SESSION['company_id'],
                'user_id'=>$_SESSION['user_id'],
                'date_created'=>$date,
                );

                $query_item_insert = $this->db->insert('pos_items_detail', $option_data);
                
                $new_item_id = $this->db->insert_id();
                
                $total_cost = $cost_price*$initial_qty_single;
                
                //ADD ITEM DETAIL IN INVENTORY TABLE    
                  $data1= array(
                    'trans_item'=>$new_item_id,
                    'trans_date'=>$date,
                    'trans_comment'=>'Initial Quantity',
                    'trans_inventory' => $initial_qty_single,
                    'company_id'=> $_SESSION['company_id'],
                    'trans_user'=>$_SESSION['user_id'],
                    'cost_price'=>$cost_price,
                    'unit_price'=>$unit_price,
                    );
                    
                  $this->db->insert('pos_inventory', $data1);
                  //////////////
      
                //for logging
                $msg = $this->input->post('name'). ' Qty('.$initial_qty_single.')';
                $this->M_logs->add_log($msg,"Product","Added","POS");
                // end logging
            
           if(isset($inventory_acc_code))
           {
               $inventory_account = $this->M_groups->get_groups($inventory_acc_code,$_SESSION['company_id']);
               $inventory_dr_balance = abs($inventory_account[0]['op_balance_dr']);
               $dr_balance = ($inventory_dr_balance+$total_cost);
               $cr_balance = 0;
               $this->M_groups->editGroupOPBalance($inventory_acc_code,$dr_balance,$cr_balance);
                     
           }
           
           $this->db->trans_complete();
           return $query_item_insert;
        }
        
     function updateItems($new_picture = null)
     {
            
        $desc = $this->input->post('description',true);
        $name = $this->input->post('name',true);
        $category_id = $this->input->post('category_id',true);
        $unit_id = ($this->input->post('unit_id',true) == '' ? 0 : $this->input->post('unit_id',true));
        $tax_id = $this->input->post('tax_id',true);
        $item_type = $this->input->post('item_type',true);
        $location_code = $this->input->post('location_code',true);
        $reorder_level=$this->input->post('reorder_level',true);
        //$inventory_acc_code = $this->input->post('inventory_acc_code',true);
        //$wip_acc_code = ($this->input->post('wip_acc_code',true) == '' ? 0 : $this->input->post('wip_acc_code',true));
        //$initial_qty_single = ($this->input->post('initial_qty_single',true) == '' ? 0 : $this->input->post('initial_qty_single',true));
        $cost_price = ($this->input->post('cost_price',true) == '' ? 0 : $this->input->post('cost_price',true));
        $unit_price = ($this->input->post('unit_price',true) == '' ? 0 : $this->input->post('unit_price',true));
        $date = date("Y-m-d H:m:i");
        $barcode = ($this->input->post('barcode',true));
        $item_code= ($this->input->post('item_code',true));

        $option_data = array(
        'name'=>$name,
        'barcode'=>$barcode,
        'category_id'=>$category_id,
        'item_code'=>$item_code,
        'item_type'=>$item_type,
        //'quantity'=>$initial_qty_single,
        'avg_cost'=>$cost_price,
        'cost_price' =>$cost_price, //actually this price is NEW cost price
        'unit_price' =>$unit_price,
        'tax_id' => $tax_id,
        're_stock_level' => $reorder_level,
        'unit_id' =>$unit_id,
        'location_code' =>$location_code,
        "picture" =>$_FILES['upload_pic']['name'],
        // 'inventory_acc_code'=>$inventory_acc_code,
        //'wip_acc_code'=>$wip_acc_code,
        'user_id'=>$_SESSION['user_id'],
        'description'=>$desc,
        'date_updated'=>$date,
        "picture" =>$new_picture,
        );

        $this->db->update('pos_items_detail',$option_data,array('id'=>$this->input->post('id')));
        
            //for logging
            $msg = $this->input->post('name'). ' ID('.$this->input->post('id').')';
            $this->M_logs->add_log($msg,"Product","Updated","POS");
            // end logging
            
     }
     
    function deleteItem($id,$inventory_acc_code,$total_cost,$size_id)
    {
        //$query = $this->db->update('pos_items',array('deleted'=>1),array('id'=>$id));
        $query = $this->db->update('pos_items_detail',array('deleted'=>1,'barcode'=>$id),array('id'=>$id));
        
       $inventory_account = $this->M_groups->get_groups($inventory_acc_code,$_SESSION['company_id']);
       $inventory_dr_balance = abs(@$inventory_account[0]['op_balance_dr']);
       $dr_balance = ($inventory_dr_balance-$total_cost);
       $cr_balance = 0;
       $this->M_groups->editGroupOPBalance($inventory_acc_code,$dr_balance,$cr_balance);
                       
                    //for logging
                    $msg = ' Product id('. $id.')';
                    $this->M_logs->add_log($msg,"Product","inactivated","POS");
                    // end logging
    }
    
    //it will reassign products to a category.
    function reasign_items()
    {
        $data = array('category_id'=>$this->input->post('categories'));
        $id_list = implode(',',array_keys($this->session->userdata('orphan')));
        
        $where = 'item_id in ('.$id_list.')';
        
        return $this->db->update('pos_items',$data,$where);
    }
    
   
    //get the total qty of all the colors and sizes of single item.
    //if you want to get total stock only against item then give only item id and leave color and size id.
    public function total_stock($item_id =0)
    {
        $this->db->select_sum('quantity');
        
        $query = $this->db->get_where('pos_items_detail',array('id'=>$item_id));
        if($total = $query->row())
        {
            return $total->quantity;
        }

        return 0;
        
    }
     //check the item option i.e item_id, color and size if exist
    public function is_item_exist($item_id = 0)
    {
        $query = $this->db->get_where('pos_items_detail',array('id'=>$item_id));
        
        if($query->num_rows() > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
            
    }
    
    //get avg cost of the product using below formula
    function getAvgCost($item_id,$new_costPrice,$new_qty,$color_id=0, $size_id=0,$type='receive')
    {
        //get old item cost price and qty.
        $options = array('id'=> $item_id,'color_id'=>$color_id,'size_id'=>$size_id);
        
        $query = $this->db->get_where('pos_items_detail',$options);
        if($row = $query->row())
        {
            $oldCost_price = $row->avg_cost;
            //$old_qty = $this->total_stock($item_id,$color_id, $size_id);
            $old_qty = $row->quantity;
        }
        else
        {  
            //if old cost and qty is no available then set value to zero
            $oldCost_price = 0;
            $old_qty = 0;
            //////
        }   
        
        //IF SERVICE THEN RETUEN ZERO
        $options = array('id'=> $item_id);
        $this->db->select('service');
        $query = $this->db->get_where('pos_items',$options);
        $row = $query->row();
        if($row->service)
        {
            return 0;
        }
        ////////////
        
            $old_cost = ($oldCost_price*$old_qty);
            
            $new_cost = ($new_costPrice*$new_qty);
            
            if($type == 'return'){
                
                if($old_cost != $new_cost)
                {
                    $total_cost = ($old_cost-$new_cost);
                }else{
                    $total_cost = $old_cost;
                }
                
                $total_qty = (($old_qty-$new_qty) == 0 ? 1 : ($old_qty-$new_qty));
               
            }
            elseif($type == 'receive')
            {
                $total_cost = ($old_cost+$new_cost);
                $total_qty = ($old_qty+$new_qty);
            }        
            
            
            $avg_cost = ($total_cost/$total_qty);
            
            return ($avg_cost); //get absolute number i.e if negative value it will convert it to positive
        
    }
    
    function get_item_history($item_id)
    {
        $options = array('trans_item'=> $item_id,'company_id'=> $_SESSION['company_id']);
        
        $query = $this->db->get_where('pos_inventory',$options);
        $data = $query->result_array();
        return $data;
    }
    
    
    function get_assignedColor($id)
    {
        $data = array();
        $this->db->select('colors_id');
        $query = $this->db->get_where('pos_item_colors',array('id'=>$id));
        
        foreach($query->result_array() as $values)
        {
            $data[] = $values['colors_id'];
        }        
        return $data;
    }
    
    function get_assignedSize($id)
    {
        $data= array();
        $this->db->select('size_id');
        $query = $this->db->get_where('pos_item_sizes',array('id'=>$id));
        
        foreach($query->result_array() as $values)
        {
            $data[] = $values['size_id'];
        }        
        return $data;
    }
    
    function barcode_exist($barcode)
     {
        $this->db->where('barcode',$barcode);
        $query = $this->db->get('pos_items_detail'); 
        
        if($query->num_rows() > 0)
        {
            return true;
        }else
        {
            return false;
        }
     }
     
}


?>