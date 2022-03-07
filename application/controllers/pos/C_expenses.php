<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_expenses extends MY_Controller{
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function index()
    {
        //$this->output->enable_profiler();
        
        $data['title'] = 'Expenses';
        $data['main'] = 'Expenses';
        
        $data['cash_account'] = $this->M_groups->get_ledgersByGroupName('current_assets');
      
        $this->load->view('templates/header',$data);
        $this->load->view('pos/expenses/v_expenses',$data);
        $this->load->view('templates/footer');
    }
    
    public function get_allExpenses()
    {
        $data['expenses'] = $this->M_groups->get_ledgersByGroupName('operative_expenses');
        
        echo json_encode($data['expenses']);
    }
    
    public function saveExpenses()
    {
        // get posted data from angularjs purchases 
        $data_posted = json_decode(file_get_contents("php://input",true)); 
        //extract JSON array items from posted data.
        if(count($data_posted) > 0)
        {
            $cr_ledger = $data_posted->cash_account;
        
            foreach($data_posted->items as $posted_values):
            
               $dr = $posted_values->amount;
               $cr = $posted_values->amount;
               $narration = $posted_values->name . ' Paid';
               
               $dr_ledger = $posted_values->id;
               
                
               $this->M_entries->addEntries($dr_ledger,$cr_ledger,$dr,$cr,$narration);
                
            endforeach;
        }
        else
        {
            echo 'No Data';
        }
        
    }
}