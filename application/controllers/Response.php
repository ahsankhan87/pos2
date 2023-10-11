<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Success extends CI_Controller{
    
    function __construct()
    {
        parent::__construct();
       
    } 
    function index()
    {
        $data['title'] = 'Success';
        $data['main'] = 'Success';
        
        // $this->load->view('templates/header',$data);
        $this->load->view('success',$data);
        // $this->load->view('templates/footer');
    }

    function cancel()
    {
        $data['title'] = 'Cancel';
        $data['main'] = 'Cancel';
        
        // $this->load->view('templates/header',$data);
        $this->load->view('cancel',$data);
        // $this->load->view('templates/footer');
    }
}