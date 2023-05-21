<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_newStore extends CI_Controller{
    
    function __construct()
    {
        parent::__construct();
       
    } 
    
    public function checkUsername(){
        
        $username = $this->input->post('u_name');
        $this->M_companies->checkUsername($username);
        if($this->M_companies->checkUsername($username))
        {
            echo 'true';
        }
        else
        {
            echo 'false';
        }
    }

    function create()
    {
        
        $username = $this->input->post('u_name');
        
        //check the username first if available
        if($this->M_companies->checkUsername($username))
        {
            $this->session->set_flashdata('error',"Username $username Taken");
            redirect('C_login','refresh');
        }
        else
        {
         
            if($username && $this->input->post('password'))
            {
                // $expire=time()+60*60*24*14; // 14 days
                $expire=0; // 0 mean unlimited
        
                $data = array(
                'name' => $this->input->post('name', true),
                'email' => $this->input->post('email', true),
                'address' => $this->input->post('address', true),
                'contact_no' => $this->input->post('contact_no', true),
                'status' => 'active',
                'currency_id' => $this->input->post('currency_id', true),
                'is_multi_currency' => 0,//($this->input->post('is_multi_currency',true) == null ? 0 : $this->input->post('is_multi_currency', true)),
                
                'time_zone' => $this->input->post('time_zone', true),
                'expire' => $expire,
                'locked' => 1
            
                );
                $this->db->insert('companies', $data);
                
                $company_id = $this->db->insert_id();
                
                $sql = "INSERT INTO acc_groups (parent_code, account_type_id, account_code, title, title_ur, name,
                 type, level, company_id, date_created) VALUES                  
                 (0, 1, '1', 'Assets', 'Varlıklar', 'assets', 'group', '1', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                 (0, 3, '2', 'Liabilities', 'yükümlülükler', 'Liabilities', 'group', '1', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (0, 2, '3', 'Equity', 'Eşitlik', 'equity', 'group', '1', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (0, 5, '4', 'Revenue', 'Hasılat', 'revenue', 'group', '1', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (0, 6, '5', 'Cost of Goods Sold', 'Satılan malın maliyeti', 'cos', 'group', '1', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (0, 4, '6', 'Expenses', 'Masraflar', 'expenses', 'group', '1', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (1, 1, '10', 'Current Assets', 'Dönen Varlıklar', 'current_assets', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (1, 1, '12', 'Fixed Assets', 'Sabit kıymetler', 'fixed_assets', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (1, 1, '13', 'Other Assets', 'Diğer varlıklar', 'other_assets', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (2, 3, '20', 'Current Liabilities', 'Cari Yükümlülükler', 'current_liabilities', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (2, 3, '21', 'Long Term Liabilities', 'Uzun vadeli yükümlülükler', 'long_term_liabilities', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (3, 2, '30', 'Equity Account', 'öz sermaye hesabı', 'equity_account', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (4, 5, '40', 'Revenue Account', 'Gelir hesabı', 'revenue_account', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (5, 6, '50', 'Cost of goods sold', 'Satılan malın maliyeti', 'cogs', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (6, 4, '60', 'Operative Expenses', 'Ameliyat Giderleri', 'operative_expenses', 'group', '2', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (10, 1, '1001', 'Cash on Hand', 'Eldeki nakit', 'cash_hand', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (10, 1, '1002', 'Cash at Bank', 'Bankadaki nakit', 'cashatbank', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (10, 1, '1003', 'Accounts Receivable', 'Alacak hesapları', 'accounts_receivable', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (10, 1, '1004', 'Inventory', 'Envanter', 'inventory', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (20, 3, '2000', 'Accounts Payable', 'Ödenebilir hesaplar', 'accounts_payable', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (20, 3, '2001', 'Sales Tax Payable', 'Ödenecek satış vergisi', 'sales_tax', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (30, 2, '3000', 'Capital', 'Başkent', 'capital', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (30, 2, '3001', 'Retained Earnings', 'Dağıtılmamış kârlar', 'retained_earnings', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (40, 5, '4000', 'Sales', 'Satış', 'sales', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (40, 5, '4001', 'Sales Returns and Allowances', 'Satış İadeleri ve Karşılıklar', 'sales_allowances', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (40, 5, '4002', 'Sales Discounts', 'Satış indirimi', 'sales_discounts', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (40, 5, '4003', 'Forex Gain Account', 'Forex Kazanç Hesabı', 'forex_gain_acc_code', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (50, 6, '5000', 'Cost of Sales', 'Satışların maliyeti', 'cost_sales', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (50, 6, '5001', 'Purchases', 'Satışların maliyeti', 'purchases', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (50, 6, '5002', 'Purchase Returns and Allowances', 'Satın Alma İadeleri ve Ödenekler', 'purchase_allowances', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (50, 6, '5003', 'Purchase Discounts', 'Satın Alma İndirimleri', 'purchase_discounts', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (60, 4, '6000', 'Office Expense', 'Ofis harcamaları', 'office_expense', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (60, 4, '6001', 'Salaries Expense', 'Maaş Gideri', 'salaries_expense', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."'),
                (60, 4, '6002', 'Forex Loss Account', 'Forex Zarar Hesabı', 'forex_loss_acc_code', 'detail', '3', ".$company_id.", '".date('Y-m-d H:m:i')."')";

                $this->db->query($sql); //INSERT ALL NECESSARY ACCOUNTS HEADS
                
                
                /////////////////////
                /// USER AND MODULE INFO CREATED HERE
                $data = array(  
                        'name'=>$this->input->post('name',true),
                        'password'=>md5($this->input->post('password',true)),
                        'status'=>1,
                        'role'=>'admin', //$this->input->post('role',true),
                        'company_id'=> $company_id,
                        'username'=>$username,
                        'email' => $this->input->post('email', true),
                      );
                  
                $this->db->insert('users',$data);
                $user_id = $this->db->insert_id();
        
                $modules = $this->M_modules->get_modulesByParent();
                foreach($modules as $i =>$values)
                        {
                            $data1 = array(
                            'emp_id' => $user_id,
                            'module_id' => $values['id'],
                          );
                            $this->db->insert('pos_emp_modules', $data1);
                            
                            //SUB MODULES 
                            $sub_modules = $this->M_modules->get_modulesByParent($values['id']);
                            
                            foreach($sub_modules as  $i => $sub_module)
                            {
                                $data2 = array(
                                //'module_id' => $values,
                                'sub_module' => $sub_module['id'],
                                'emp_id' => $user_id,
                                //'can_create'=>(!isset($can_create[$i]) ? 0 : $can_create[$i]),
    //                            'can_update'=>(!isset($can_update[$i]) ? 0 : $can_update[$i]),
    //                            'can_delete'=>(!isset($can_delete[$i]) ? 0 : $can_delete[$i])
                                );
                                $this->db->insert('pos_emp_modules', $data2);
                                                            
                            }
                            //END SUB MODULES 
                        }
                        
                        
                /////////////
                
                $data = array(
                'company_id'=> $company_id,
                'fy_start_date' => $this->input->post('fy_start_date',true),
                'fy_end_date' => $this->input->post('fy_end_date',true),
                'fy_year' => $this->input->post('fy_year',true),
                'status' => 'active',
                );
                
                $this->db->insert('acc_fiscal_years', $data);
                
                
                                
                $this->session->set_flashdata('message','Your store has created successfully now you can start your business');
                redirect('C_login','refresh');
               
            }
            else
            {
                $this->session->set_flashdata('error','Your store has not created please enter correct information and try again');
                redirect('C_login','refresh');
            }
        
        }
    }
}