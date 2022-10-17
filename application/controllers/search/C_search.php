<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class C_search extends MY_Controller{
    
    function __construct()
    {
        parent::__construct();
        $this->lang->load('index');
    } 

    function index()
    {
        $data = array('langs' => $this->session->userdata('lang'));
        
        ini_set('max_execution_time', 0); 
        ini_set('memory_limit','10240M');

        $data['title'] = lang('search');
        $data['main'] = lang('search');
        
        $keyword = $this->input->get('keyword');
        
        $data['search_result']= $this->M_dashboard->main_search($keyword);
        
        $this->load->view('templates/header',$data);
        $this->load->view('search/v_main_search',$data);
        $this->load->view('templates/footer');
    }
    
   
    
}