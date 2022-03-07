<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_reports extends MY_Controller{
    
    public function __construct()
    {
        parent::__construct();
        
    }
    
    public function trial_balance()
    {
         
        $data['title'] = 'Trial Balance';
        $data['main'] = 'Trial Balance';
        
        //$data['LedgerDDL'] = $this->M_ledgers->getLedgerDropDown();
        //$data['ledgers']= $this->M_ledgers->get_ledgers();
        $data['trialBalance']= $this->M_reports->get_trialBalance($_SESSION['company_id'],FY_START_DATE,FY_END_DATE);
        
                    //for logging
                    $msg = 'Trial Balance';
                    $this->M_logs->add_log($msg,"","retrieved","Reports");
                    // end logging
                    
        $this->load->view('templates/header',$data);
        $this->load->view('accounts/reports/trial_balance',$data);
        $this->load->view('templates/footer');
        
    }
    
    public function pl()
    {
        //$this->output->enable_profiler(TRUE);
        $data['title'] = 'Profit &amp; Loss';
        $data['main'] = 'Profit &amp; Loss';
        
        //$data['LedgerDDL'] = $this->M_ledgers->getLedgerDropDown();
        //$data['parent_groups']= $this->M_groups->get_parentGroups4PL($_SESSION['company_id']);
        
        //$data['proft_loss']= $this->M_reports->get_proft_loss($_SESSION['company_id']);
                    //for logging
                    $msg = 'Profit & Loss';
                    $this->M_logs->add_log($msg,"","retrieved","Reports");
                    // end logging
                    
        $this->load->view('templates/header',$data);
        $this->load->view('accounts/reports/pl-2',$data);
        $this->load->view('templates/footer');
        
    }
    
    public function balanceSheet()
    {
        //$this->output->enable_profiler(TRUE);
        $data['title'] = 'Balance &amp; Sheet';
        $data['main'] = 'Balance &amp; Sheet';
    
        $data['net_income']= $this->M_reports->get_net_income();
        
                    //for logging
                    $msg = 'Balance Sheet';
                    $this->M_logs->add_log($msg,"","retrieved","Reports");
                    // end logging
                    
        $this->load->view('templates/header',$data);
        $this->load->view('accounts/reports/balance_sheet',$data);
        $this->load->view('templates/footer');
        
    }
}