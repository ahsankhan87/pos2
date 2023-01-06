<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_accountPayable extends MY_Controller{
    
    public function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    }
    
    public function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        ini_set('max_execution_time', 0); 
        ini_set('memory_limit','10240M');

        $data['title'] = lang('account_payable');
        $data['main'] = lang('account_payable');
        
        $data['from_date'] = ($this->input->post('from_date') ? $this->input->post('from_date') : FY_START_DATE);
        $data['to_date'] = ($this->input->post('to_date') ? $this->input->post('to_date') : FY_END_DATE);
        
        $data['main_small'] = '<br />'.date('d-m-Y',strtotime($data['from_date'])).' To '.date('d-m-Y',strtotime($data['to_date']));
        $data['suppliers']= $this->M_suppliers->get_suppliers();
        
        //if($data['from_date'] && $data['to_date'])
        //{
        //   $data['account_payable']= $this->M_groups->get_detail_accounts(FALSE,$_SESSION['company_id']);
          //$data['trialBalance']= $this->M_reports->get_account_payable($_SESSION['company_id'],$data['from_date'],$data['to_date']);
       // }
        
        //for logging
                    $msg = '';
                    $this->M_logs->add_log($msg,"Account Payable Report","Retrieved","Accounts");
                    // end logging
                    
        $this->load->view('templates/header',$data);
        $this->load->view('reports/v_account_payable',$data);
        $this->load->view('templates/footer');
        
    }
}